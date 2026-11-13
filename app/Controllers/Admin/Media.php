<?php

namespace App\Controllers\Admin;

use App\Models\MediaModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Exceptions\PageNotFoundException;
use finfo;
use InvalidArgumentException;
use Throwable;

class Media extends CmsController
{
    private const MAX_FILE_SIZE = 5242880;

    public function index(): string
    {
        $model = new MediaModel();
        $records = $model->orderBy('created_at', 'DESC')->paginate(24, 'cmsMedia');

        return $this->adminView('admin/media/index', 'Media library', [
            'records' => $records,
            'pager'   => $model->pager,
        ]);
    }

    public function upload(): \CodeIgniter\HTTP\RedirectResponse
    {
        $file = $this->request->getFile('image');
        $altText = trim($this->postString('alt_text'));
        $errors = [];
        $imageInfo = null;

        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Choose a valid image file to upload.';
        } elseif (in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            $errors[] = 'PHP rejected the upload because its configured limit was exceeded. Configure the server to allow files up to 5 MB.';
        } elseif (! $file->isValid()) {
            $errors[] = 'Choose a valid image file to upload.';
        } elseif ($file->getSize() > self::MAX_FILE_SIZE) {
            $errors[] = 'Images must be 5 MB or smaller.';
        } else {
            $imageInfo = $this->validateImage($file);
            if ($imageInfo === null) {
                $errors[] = 'Upload a valid JPEG, PNG or WebP image.';
            }
        }

        if (strlen($altText) > 255) {
            $errors[] = 'Alternative text must be 255 characters or fewer.';
        }

        if ($errors !== []) {
            return redirect()->route('admin.cms.media')->with('error', implode(' ', $errors));
        }

        if ($imageInfo === null) {
            throw new \RuntimeException('The uploaded image could not be validated.');
        }

        $directory = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'media';
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new \RuntimeException('The media upload directory could not be created.');
        }
        if (! is_writable($directory)) {
            throw new \RuntimeException('The media upload directory is not writable.');
        }

        $storedName = bin2hex(random_bytes(16)) . '.' . $imageInfo['extension'];
        $filePath = 'uploads/media/' . $storedName;
        $originalName = $this->safeOriginalName($file->getClientName());
        $fileSize = (int) $file->getSize();

        $file->move($directory, $storedName);
        $movedPath = $directory . DIRECTORY_SEPARATOR . $storedName;

        try {
            $this->saveCmsRecord(
                new MediaModel(),
                [
                    'original_name' => $originalName,
                    'stored_name'   => $storedName,
                    'file_path'     => $filePath,
                    'mime_type'     => $imageInfo['mime'],
                    'file_size'     => $fileSize,
                    'alt_text'      => $altText !== '' ? $altText : null,
                ],
                null,
                'cms.media_uploaded',
                'media',
                'mime=' . $imageInfo['mime'],
            );
        } catch (Throwable $exception) {
            if (is_file($movedPath) && ! unlink($movedPath)) {
                log_message('error', 'Unable to remove a media file after its database insert failed: {path}', [
                    'path' => $movedPath,
                ]);
            }

            throw $exception;
        }

        return redirect()->route('admin.cms.media')->with('message', 'Image uploaded to the media library.');
    }

    public function updateAltText(string $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $mediaId = filter_var($id, FILTER_VALIDATE_INT);
        $model = new MediaModel();
        $record = $mediaId === false ? null : $model->find($mediaId);

        if ($record === null) {
            throw PageNotFoundException::forPageNotFound('Media item not found.');
        }

        $altText = trim($this->postString('alt_text'));
        if (strlen($altText) > 255) {
            return redirect()->route('admin.cms.media')->with('error', 'Alternative text must be 255 characters or fewer.');
        }

        $this->saveCmsRecord(
            $model,
            ['alt_text' => $altText !== '' ? $altText : null],
            (int) $mediaId,
            'cms.media_alt_updated',
            'media',
        );

        return redirect()->route('admin.cms.media')->with('message', 'Alternative text updated.');
    }

    public function delete(string $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $mediaId = filter_var($id, FILTER_VALIDATE_INT);
        $model = new MediaModel();
        $record = $mediaId === false ? null : $model->find($mediaId);

        if ($record === null) {
            throw PageNotFoundException::forPageNotFound('Media item not found.');
        }

        if ($this->isReferenced((string) $record['file_path'])) {
            return redirect()->route('admin.cms.media')->with(
                'error',
                'This image is referenced by website content and cannot be deleted.',
            );
        }

        $path = (string) $record['file_path'];
        if (
            preg_match('#\Auploads/media/[a-f0-9]{32}\.(?:jpg|png|webp)\z#i', $path) !== 1
        ) {
            throw new InvalidArgumentException('The media path is not a managed upload.');
        }

        $absolutePath = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $path);
        $this->deleteCmsRecord($model, (int) $mediaId, 'cms.media_deleted', 'media');

        if (is_file($absolutePath) && ! unlink($absolutePath)) {
            log_message('error', 'Media row {id} was removed but its file could not be deleted: {path}', [
                'id'   => $mediaId,
                'path' => $absolutePath,
            ]);

            return redirect()->route('admin.cms.media')->with(
                'error',
                'The media record was removed, but the file could not be deleted. Contact an administrator.',
            );
        }

        return redirect()->route('admin.cms.media')->with('message', 'Image deleted.');
    }

    /**
     * @return array{mime: string, extension: string}|null
     */
    private function validateImage(UploadedFile $file): ?array
    {
        $extension = strtolower(pathinfo($file->getClientName(), PATHINFO_EXTENSION));
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file->getTempName());
        $allowed = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'webp' => 'image/webp',
        ];

        if (
            ! is_string($mime)
            || ! isset($allowed[$extension])
            || $allowed[$extension] !== $mime
        ) {
            return null;
        }

        $dimensions = getimagesize($file->getTempName());
        if (
            $dimensions === false
            || $dimensions[0] < 1
            || $dimensions[1] < 1
            || $dimensions[0] > 6000
            || $dimensions[1] > 6000
            || $dimensions[0] * $dimensions[1] > 20000000
        ) {
            return null;
        }

        return [
            'mime'      => $mime,
            'extension' => $mime === 'image/jpeg' ? 'jpg' : $extension,
        ];
    }

    private function safeOriginalName(string $name): string
    {
        $basename = basename(str_replace('\\', '/', $name));
        $safe = preg_replace('/[^A-Za-z0-9._ -]/', '_', $basename) ?? 'image';

        return substr($safe, 0, 255);
    }

    private function isReferenced(string $path): bool
    {
        foreach (['courses', 'posts', 'announcements', 'pages'] as $table) {
            if (db_connect(config('Auth')->DBGroup)
                ->table($table)
                ->where('featured_image', $path)
                ->countAllResults() > 0
            ) {
                return true;
            }
        }

        return false;
    }
}

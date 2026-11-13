<?php

namespace App\Controllers\Admin;

use App\Models\AdminAuditLogModel;
use App\Models\MediaModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Model;
use DateTimeImmutable;
use InvalidArgumentException;
use Throwable;

abstract class CmsController extends AdminBaseController
{
    /**
     * @param array<string, mixed> $data
     */
    protected function adminView(string $view, string $title, array $data = []): string
    {
        return view($view, array_merge($data, [
            'pageTitle' => $title . ' | Membley Administration',
            'message'   => session('message'),
            'error'     => session('error'),
        ]));
    }

    protected function queryString(string $key): string
    {
        $value = $this->request->getGet($key);

        return is_string($value) ? trim($value) : '';
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function mediaOptions(): array
    {
        return (new MediaModel())->orderBy('created_at', 'DESC')->findAll();
    }

    protected function selectedMediaPath(string $id): ?string
    {
        if ($id === '' || $id === '0') {
            return null;
        }

        if (! ctype_digit($id)) {
            throw new InvalidArgumentException('Choose an image from the media library.');
        }

        $media = (new MediaModel())->find((int) $id);

        if ($media === null) {
            throw new InvalidArgumentException('The selected media item no longer exists.');
        }

        $path = (string) $media['file_path'];

        if (
            preg_match(
                '#\Auploads/media/[a-f0-9]{32}\.(?:jpg|png|webp)\z#i',
                $path,
            ) !== 1
            || ! is_file(FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $path))
        ) {
            throw new InvalidArgumentException('The selected media file is unavailable.');
        }

        return $path;
    }

    protected function normalizeSlug(string $slug, string $title): string
    {
        helper('text');
        $value = trim($slug) !== '' ? trim($slug) : trim($title);
        $normalized = strtolower(trim(url_title($value, '-', true), '-'));

        if (
            $normalized === ''
            || strlen($normalized) > 240
            || preg_match('/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', $normalized) !== 1
        ) {
            throw new InvalidArgumentException('Enter a valid URL slug using letters, numbers and hyphens.');
        }

        return $normalized;
    }

    protected function slugAvailable(string $table, string $slug, ?int $exceptId = null): bool
    {
        $builder = db_connect(config('Auth')->DBGroup)
            ->table($table)
            ->select('id')
            ->where('slug', $slug);

        if ($exceptId !== null) {
            $builder->where('id !=', $exceptId);
        }

        return $builder->countAllResults() === 0;
    }

    protected function dateTimeValue(string $value): ?string
    {
        if (trim($value) === '') {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $value);
        $errors = DateTimeImmutable::getLastErrors();

        if (
            $date === false
            || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
        ) {
            throw new InvalidArgumentException('Enter a valid publication date and time.');
        }

        return $date->format('Y-m-d H:i:s');
    }

    protected function dateTimeInput(?string $value): string
    {
        return $value === null || $value === ''
            ? ''
            : str_replace(' ', 'T', substr($value, 0, 16));
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function saveCmsRecord(
        Model $model,
        array $data,
        ?int $id,
        string $action,
        string $entityType,
        string $details = '',
    ): int {
        $db = db_connect(config('Auth')->DBGroup);

        if (! $db->transBegin()) {
            throw new \RuntimeException('The content transaction could not be started.');
        }

        try {
            if ($id === null) {
                if ($model->insert($data) === false) {
                    throw new \RuntimeException('The content could not be created.');
                }

                $entityId = (int) $model->getInsertID();
            } else {
                if (! $model->update($id, $data)) {
                    throw new \RuntimeException('The content could not be updated.');
                }

                $entityId = $id;
            }

            if ($entityId < 1 || ! $this->recordCmsAction($action, $entityType, $entityId, $details)) {
                throw new \RuntimeException('The content change could not be audited.');
            }

            $this->commitOrFail($db);
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }

        return $entityId;
    }

    protected function deleteCmsRecord(
        Model $model,
        int $id,
        string $action,
        string $entityType,
    ): void {
        $db = db_connect(config('Auth')->DBGroup);

        if (! $db->transBegin()) {
            throw new \RuntimeException('The content transaction could not be started.');
        }

        try {
            if (! $model->delete($id)) {
                throw new \RuntimeException('The content could not be deleted.');
            }

            if (! $this->recordCmsAction($action, $entityType, $id)) {
                throw new \RuntimeException('The content deletion could not be audited.');
            }

            $this->commitOrFail($db);
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }

    private function recordCmsAction(
        string $action,
        string $entityType,
        int $entityId,
        string $details = '',
    ): bool {
        return (new AdminAuditLogModel())->record(
            (int) auth()->id(),
            $action,
            $entityType,
            $entityId,
            $details,
            $this->request->getIPAddress(),
        );
    }

    private function commitOrFail(BaseConnection $db): void
    {
        if (! $db->transStatus() || ! $db->transCommit()) {
            throw new \RuntimeException('The content change could not be committed.');
        }
    }
}

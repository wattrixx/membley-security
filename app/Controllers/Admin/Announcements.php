<?php

namespace App\Controllers\Admin;

use App\Models\AnnouncementModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use DateTimeImmutable;
use InvalidArgumentException;

class Announcements extends CmsController
{
    private const STATES = ['draft', 'published', 'archived'];

    public function index(): string
    {
        $model = new AnnouncementModel();
        $search = $this->queryString('q');
        $status = $this->queryString('status');
        $builder = $model->builder();

        if ($search !== '') {
            $builder->groupStart()
                ->like('title', $search)
                ->orLike('slug', $search)
                ->groupEnd();
        }
        if (in_array($status, self::STATES, true)) {
            $builder->where('status', $status);
        }

        $records = $model->orderBy('updated_at', 'DESC')->paginate(15, 'cmsAnnouncements');

        return $this->adminView('admin/announcements/index', 'Announcement management', [
            'records' => $records,
            'pager'   => $model->pager,
            'search'  => $search,
            'status'  => $status,
        ]);
    }

    public function createForm(): string
    {
        return $this->formView($this->blankRecord(), []);
    }

    public function create(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $input = $this->input();
        $errors = [];
        $data = $this->contentData($input, null, $errors);

        if ($errors !== []) {
            return $this->formView($input, $errors);
        }

        $this->saveCmsRecord(
            new AnnouncementModel(),
            $data,
            null,
            'cms.announcement_created',
            'announcement',
            'publication=' . $data['status'],
        );

        return redirect()->route('admin.cms.announcements')->with('message', 'Announcement created.');
    }

    public function edit(string $id): string
    {
        $record = (new AnnouncementModel())->find((int) $id);
        if ($record === null) {
            throw PageNotFoundException::forPageNotFound('Announcement not found.');
        }

        $record['featured_media_id'] = $this->mediaIdForPath($record['featured_image'] ?? null);

        return $this->formView($record, []);
    }

    public function update(string $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $recordId = filter_var($id, FILTER_VALIDATE_INT);
        $model = new AnnouncementModel();
        $record = $recordId === false ? null : $model->find($recordId);
        if ($record === null) {
            throw PageNotFoundException::forPageNotFound('Announcement not found.');
        }

        $input = $this->input();
        $errors = [];
        $data = $this->contentData($input, (int) $recordId, $errors);

        if ($errors !== []) {
            $input['id'] = (int) $recordId;

            return $this->formView($input, $errors);
        }

        $this->saveCmsRecord(
            $model,
            $data,
            (int) $recordId,
            'cms.announcement_updated',
            'announcement',
            'publication=' . $data['status'],
        );

        return redirect()->route('admin.cms.announcements')->with('message', 'Announcement updated.');
    }

    public function preview(): string
    {
        $input = $this->input();
        $errors = [];
        $data = $this->contentData($input, null, $errors, false);

        if ($errors !== []) {
            return $this->formView($input, $errors);
        }

        return view('admin/content_preview', [
            'pageTitle'       => 'Announcement preview | Membley Administration',
            'previewTitle'    => $data['title'],
            'previewExcerpt'  => '',
            'previewContent'  => $data['content'],
            'previewImage'    => $data['featured_image'],
            'previewMetadata' => [
                'Status'      => ucfirst($data['status']),
                'Starts'      => $data['starts_at'] ?? 'No start date',
                'Ends'        => $data['ends_at'] ?? 'No end date',
                'Publication' => $data['published_at'],
            ],
        ]);
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, string> $errors
     * @return array<string, mixed>
     */
    private function contentData(
        array $input,
        ?int $exceptId,
        array &$errors,
        bool $checkUnique = true,
    ): array {
        $rules = [
            'title'        => 'required|max_length[220]',
            'slug'         => 'permit_empty|max_length[240]',
            'content'      => 'required|max_length[50000]',
            'status'       => 'required|in_list[' . implode(',', self::STATES) . ']',
            'starts_at'    => 'permit_empty|max_length[16]',
            'ends_at'      => 'permit_empty|max_length[16]',
            'published_at' => 'permit_empty|max_length[16]',
        ];
        if (! $this->validateData($input, $rules)) {
            $errors = $this->validator->getErrors();
        }

        try {
            $slug = $this->normalizeSlug($input['slug'], $input['title']);
            if ($checkUnique && ! $this->slugAvailable('announcements', $slug, $exceptId)) {
                $errors['slug'] = 'This announcement URL slug is already in use.';
            }
        } catch (InvalidArgumentException $exception) {
            $slug = '';
            $errors['slug'] = $exception->getMessage();
        }

        try {
            $imagePath = $this->selectedMediaPath($input['featured_media_id']);
        } catch (InvalidArgumentException $exception) {
            $imagePath = null;
            $errors['featured_media_id'] = $exception->getMessage();
        }

        $dates = [];
        foreach (['starts_at', 'ends_at', 'published_at'] as $field) {
            try {
                $dates[$field] = $this->dateTimeValue($input[$field]);
            } catch (InvalidArgumentException $exception) {
                $dates[$field] = null;
                $errors[$field] = $exception->getMessage();
            }
        }
        if (
            $dates['starts_at'] !== null
            && $dates['ends_at'] !== null
            && DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dates['ends_at'])
                < DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dates['starts_at'])
        ) {
            $errors['ends_at'] = 'The end date must be after the start date.';
        }
        if ($input['status'] === 'published' && $dates['published_at'] === null) {
            $dates['published_at'] = date('Y-m-d H:i:s');
        }

        return [
            'title'         => trim($input['title']),
            'slug'          => $slug,
            'content'       => trim($input['content']),
            'featured_image'=> $imagePath,
            'status'        => $input['status'],
            'starts_at'     => $dates['starts_at'],
            'ends_at'       => $dates['ends_at'],
            'published_at'  => $dates['published_at'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function input(): array
    {
        return [
            'title'             => trim($this->postString('title')),
            'slug'              => trim($this->postString('slug')),
            'content'           => trim($this->postString('content')),
            'featured_media_id' => $this->postString('featured_media_id'),
            'status'            => $this->postString('status'),
            'starts_at'         => $this->postString('starts_at'),
            'ends_at'           => $this->postString('ends_at'),
            'published_at'      => $this->postString('published_at'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function blankRecord(): array
    {
        return [
            'title'             => '',
            'slug'              => '',
            'content'           => '',
            'featured_image'    => null,
            'featured_media_id' => '',
            'status'            => 'draft',
            'starts_at'         => null,
            'ends_at'           => null,
            'published_at'      => null,
            'id'                => null,
        ];
    }

    /**
     * @param array<string, mixed> $record
     * @param array<string, string> $errors
     */
    private function formView(array $record, array $errors): string
    {
        return $this->adminView('admin/announcements/form', 'Announcement editor', [
            'record'    => array_merge($this->blankRecord(), $record),
            'media'     => $this->mediaOptions(),
            'errors'    => $errors,
            'isEditing' => ! empty($record['id']),
        ]);
    }

    private function mediaIdForPath(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        $media = (new \App\Models\MediaModel())
            ->where('file_path', $path)
            ->first();

        return $media === null ? '' : (string) $media['id'];
    }
}

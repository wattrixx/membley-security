<?php

namespace App\Controllers\Admin;

use App\Models\PostModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use InvalidArgumentException;

class News extends CmsController
{
    private const STATES = ['draft', 'published', 'archived'];

    public function index(): string
    {
        $model = new PostModel();
        $search = $this->queryString('q');
        $status = $this->queryString('status');
        $builder = $model->builder();

        if ($search !== '') {
            $builder->groupStart()
                ->like('title', $search)
                ->orLike('slug', $search)
                ->orLike('category', $search)
                ->groupEnd();
        }
        if (in_array($status, self::STATES, true)) {
            $builder->where('status', $status);
        }

        $posts = $model->orderBy('updated_at', 'DESC')->paginate(15, 'cmsNews');

        return $this->adminView('admin/news/index', 'News and blog', [
            'posts'  => $posts,
            'pager'  => $model->pager,
            'search' => $search,
            'status' => $status,
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
        $data = $this->contentData($input, null, null, $errors);

        if ($errors !== []) {
            return $this->formView($input, $errors);
        }

        $this->saveCmsRecord(
            new PostModel(),
            $data,
            null,
            'cms.news_created',
            'post',
            'publication=' . $data['status'],
        );

        return redirect()->route('admin.cms.news')->with('message', 'Article created.');
    }

    public function edit(string $id): string
    {
        $post = (new PostModel())->find((int) $id);

        if ($post === null) {
            throw PageNotFoundException::forPageNotFound('Article not found.');
        }

        $post['featured_media_id'] = $this->mediaIdForPath($post['featured_image'] ?? null);

        return $this->formView($post, []);
    }

    public function update(string $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $postId = filter_var($id, FILTER_VALIDATE_INT);
        $model = new PostModel();
        $post = $postId === false ? null : $model->find($postId);

        if ($post === null) {
            throw PageNotFoundException::forPageNotFound('Article not found.');
        }

        $input = $this->input();
        $errors = [];
        $data = $this->contentData($input, (int) $postId, (string) $post['author_name'], $errors);

        if ($errors !== []) {
            $input['id'] = (int) $postId;

            return $this->formView($input, $errors);
        }

        $this->saveCmsRecord(
            $model,
            $data,
            (int) $postId,
            'cms.news_updated',
            'post',
            'publication=' . $data['status'],
        );

        return redirect()->route('admin.cms.news')->with('message', 'Article updated.');
    }

    public function preview(): string
    {
        $input = $this->input();
        $errors = [];
        $data = $this->contentData($input, null, null, $errors, false);

        if ($errors !== []) {
            return $this->formView($input, $errors);
        }

        return view('admin/content_preview', [
            'pageTitle'       => 'Article preview | Membley Administration',
            'previewTitle'    => $data['title'],
            'previewExcerpt'  => $data['excerpt'],
            'previewContent'  => $data['content'],
            'previewImage'    => $data['featured_image'],
            'previewMetadata' => [
                'Category'   => $data['category'],
                'Author'     => $data['author_name'],
                'Status'     => ucfirst($data['status']),
                'Publication'=> $data['published_at'],
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
        ?string $authorName,
        array &$errors,
        bool $checkUnique = true,
    ): array {
        $rules = [
            'title'             => 'required|max_length[220]',
            'slug'              => 'permit_empty|max_length[240]',
            'category'          => 'permit_empty|max_length[150]',
            'excerpt'           => 'permit_empty|max_length[5000]',
            'content'           => 'required|max_length[50000]',
            'meta_title'        => 'permit_empty|max_length[220]',
            'meta_description'  => 'permit_empty|max_length[300]',
            'status'            => 'required|in_list[' . implode(',', self::STATES) . ']',
            'published_at'      => 'permit_empty|max_length[16]',
        ];
        if (! $this->validateData($input, $rules)) {
            $errors = $this->validator->getErrors();
        }

        try {
            $slug = $this->normalizeSlug($input['slug'], $input['title']);
            if ($checkUnique && ! $this->slugAvailable('posts', $slug, $exceptId)) {
                $errors['slug'] = 'This article URL slug is already in use.';
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

        try {
            $publishedAt = $this->dateTimeValue($input['published_at']);
        } catch (InvalidArgumentException $exception) {
            $publishedAt = null;
            $errors['published_at'] = $exception->getMessage();
        }
        if ($input['status'] === 'published' && $publishedAt === null) {
            $publishedAt = date('Y-m-d H:i:s');
        }

        return [
            'title'            => trim($input['title']),
            'slug'             => $slug,
            'excerpt'          => trim($input['excerpt']),
            'content'          => trim($input['content']),
            'category'         => trim($input['category']),
            'featured_image'   => $imagePath,
            'author_name'      => $authorName ?? $this->currentAuthorName(),
            'status'           => $input['status'],
            'meta_title'       => trim($input['meta_title']),
            'meta_description' => trim($input['meta_description']),
            'published_at'     => $publishedAt,
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
            'category'          => trim($this->postString('category')),
            'excerpt'           => trim($this->postString('excerpt')),
            'content'           => trim($this->postString('content')),
            'featured_media_id' => $this->postString('featured_media_id'),
            'meta_title'        => trim($this->postString('meta_title')),
            'meta_description'  => trim($this->postString('meta_description')),
            'status'            => $this->postString('status'),
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
            'category'          => '',
            'excerpt'           => '',
            'content'           => '',
            'featured_image'    => null,
            'featured_media_id' => '',
            'author_name'       => $this->currentAuthorName(),
            'status'            => 'draft',
            'meta_title'        => '',
            'meta_description'  => '',
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
        return $this->adminView('admin/news/form', 'Article editor', [
            'record'    => array_merge($this->blankRecord(), $record),
            'media'     => $this->mediaOptions(),
            'errors'    => $errors,
            'isEditing' => ! empty($record['id']),
        ]);
    }

    private function currentAuthorName(): string
    {
        $user = auth()->user();

        return $user->username;
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

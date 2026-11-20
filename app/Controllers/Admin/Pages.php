<?php

namespace App\Controllers\Admin;

use App\Content\AboutContent;
use App\Content\TransportContent;
use App\Models\MediaModel;
use App\Models\PageModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use InvalidArgumentException;

class Pages extends CmsController
{
    private const STATES = ['draft', 'published', 'archived'];

    /**
     * Only the existing fixed public routes can be overridden by CMS content.
     *
     * @var array<string, array{title: string, content: string, meta_title: string, meta_description: string}>
     */
    private const MANAGED_PAGES = [
        'about' => [
            'title' => 'About the Group',
            'content' => '',
            'meta_title' => 'About the Group | Membley Security Group',
            'meta_description' => 'Learn how Membley Security Group connects its College and Security Services teams.',
        ],
        'college' => [
            'title' => 'Membley College of Security Studies',
            'content' => 'The College provides training information for people preparing to enter security, serving officers developing their skills and employers training their teams.' . "\n\n"
                . 'The short-course approach connects learning with practical tasks, case discussions and simulated workplace situations.' . "\n\n"
                . 'Course availability, dates and assessment arrangements are confirmed by Admissions for each intake. Application forms are not yet available on this website.',
            'meta_title' => 'Membley College of Security Studies',
            'meta_description' => 'Explore the College training approach, current course listings and admissions contact.',
        ],
        'security-services' => [
            'title' => 'Security Services',
            'content' => 'Membley Investigations and Surveillance Company handles investigation, protection and security-service enquiries.' . "\n\n"
                . 'Service scope, availability, fees and engagement arrangements are confirmed with the company before any work begins.' . "\n\n"
                . 'Enquiry forms are not yet available online. Contact the Security Services team to discuss your request and the appropriate next step.',
            'meta_title' => 'Security Services | Membley Security Group',
            'meta_description' => 'Contact Membley Investigations and Surveillance Company about security service enquiries.',
        ],
    ];

    public function index(): string
    {
        $managedPages = $this->managedPages();
        $pages = (new PageModel())
            ->whereIn('slug', array_keys($managedPages))
            ->findAll();
        $saved = [];
        foreach ($pages as $page) {
            $saved[$page['slug']] = $page;
        }

        $records = [];
        foreach ($managedPages as $slug => $defaults) {
            $records[] = array_merge(
                [
                    'slug'       => $slug,
                    'status'     => 'template',
                    'updated_at' => null,
                    'is_saved'   => false,
                ],
                $defaults,
                $saved[$slug] ?? [],
                ['is_saved' => isset($saved[$slug])],
            );
        }

        return $this->adminView('admin/pages/index', 'Page management', [
            'records' => $records,
        ]);
    }

    public function edit(string $slug): string
    {
        $this->assertManagedSlug($slug);
        $record = (new PageModel())->where('slug', $slug)->first();

        if ($record === null) {
            $record = array_merge($this->managedPages()[$slug], [
                'slug'               => $slug,
                'status'             => 'draft',
                'published_at'       => null,
                'featured_image'     => null,
                'featured_media_id'  => '',
                'id'                 => null,
            ]);
        } else {
            $record['featured_media_id'] = $this->mediaIdForPath($record['featured_image'] ?? null);
        }

        return $this->formView($record, []);
    }

    public function update(string $slug): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $this->assertManagedSlug($slug);
        $input = $this->input();
        $input['slug'] = $slug;
        $existing = (new PageModel())->where('slug', $slug)->first();
        $input['id'] = $existing === null ? null : (int) $existing['id'];
        $errors = [];
        $data = $this->contentData($input, $errors);

        if ($errors !== []) {
            return $this->formView($input, $errors);
        }

        $model = new PageModel();
        $id = $existing === null ? null : (int) $existing['id'];
        $this->saveCmsRecord(
            $model,
            $data,
            $id,
            $id === null ? 'cms.page_created' : 'cms.page_updated',
            'page',
            'slug=' . $slug . ';publication=' . $data['status'],
        );

        return redirect()->route('admin.cms.pages')->with('message', 'Page content saved.');
    }

    public function preview(): string
    {
        $slug = $this->postString('slug');
        $this->assertManagedSlug($slug);
        $input = $this->input();
        $input['slug'] = $slug;
        $errors = [];
        $data = $this->contentData($input, $errors);

        if ($errors !== []) {
            return $this->formView($input, $errors);
        }

        return view('admin/content_preview', [
            'pageTitle'       => 'Page preview | Membley Administration',
            'previewTitle'    => $data['title'],
            'previewExcerpt'  => $data['meta_description'],
            'previewContent'  => $data['content'],
            'previewImage'    => $data['featured_image'],
            'previewMetadata' => [
                'Page path'   => '/' . (TransportContent::PAGES[$slug]['path'] ?? $slug),
                'Status'      => ucfirst($data['status']),
                'Publication' => $data['published_at'],
            ],
        ]);
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, string> $errors
     * @return array<string, mixed>
     */
    private function contentData(array $input, array &$errors): array
    {
        $rules = [
            'title'            => 'required|max_length[220]',
            'content'          => 'required|max_length[50000]',
            'meta_title'       => 'permit_empty|max_length[220]',
            'meta_description' => 'permit_empty|max_length[300]',
            'status'           => 'required|in_list[' . implode(',', self::STATES) . ']',
            'published_at'     => 'permit_empty|max_length[16]',
        ];
        if (! $this->validateData($input, $rules)) {
            $errors = $this->validator->getErrors();
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
            'slug'             => $input['slug'],
            'content'          => trim($input['content']),
            'meta_title'       => trim($input['meta_title']),
            'meta_description' => trim($input['meta_description']),
            'featured_image'   => $imagePath,
            'status'           => $input['status'],
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
            'content'           => trim($this->postString('content')),
            'meta_title'        => trim($this->postString('meta_title')),
            'meta_description'  => trim($this->postString('meta_description')),
            'featured_media_id' => $this->postString('featured_media_id'),
            'status'            => $this->postString('status'),
            'published_at'      => $this->postString('published_at'),
        ];
    }

    /**
     * @param array<string, mixed> $record
     * @param array<string, string> $errors
     */
    private function formView(array $record, array $errors): string
    {
        return $this->adminView('admin/pages/form', 'Page editor', [
            'record'    => $record,
            'media'     => $this->mediaOptions(),
            'errors'    => $errors,
            'isEditing' => isset($record['id']) && $record['id'] !== null,
            'slug'      => $record['slug'],
        ]);
    }

    private function assertManagedSlug(string $slug): void
    {
        if (! array_key_exists($slug, $this->managedPages())) {
            throw PageNotFoundException::forPageNotFound('This system page is not available for CMS editing.');
        }
    }

    private function managedPages(): array
    {
        $pages = self::MANAGED_PAGES;
        $pages['about']['content'] = AboutContent::editorContent();
        foreach (TransportContent::PAGES as $slug => $page) {
            $pages[$slug] = [
                'title' => $page['title'],
                'content' => $page['content'],
                'meta_title' => $page['meta_title'],
                'meta_description' => $page['meta_description'],
            ];
        }

        return $pages;
    }

    private function mediaIdForPath(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        $media = (new MediaModel())->where('file_path', $path)->first();

        return $media === null ? '' : (string) $media['id'];
    }
}

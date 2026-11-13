<?php

namespace App\Controllers\Admin;

use App\Models\CourseModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use InvalidArgumentException;

class Courses extends CmsController
{
    private const PUBLICATION_STATES = ['draft', 'published', 'archived'];
    private const AVAILABILITY_STATES = ['available', 'proposed', 'development'];

    public function index(): string
    {
        $model = new CourseModel();
        $search = $this->queryString('q');
        $publication = $this->queryString('publication');
        $builder = $model->builder();

        if ($search !== '') {
            $builder->groupStart()
                ->like('title', $search)
                ->orLike('slug', $search)
                ->groupEnd();
        }
        if (in_array($publication, self::PUBLICATION_STATES, true)) {
            $builder->where('publication_status', $publication);
        }

        $courses = $model->orderBy('updated_at', 'DESC')->paginate(15, 'cmsCourses');

        return $this->adminView('admin/courses/index', 'Course management', [
            'courses'     => $courses,
            'pager'       => $model->pager,
            'search'      => $search,
            'publication' => $publication,
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
            new CourseModel(),
            $data,
            null,
            'cms.course_created',
            'course',
            'publication=' . $data['publication_status'],
        );

        return redirect()->route('admin.cms.courses')->with('message', 'Course created.');
    }

    public function edit(string $id): string
    {
        $course = (new CourseModel())->find((int) $id);

        if ($course === null) {
            throw PageNotFoundException::forPageNotFound('Course not found.');
        }

        $course['featured_media_id'] = $this->mediaIdForPath($course['featured_image'] ?? null);

        return $this->formView($course, []);
    }

    public function update(string $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $courseId = filter_var($id, FILTER_VALIDATE_INT);
        $model = new CourseModel();
        $course = $courseId === false ? null : $model->find($courseId);

        if ($course === null) {
            throw PageNotFoundException::forPageNotFound('Course not found.');
        }

        $input = $this->input();
        $errors = [];
        $data = $this->contentData($input, (int) $courseId, $errors);

        if ($errors !== []) {
            $input['id'] = (int) $courseId;

            return $this->formView($input, $errors);
        }

        $this->saveCmsRecord(
            $model,
            $data,
            (int) $courseId,
            'cms.course_updated',
            'course',
            'publication=' . $data['publication_status'],
        );

        return redirect()->route('admin.cms.courses')->with('message', 'Course updated.');
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
            'pageTitle'       => 'Course preview | Membley Administration',
            'previewTitle'    => $data['title'],
            'previewExcerpt'  => $data['short_description'],
            'previewContent'  => $data['description'],
            'previewImage'    => $data['featured_image'],
            'previewMetadata' => [
                'Availability' => ucfirst($data['status']),
                'Publication'  => ucfirst($data['publication_status']),
                'Duration'     => $data['duration'],
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
            'title'              => 'required|max_length[200]',
            'slug'               => 'permit_empty|max_length[220]',
            'short_description'  => 'permit_empty|max_length[2000]',
            'description'        => 'required|max_length[50000]',
            'duration'           => 'permit_empty|max_length[100]',
            'fee'                => 'permit_empty|decimal|greater_than_equal_to[0]|max_length[13]',
            'requirements'       => 'permit_empty|max_length[20000]',
            'outcomes'           => 'permit_empty|max_length[20000]',
            'status'             => 'required|in_list[' . implode(',', self::AVAILABILITY_STATES) . ']',
            'publication_status'=> 'required|in_list[' . implode(',', self::PUBLICATION_STATES) . ']',
            'published_at'       => 'permit_empty|max_length[16]',
        ];

        if (! $this->validateData($input, $rules)) {
            $errors = $this->validator->getErrors();
        }
        if (! in_array($input['fee_verified'], ['', '1'], true)) {
            $errors['fee_verified'] = 'Choose a valid fee verification setting.';
        }
        if (! in_array($input['is_featured'], ['', '1'], true)) {
            $errors['is_featured'] = 'Choose a valid featured-course setting.';
        }
        if ($input['fee'] !== '' && (float) $input['fee'] > 9999999999.99) {
            $errors['fee'] = 'The fee exceeds the supported amount.';
        }

        try {
            $slug = $this->normalizeSlug($input['slug'], $input['title']);
            if ($checkUnique && ! $this->slugAvailable('courses', $slug, $exceptId)) {
                $errors['slug'] = 'This course URL slug is already in use.';
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

        $categoryId = null;
        if ($input['category_id'] !== '' && $input['category_id'] !== '0') {
            if (! ctype_digit($input['category_id'])) {
                $errors['category_id'] = 'Choose a valid course category.';
            } else {
                $category = db_connect(config('Auth')->DBGroup)
                    ->table('course_categories')
                    ->where('id', (int) $input['category_id'])
                    ->where('status', 'active')
                    ->get()
                    ->getRowArray();

                if ($category === null) {
                    $errors['category_id'] = 'Choose an active course category.';
                } else {
                    $categoryId = (int) $category['id'];
                }
            }
        }

        $verified = $input['fee_verified'] === '1' && trim($input['fee']) !== '';
        if ($input['fee_verified'] === '1' && trim($input['fee']) === '') {
            $errors['fee'] = 'Enter the approved course fee or leave fee verification unchecked.';
        }

        try {
            $publishedAt = $this->dateTimeValue($input['published_at']);
        } catch (InvalidArgumentException $exception) {
            $publishedAt = null;
            $errors['published_at'] = $exception->getMessage();
        }
        if ($input['publication_status'] === 'published' && $publishedAt === null) {
            $publishedAt = date('Y-m-d H:i:s');
        }

        return [
            'category_id'        => $categoryId,
            'title'              => trim($input['title']),
            'slug'               => $slug,
            'short_description'  => trim($input['short_description']),
            'description'        => trim($input['description']),
            'duration'           => trim($input['duration']),
            'fee'                => $verified ? number_format((float) $input['fee'], 2, '.', '') : null,
            'fee_verified'        => $verified,
            'requirements'       => trim($input['requirements']),
            'outcomes'            => trim($input['outcomes']),
            'featured_image'      => $imagePath,
            'status'              => $input['status'],
            'publication_status'  => $input['publication_status'],
            'is_featured'         => $input['is_featured'] === '1',
            'published_at'        => $publishedAt,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function input(): array
    {
        return [
            'title'               => trim($this->postString('title')),
            'slug'                => trim($this->postString('slug')),
            'short_description'   => trim($this->postString('short_description')),
            'description'         => trim($this->postString('description')),
            'duration'            => trim($this->postString('duration')),
            'fee'                 => trim($this->postString('fee')),
            'fee_verified'        => $this->postString('fee_verified'),
            'requirements'        => trim($this->postString('requirements')),
            'outcomes'            => trim($this->postString('outcomes')),
            'category_id'         => $this->postString('category_id'),
            'featured_media_id'   => $this->postString('featured_media_id'),
            'status'              => $this->postString('status'),
            'publication_status'  => $this->postString('publication_status'),
            'is_featured'         => $this->postString('is_featured'),
            'published_at'        => $this->postString('published_at'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function blankRecord(): array
    {
        return array_merge($this->inputDefaults(), ['id' => null]);
    }

    /**
     * @return array<string, mixed>
     */
    private function inputDefaults(): array
    {
        return [
            'title'              => '',
            'slug'               => '',
            'short_description'  => '',
            'description'        => '',
            'duration'           => '',
            'fee'                => '',
            'fee_verified'       => false,
            'requirements'       => '',
            'outcomes'           => '',
            'category_id'        => null,
            'featured_image'     => null,
            'featured_media_id'  => '',
            'status'             => 'development',
            'publication_status' => 'draft',
            'is_featured'        => false,
            'published_at'       => null,
        ];
    }

    /**
     * @param array<string, mixed> $record
     * @param array<string, string> $errors
     */
    private function formView(array $record, array $errors): string
    {
        return $this->adminView('admin/courses/form', 'Course editor', [
            'record'     => array_merge($this->inputDefaults(), $record),
            'categories' => db_connect(config('Auth')->DBGroup)
                ->table('course_categories')
                ->where('status', 'active')
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray(),
            'media'      => $this->mediaOptions(),
            'errors'     => $errors,
            'isEditing'  => ! empty($record['id']),
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

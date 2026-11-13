<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'category_id',
        'title',
        'slug',
        'short_description',
        'description',
        'duration',
        'fee',
        'requirements',
        'outcomes',
        'featured_image',
        'status',
        'is_featured',
        'published_at',
    ];

    /**
     * Only explicitly dated public states are visible; draft and other states
     * remain private even when a slug is requested directly.
     *
     * @return list<array<string, mixed>>
     */
    public function findPublicCourses(): array
    {
        return $this->publicBuilder()
            ->orderBy('is_featured', 'DESC')
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findFeaturedPublicCourses(int $limit = 3): array
    {
        return $this->publicBuilder()
            ->where('is_featured', true)
            ->orderBy('title', 'ASC')
            ->limit(max(1, $limit))
            ->get()
            ->getResultArray();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findPublicCourseBySlug(string $slug): ?array
    {
        $course = $this->publicBuilder()
            ->where('slug', $slug)
            ->get()
            ->getRowArray();

        return $course === null ? null : $course;
    }

    private function publicBuilder(): BaseBuilder
    {
        return $this->builder()
            ->whereIn('status', ['available', 'proposed', 'development'])
            ->where('published_at <=', date('Y-m-d H:i:s'));
    }
}

<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'author_name',
        'status',
        'meta_title',
        'meta_description',
        'published_at',
    ];

    /**
     * @return array<string, mixed>|null
     */
    public function findPublicPostBySlug(string $slug): ?array
    {
        $post = $this->publicBuilder()
            ->where('slug', $slug)
            ->get()
            ->getRowArray();

        return $post === null ? null : $post;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function paginatePublicPosts(int $perPage = 12): array
    {
        return $this->where('status', 'published')
            ->where('published_at <=', date('Y-m-d H:i:s'))
            ->orderBy('published_at', 'DESC')
            ->paginate($perPage);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findLatestPublicPosts(int $limit = 3): array
    {
        return $this->publicBuilder()
            ->orderBy('published_at', 'DESC')
            ->limit(max(1, $limit))
            ->get()
            ->getResultArray();
    }

    private function publicBuilder(): BaseBuilder
    {
        return $this->builder()
            ->where('status', 'published')
            ->where('published_at <=', date('Y-m-d H:i:s'));
    }
}

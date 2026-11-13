<?php

namespace App\Models;

use CodeIgniter\Model;

class PageModel extends Model
{
    protected $table = 'pages';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'status',
        'published_at',
    ];

    /**
     * @return array<string, mixed>|null
     */
    public function findPublishedPageBySlug(string $slug): ?array
    {
        $page = $this->builder()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where('published_at <=', date('Y-m-d H:i:s'))
            ->get()
            ->getRowArray();

        return $page === null ? null : $page;
    }
}

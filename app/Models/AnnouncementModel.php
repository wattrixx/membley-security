<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'title',
        'slug',
        'content',
        'featured_image',
        'status',
        'starts_at',
        'ends_at',
        'published_at',
    ];

    /**
     * @return list<array<string, mixed>>
     */
    public function findPublicAnnouncements(int $limit = 5): array
    {
        return $this->publicBuilder()
            ->orderBy('published_at', 'DESC')
            ->limit(max(1, $limit))
            ->get()
            ->getResultArray();
    }

    private function publicBuilder(): BaseBuilder
    {
        $now = date('Y-m-d H:i:s');

        return $this->builder()
            ->where('status', 'published')
            ->where('published_at <=', $now)
            ->groupStart()
                ->where('starts_at', null)
                ->orWhere('starts_at <=', $now)
            ->groupEnd()
            ->groupStart()
                ->where('ends_at', null)
                ->orWhere('ends_at >=', $now)
            ->groupEnd();
    }
}

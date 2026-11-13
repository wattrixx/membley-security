<?php

namespace App\Models;

use CodeIgniter\Model;

class MediaModel extends Model
{
    protected $table = 'media';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'original_name',
        'stored_name',
        'file_path',
        'mime_type',
        'file_size',
        'alt_text',
    ];
    protected $useTimestamps = true;
}

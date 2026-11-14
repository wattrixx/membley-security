<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationModel extends Model
{
    protected $table = 'applications';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'application_number',
        'course_id',
        'full_name',
        'email',
        'phone',
        'country',
        'message',
        'privacy_accepted',
        'status',
    ];
    protected $useTimestamps = true;
}

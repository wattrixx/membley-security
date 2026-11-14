<?php

namespace App\Models;

use CodeIgniter\Model;

class EnquiryModel extends Model
{
    protected $table = 'enquiries';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'enquiry_type',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'privacy_accepted',
        'status',
    ];
    protected $useTimestamps = true;
}

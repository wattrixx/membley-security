<?php

namespace App\Models;

use CodeIgniter\Model;

class SecurityEnquiryModel extends Model
{
    protected $table = 'security_enquiries';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'organization',
        'contact_name',
        'email',
        'phone',
        'service_category',
        'message',
        'privacy_accepted',
        'status',
    ];
    protected $useTimestamps = true;
}

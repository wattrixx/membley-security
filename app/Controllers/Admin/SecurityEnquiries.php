<?php

namespace App\Controllers\Admin;

class SecurityEnquiries extends SubmissionManagement
{
    protected string $table = 'security_enquiries';
    protected string $entity = 'security_enquiry';
    protected string $permission = 'security.enquiries';
    protected string $route = 'admin.security.enquiries';
    protected string $title = 'Security Services enquiries';
    protected string $nameField = 'contact_name';
    protected array $searchFields = ['contact_name', 'organization'];
    protected array $statuses = [
        'new' => 'New', 'in_progress' => 'In progress', 'awaiting_response' => 'Awaiting response',
        'resolved' => 'Resolved', 'closed' => 'Closed',
    ];
}

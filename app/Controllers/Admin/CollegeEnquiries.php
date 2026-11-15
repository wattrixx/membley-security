<?php

namespace App\Controllers\Admin;

class CollegeEnquiries extends SubmissionManagement
{
    protected string $table = 'enquiries';
    protected string $entity = 'college_enquiry';
    protected string $permission = 'college.enquiries';
    protected string $route = 'admin.college.enquiries';
    protected string $title = 'College enquiries';
    protected string $nameField = 'name';
    protected array $searchFields = ['name', 'subject'];
    protected array $statuses = [
        'new' => 'New', 'in_progress' => 'In progress', 'awaiting_response' => 'Awaiting response',
        'resolved' => 'Resolved', 'closed' => 'Closed',
    ];
}

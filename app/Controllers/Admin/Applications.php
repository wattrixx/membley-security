<?php

namespace App\Controllers\Admin;

class Applications extends SubmissionManagement
{
    protected string $table = 'applications';
    protected string $entity = 'application';
    protected string $permission = 'college.applications';
    protected string $route = 'admin.applications';
    protected string $title = 'College applications';
    protected string $nameField = 'full_name';
    protected array $searchFields = ['full_name', 'application_number'];
    protected array $statuses = [
        'received' => 'Submitted', 'under_review' => 'Under review',
        'awaiting_information' => 'Awaiting information', 'accepted' => 'Accepted',
        'declined' => 'Declined', 'withdrawn' => 'Withdrawn',
    ];
}

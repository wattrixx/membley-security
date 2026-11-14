<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class PublicSubmissions extends BaseConfig
{
    public bool $notificationsEnabled = false;
    public string $collegeRecipient = '';
    public string $securityRecipient = '';

    public function __construct()
    {
        parent::__construct();

        $this->notificationsEnabled = (bool) env('publicSubmissions.notificationsEnabled', false);
        $this->collegeRecipient = trim((string) env('publicSubmissions.collegeRecipient', ''));
        $this->securityRecipient = trim((string) env('publicSubmissions.securityRecipient', ''));
    }
}

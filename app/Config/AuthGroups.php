<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'user';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://codeigniter4.github.io/shield/quick_start_guide/using_authorization/#change-available-groups for more info
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Complete control of the site.',
        ],
        'content_manager' => [
            'title'       => 'Content Manager',
            'description' => 'Manage public pages, news, announcements and media.',
        ],
        'admissions_officer' => [
            'title'       => 'Admissions Officer',
            'description' => 'Access college administration.',
        ],
        'security_services_officer' => [
            'title'       => 'Security Services Officer',
            'description' => 'Access security-services administration.',
        ],
        'user' => [
            'title'       => 'Unassigned',
            'description' => 'No administrator permissions.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     *
     * If a permission is not listed here it cannot be used.
     */
    public array $permissions = [
        'admin.access'                 => 'Access the administration area.',
        'admin.users.view'             => 'View administrator accounts.',
        'admin.users.manage'           => 'Create and manage administrator accounts.',
        'admin.settings.view'          => 'View website settings.',
        'admin.settings.manage'        => 'Manage website settings.',
        'content.pages.view'           => 'View public pages.',
        'content.pages.manage'         => 'Manage public pages.',
        'content.news.view'            => 'View news articles.',
        'content.news.manage'          => 'Manage news articles.',
        'content.announcements.view'   => 'View announcements.',
        'content.announcements.manage' => 'Manage announcements.',
        'content.media.view'           => 'View media.',
        'content.media.manage'         => 'Manage media.',
        'college.dashboard.view'       => 'View college administration.',
        'college.courses.view'          => 'View courses.',
        'college.courses.manage'       => 'Manage courses.',
        'college.applications.view'    => 'View applications.',
        'college.applications.manage'  => 'Manage applications.',
        'college.enquiries.view'       => 'View college enquiries.',
        'college.enquiries.manage'     => 'Manage college enquiries.',
        'security.dashboard.view'      => 'View security-services administration.',
        'security.enquiries.view'      => 'View security-services enquiries.',
        'security.enquiries.manage'    => 'Manage security-services enquiries.',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines group-level permissions.
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'content.*',
            'college.*',
            'security.*',
        ],
        'content_manager' => [
            'admin.access',
            'content.*',
        ],
        'admissions_officer' => [
            'admin.access',
            'college.*',
        ],
        'security_services_officer' => [
            'admin.access',
            'security.*',
        ],
        'user' => [],
    ];
}

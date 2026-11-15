<?php

namespace App\Controllers\Admin;

use CodeIgniter\Shield\Entities\User;

class Dashboard extends AdminBaseController
{
    public function index(): string
    {
        /** @var User $user */
        $user = auth()->user();
        $cards = [];

        if ($user->can('admin.users.view')) {
            $cards[] = [
                'label'       => 'Administrator accounts',
                'count'       => $this->countAdminAccounts(),
                'description' => 'Manage administrator accounts and roles.',
                'href'        => url_to('admin.users'),
                'available'   => true,
            ];
        }

        $contentModules = [
            ['content.pages.view', 'Public pages', 'pages', 'admin.cms.pages'],
            ['content.news.view', 'News articles', 'posts', 'admin.cms.news'],
            ['content.announcements.view', 'Announcements', 'announcements', 'admin.cms.announcements'],
            ['content.media.view', 'Media items', 'media', 'admin.cms.media'],
        ];

        foreach ($contentModules as [$permission, $label, $table, $route]) {
            if ($user->can($permission)) {
                $cards[] = $this->moduleCard($label, $this->countTable($table), url_to($route));
            }
        }

        $collegeModules = [
            ['college.courses.view', 'Courses', 'courses', 'admin.cms.courses'],
        ];

        foreach ($collegeModules as [$permission, $label, $table, $route]) {
            if ($user->can($permission)) {
                $cards[] = $this->moduleCard($label, $this->countTable($table), url_to($route));
            }
        }

        if ($user->can('college.applications.view')) {
            $cards[] = $this->submissionCard('Total applications', 'applications', [], 'admin.applications');
            $cards[] = $this->submissionCard('New applications', 'applications', ['status' => 'received'], 'admin.applications');
            $cards[] = $this->submissionCard('Applications awaiting review', 'applications', [], 'admin.applications', ['received', 'under_review']);
        }
        if ($user->can('college.enquiries.view')) {
            $cards[] = $this->submissionCard('College enquiries awaiting attention', 'enquiries', ['enquiry_type' => 'college'], 'admin.college.enquiries', ['new', 'in_progress', 'awaiting_response']);
        }
        if ($user->can('security.enquiries.view')) {
            $cards[] = $this->submissionCard('New Security Services enquiries', 'security_enquiries', ['status' => 'new'], 'admin.security.enquiries');
        }

        $groupTitles = config('AuthGroups')->groups;
        $roles = array_map(
            static fn (string $group): string => $groupTitles[$group]['title'] ?? $group,
            $user->getGroups() ?? [],
        );

        return view('admin/dashboard', [
            'pageTitle' => 'Dashboard | Membley Administration',
            'username'  => $user->username,
            'roles'     => $roles,
            'cards'     => $cards,
        ]);
    }

    private function submissionCard(string $label, string $table, array $where, string $route, array $statuses = []): array
    {
        $query = db_connect(config('Auth')->DBGroup)->table($table)->where($where);
        if ($statuses !== []) {
            $query->whereIn('status', $statuses);
        }
        return [
            'label'       => $label,
            'count'       => (int) $query->countAllResults(),
            'description' => 'Open the appropriate submission queue.',
            'href'        => url_to($route),
            'available'   => true,
        ];
    }

    /**
     * @return array{label: string, count: int, description: string, href: string, available: true}
     */
    private function moduleCard(string $label, int $count, string $href): array
    {
        return [
            'label'       => $label,
            'count'       => $count,
            'description' => 'Create and manage published website content.',
            'href'        => $href,
            'available'   => true,
        ];
    }

    private function countTable(string $table): int
    {
        return (int) db_connect(config('Auth')->DBGroup)
            ->table($table)
            ->countAllResults();
    }

    private function countAdminAccounts(): int
    {
        $usersTable = config('Auth')->tables['users'];

        return (int) db_connect(config('Auth')->DBGroup)
            ->table($usersTable)
            ->where('deleted_at', null)
            ->countAllResults();
    }

}

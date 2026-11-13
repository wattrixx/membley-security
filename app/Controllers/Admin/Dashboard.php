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
            ['college.applications.view', 'Applications', 'applications', null],
        ];

        foreach ($collegeModules as [$permission, $label, $table, $route]) {
            if ($user->can($permission)) {
                $cards[] = $route === null
                    ? $this->futureCard($label, $this->countTable($table))
                    : $this->moduleCard($label, $this->countTable($table), url_to($route));
            }
        }

        if ($user->can('security.enquiries.view')) {
            $cards[] = $this->futureCard(
                'Security Services enquiries',
                $this->countSecurityEnquiries(),
            );
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

    /**
     * @return array{label: string, count: int, description: string, href: null, available: false}
     */
    private function futureCard(string $label, int $count): array
    {
        return [
            'label'       => $label,
            'count'       => $count,
            'description' => 'Management will be added in a later milestone.',
            'href'        => null,
            'available'   => false,
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

    private function countSecurityEnquiries(): int
    {
        return (int) db_connect(config('Auth')->DBGroup)
            ->table('enquiries')
            ->where('enquiry_type', 'security_services')
            ->countAllResults();
    }
}

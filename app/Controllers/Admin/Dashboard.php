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
            ['content.pages.view', 'Public pages', 'pages'],
            ['content.news.view', 'News articles', 'posts'],
            ['content.announcements.view', 'Announcements', 'announcements'],
            ['content.media.view', 'Media items', 'media'],
        ];

        foreach ($contentModules as [$permission, $label, $table]) {
            if ($user->can($permission)) {
                $cards[] = $this->futureCard($label, $this->countTable($table));
            }
        }

        $collegeModules = [
            ['college.courses.view', 'Courses', 'courses'],
            ['college.applications.view', 'Applications', 'applications'],
        ];

        foreach ($collegeModules as [$permission, $label, $table]) {
            if ($user->can($permission)) {
                $cards[] = $this->futureCard($label, $this->countTable($table));
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

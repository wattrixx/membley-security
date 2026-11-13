<?php

namespace App\Controllers\Admin;

use App\Models\AdminAuditLogModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Validation\ValidationRules;
use CodeIgniter\Exceptions\PageNotFoundException;
use Throwable;

class Users extends AdminBaseController
{
    private const ROLE_KEYS = [
        'superadmin',
        'content_manager',
        'admissions_officer',
        'security_services_officer',
    ];

    public function index(): string
    {
        $authConfig = config('Auth');
        $db = db_connect($authConfig->DBGroup);
        $usersTable = $authConfig->tables['users'];
        $identitiesTable = $authConfig->tables['identities'];
        $groupsTable = $authConfig->tables['groups_users'];
        $users = $db->table($usersTable . ' u')
            ->select('u.id, u.username, u.active, u.last_active, i.secret AS email')
            ->join($identitiesTable . ' i', 'i.user_id = u.id')
            ->join($groupsTable . ' gu', 'gu.user_id = u.id')
            ->where('i.type', 'email_password')
            ->whereIn('gu.group', self::ROLE_KEYS)
            ->where('u.deleted_at', null)
            ->distinct()
            ->orderBy('u.id', 'DESC')
            ->get()
            ->getResultArray();
        $userIds = array_column($users, 'id');
        $groupMap = [];

        if ($userIds !== []) {
            $groupRows = $db->table($groupsTable)
                ->select(['user_id', 'group'])
                ->whereIn('group', self::ROLE_KEYS)
                ->whereIn('user_id', $userIds)
                ->get()
                ->getResultArray();

            foreach ($groupRows as $groupRow) {
                $groupMap[(int) $groupRow['user_id']][] = $groupRow['group'];
            }
        }

        $knownGroups = config('AuthGroups')->groups;
        $accounts = [];

        foreach ($users as $user) {
            $groups = $groupMap[(int) $user['id']] ?? [];
            $user['role'] = $groups[0] ?? 'user';
            $user['role_label'] = $knownGroups[$user['role']]['title'] ?? $user['role'];
            $accounts[] = $user;
        }

        return view('admin/users/index', [
            'pageTitle' => 'Administrator accounts | Membley Administration',
            'accounts'  => $accounts,
            'roles'     => $this->roles(),
            'currentId' => (int) auth()->id(),
            'message'   => session('message'),
            'error'     => session('error'),
        ]);
    }

    public function createForm(): string
    {
        return $this->createView('', '', 'content_manager', []);
    }

    public function create(): RedirectResponse|string
    {
        $input = [
            'username'         => trim($this->postString('username')),
            'email'            => strtolower(trim($this->postString('email'))),
            'password'         => $this->postString('password'),
            'password_confirm' => $this->postString('password_confirm'),
            'role'             => $this->postString('role'),
        ];
        $rules = (new ValidationRules())->getRegistrationRules();
        $passwordRules = $rules['password']['rules'];
        $passwordRules = is_array($passwordRules) ? $passwordRules : explode('|', $passwordRules);
        $rules['password']['rules'] = array_values(array_filter(
            $passwordRules,
            static fn ($rule): bool => ! is_string($rule) || ! str_starts_with($rule, 'strong_password'),
        ));
        $rules['role'] = [
            'label' => 'Administrator role',
            'rules' => 'required|in_list[' . implode(',', self::ROLE_KEYS) . ']',
        ];

        if (! $this->validateData($input, $rules)) {
            return $this->createView(
                $input['username'],
                $input['email'],
                $input['role'],
                $this->validator->getErrors(),
            );
        }

        $passwordCandidate = new User(['username' => $input['username']]);
        $passwordCandidate->email = $input['email'];
        $passwordCheck = service('passwords')->check($input['password'], $passwordCandidate);

        if (! $passwordCheck->isOK()) {
            return $this->createView(
                $input['username'],
                $input['email'],
                $input['role'],
                ['password' => 'Choose a stronger password that does not contain your username or email address.'],
            );
        }

        $db = db_connect(config('Auth')->DBGroup);
        $userModel = model(UserModel::class);
        $user = new User([
            'username' => $input['username'],
            'active'   => true,
        ]);
        $user->email = $input['email'];
        $user->password = $input['password'];

        if (! $db->transBegin()) {
            throw new \RuntimeException('The administrator account transaction could not be started.');
        }

        try {
            if (! $userModel->save($user)) {
                throw new \RuntimeException('The administrator account could not be created.');
            }

            $createdUser = $userModel->findByCredentials(['email' => $input['email']]);

            if ($createdUser === null) {
                throw new \RuntimeException('The administrator account could not be reloaded after creation.');
            }

            $createdUser->syncGroups($input['role']);
            $createdUser->syncPermissions();
            $this->recordAccountChange(
                (int) $createdUser->id,
                'admin.user_created',
                'role=' . $input['role'],
            );
            $this->commitOrFail($db);
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }

        return redirect()->route('admin.users')->with('message', 'Administrator account created.');
    }

    public function updateRole(string $id): RedirectResponse
    {
        $role = $this->postString('role');

        if (! in_array($role, self::ROLE_KEYS, true)) {
            return redirect()->route('admin.users')->with('error', 'Choose a valid administrator role.');
        }

        $userId = filter_var($id, FILTER_VALIDATE_INT);

        if ($userId === false || $userId === (int) auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'You cannot change your own administrator role.');
        }

        $db = db_connect(config('Auth')->DBGroup);
        $userModel = model(UserModel::class);

        if (! $db->transBegin()) {
            throw new \RuntimeException('The administrator role transaction could not be started.');
        }

        try {
            $activeSuperAdminCount = $this->lockActiveSuperAdmins();
            $this->lockUserRow($db, $userId);
            $target = $userModel->findById($userId);

            if ($target === null) {
                throw PageNotFoundException::forPageNotFound('Administrator account not found.');
            }

            $oldGroups = $target->getGroups() ?? [];
            $wasActiveSuperAdmin = $target->active && in_array('superadmin', $oldGroups, true);

            if ($wasActiveSuperAdmin && $role !== 'superadmin' && $activeSuperAdminCount <= 1) {
                $db->transRollback();

                return redirect()->route('admin.users')->with('error', 'The last active Super Admin cannot be demoted.');
            }

            $target->syncGroups($role);
            $target->syncPermissions();
            $this->recordAccountChange(
                (int) $target->id,
                'admin.user_role_changed',
                'from=' . implode(',', $oldGroups) . ';to=' . $role,
            );
            $this->commitOrFail($db);
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }

        return redirect()->route('admin.users')->with('message', 'Administrator role updated.');
    }

    public function updateStatus(string $id): RedirectResponse
    {
        $activeInput = $this->postString('active');

        if (! in_array($activeInput, ['0', '1'], true)) {
            return redirect()->route('admin.users')->with('error', 'Choose a valid account status.');
        }

        $userId = filter_var($id, FILTER_VALIDATE_INT);

        if ($userId === false || $userId === (int) auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'You cannot change your own account status.');
        }

        $active = $activeInput === '1';
        $db = db_connect(config('Auth')->DBGroup);
        $userModel = model(UserModel::class);

        if (! $db->transBegin()) {
            throw new \RuntimeException('The administrator status transaction could not be started.');
        }

        try {
            $activeSuperAdminCount = $this->lockActiveSuperAdmins();
            $this->lockUserRow($db, $userId);
            $target = $userModel->findById($userId);

            if ($target === null) {
                throw PageNotFoundException::forPageNotFound('Administrator account not found.');
            }

            $groups = $target->getGroups() ?? [];

            if (
                ! $active
                && $target->active
                && in_array('superadmin', $groups, true)
                && $activeSuperAdminCount <= 1
            ) {
                $db->transRollback();

                return redirect()->route('admin.users')->with('error', 'The last active Super Admin cannot be deactivated.');
            }

            if ((bool) $target->active === $active) {
                $db->transRollback();

                return redirect()->route('admin.users')->with('message', 'Account status is unchanged.');
            }

            $target->active = $active;

            if (! $userModel->save($target)) {
                throw new \RuntimeException('The administrator status could not be updated.');
            }
            $this->recordAccountChange(
                (int) $target->id,
                $active ? 'admin.user_activated' : 'admin.user_deactivated',
                '',
            );
            $this->commitOrFail($db);
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }

        return redirect()->route('admin.users')->with('message', 'Account status updated.');
    }

    /**
     * @param array<string, string> $errors
     */
    private function createView(
        string $username,
        string $email,
        string $role,
        array $errors,
    ): string {
        return view('admin/users/create', [
            'pageTitle' => 'Create administrator | Membley Administration',
            'username'  => $username,
            'email'     => $email,
            'role'      => $role,
            'roles'     => $this->roles(),
            'errors'    => $errors,
        ]);
    }

    /**
     * @return array<string, array{title: string, description: string}>
     */
    private function roles(): array
    {
        return array_intersect_key(
            config('AuthGroups')->groups,
            array_fill_keys(self::ROLE_KEYS, true),
        );
    }

    private function lockActiveSuperAdmins(): int
    {
        $tables = config('Auth')->tables;
        $db = db_connect(config('Auth')->DBGroup);
        $query = $db->table($tables['groups_users'] . ' gu')
            ->select('gu.user_id')
            ->join($tables['users'] . ' u', 'u.id = gu.user_id')
            ->where('gu.group', 'superadmin')
            ->where('u.active', 1)
            ->where('u.deleted_at', null);
        $sql = $query->getCompiledSelect();

        return count($db->query($sql . ' FOR UPDATE')->getResultArray());
    }

    private function lockUserRow(BaseConnection $db, int $userId): void
    {
        $table = config('Auth')->tables['users'];
        $db->query("SELECT id FROM {$table} WHERE id = ? FOR UPDATE", [$userId]);
    }

    private function recordAccountChange(int $entityId, string $action, string $details): void
    {
        $recorded = model(AdminAuditLogModel::class)->record(
            (int) auth()->id(),
            $action,
            'admin_user',
            $entityId,
            $details,
            $this->request->getIPAddress(),
        );

        if (! $recorded) {
            throw new \RuntimeException('The administrator change could not be recorded.');
        }
    }

    private function commitOrFail(BaseConnection $db): void
    {
        if (! $db->transStatus()) {
            throw new \RuntimeException('The administrator change could not be saved.');
        }

        if (! $db->transCommit()) {
            throw new \RuntimeException('The administrator change could not be committed.');
        }
    }
}

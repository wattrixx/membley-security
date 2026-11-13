<?php

namespace App\Commands;

use App\Models\AdminAuditLogModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Validation\ValidationRules;
use Config\Auth;
use Throwable;

class ProvisionSuperAdmin extends BaseCommand
{
    protected $group = 'Membley Admin';
    protected $name = 'admin:provision-superadmin';
    protected $description = 'Provision the first Super Admin account.';
    protected $usage = 'admin:provision-superadmin <username> <email>';
    protected $arguments = [
        'username' => 'The initial administrator username.',
        'email'    => 'The initial administrator email address.',
    ];

    public function run(array $params): int
    {
        $username = trim((string) ($params[0] ?? ''));
        $email = strtolower(trim((string) ($params[1] ?? '')));
        $environmentKey = 'MEMBLEY_INITIAL_ADMIN_PASSWORD';
        $password = getenv($environmentKey);
        putenv($environmentKey);
        unset($_ENV[$environmentKey], $_SERVER[$environmentKey]);

        if ($username === '' || $email === '' || $password === false || $password === '') {
            CLI::error('Provide username and email arguments and set MEMBLEY_INITIAL_ADMIN_PASSWORD in the process environment.');

            return EXIT_ERROR;
        }

        $authConfig = config(Auth::class);
        $rules = (new ValidationRules())->getRegistrationRules();
        unset($rules['password_confirm']);
        $passwordRules = $rules['password']['rules'];
        $passwordRules = is_array($passwordRules) ? $passwordRules : explode('|', $passwordRules);
        $rules['password']['rules'] = array_values(array_filter(
            $passwordRules,
            static fn ($rule): bool => ! is_string($rule) || ! str_starts_with($rule, 'strong_password'),
        ));
        $validation = service('validation');
        $validation->setRules($rules);
        $data = [
            'username' => $username,
            'email'    => $email,
            'password' => $password,
        ];

        if (! $validation->run($data)) {
            foreach ($validation->getErrors() as $error) {
                CLI::error($error);
            }

            return EXIT_ERROR;
        }

        $passwordCandidate = new User(['username' => $username]);
        $passwordCandidate->email = $email;
        $passwordCheck = service('passwords')->check($password, $passwordCandidate);

        if (! $passwordCheck->isOK()) {
            CLI::error('Choose a stronger password that does not contain the username or email address.');

            return EXIT_ERROR;
        }

        $tables = $authConfig->tables;
        $db = db_connect($authConfig->DBGroup);
        $existingSuperAdmin = $db->table($tables['groups_users'] . ' gu')
            ->join($tables['users'] . ' u', 'u.id = gu.user_id')
            ->where('gu.group', 'superadmin')
            ->where('u.deleted_at', null)
            ->countAllResults();

        if ($existingSuperAdmin > 0) {
            CLI::error('A Super Admin account already exists. Use the authenticated admin account-management page.');

            return EXIT_ERROR;
        }

        $user = new User([
            'username' => $username,
            'active'   => true,
        ]);
        $user->email = $email;
        $user->password = $password;
        $userModel = model(UserModel::class);

        if (! $db->transBegin()) {
            CLI::error('The Super Admin provisioning transaction could not be started.');

            return EXIT_ERROR;
        }

        try {
            if (! $userModel->save($user)) {
                throw new \RuntimeException('The Super Admin account could not be created.');
            }

            $createdUser = $userModel->findByCredentials(['email' => $email]);

            if ($createdUser === null) {
                throw new \RuntimeException('The Super Admin account could not be reloaded after creation.');
            }

            $createdUser->syncGroups('superadmin');
            $createdUser->syncPermissions();

            if (! model(AdminAuditLogModel::class)->record(
                (int) $createdUser->id,
                'admin.superadmin_provisioned',
                'admin_user',
                (int) $createdUser->id,
                'Initial Super Admin provisioned from the CLI.',
                null,
            )) {
                throw new \RuntimeException('The Super Admin provisioning event could not be recorded.');
            }

            if (! $db->transStatus()) {
                throw new \RuntimeException('The Super Admin account could not be saved.');
            }

            if (! $db->transCommit()) {
                throw new \RuntimeException('The Super Admin account could not be committed.');
            }
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        } finally {
            unset($password, $data, $user);
        }

        CLI::write('Initial Super Admin provisioned. The password was not printed or stored by this command.', 'green');

        return EXIT_SUCCESS;
    }
}

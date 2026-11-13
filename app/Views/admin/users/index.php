<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<header class="admin-page-heading admin-heading-row">
    <div>
        <span class="admin-eyebrow">Super Admin controls</span>
        <h1>Administrator accounts</h1>
        <p>Passwords and password hashes are never shown on this page.</p>
    </div>
    <a class="admin-button admin-button-primary" href="<?= url_to('admin.users.create') ?>">Create administrator</a>
</header>

<?php if ($message !== null): ?>
    <div class="admin-alert admin-alert-success" role="status"><?= esc($message) ?></div>
<?php endif; ?>
<?php if ($error !== null): ?>
    <div class="admin-alert admin-alert-error" role="alert"><?= esc($error) ?></div>
<?php endif; ?>

<?php if ($accounts === []): ?>
    <section class="admin-empty-state">
        <h2>No administrator accounts found</h2>
        <p>Create an authorised account to begin using the administration area.</p>
    </section>
<?php else: ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <caption>Administrator accounts and assigned roles</caption>
            <thead>
            <tr>
                <th scope="col">Account</th>
                <th scope="col">Role</th>
                <th scope="col">Status</th>
                <th scope="col">Last active</th>
                <th scope="col">Role assignment</th>
                <th scope="col">Account status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($accounts as $account): ?>
                <tr>
                    <td>
                        <strong><?= esc($account['username']) ?></strong>
                        <span class="admin-cell-secondary"><?= esc($account['email']) ?></span>
                    </td>
                    <td><?= esc($account['role_label']) ?></td>
                    <td>
                        <span class="admin-status <?= $account['active'] ? 'is-active' : 'is-inactive' ?>">
                            <?= $account['active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td><?= esc($account['last_active'] ?? 'Never') ?></td>
                    <td>
                        <?php if ((int) $account['id'] === $currentId): ?>
                            <span class="admin-cell-secondary">Current account</span>
                        <?php else: ?>
                            <form action="<?= esc(url_to('admin.users.role', $account['id']), 'attr') ?>"
                                  method="post"
                                  class="admin-inline-form">
                                <?= csrf_field() ?>
                                <label class="sr-only" for="role-<?= esc((string) $account['id'], 'attr') ?>">
                                    Role for <?= esc($account['username']) ?>
                                </label>
                                <select id="role-<?= esc((string) $account['id'], 'attr') ?>" name="role" required>
                                    <option value=""
                                        disabled
                                        <?= isset($roles[$account['role']]) ? '' : 'selected' ?>>
                                        Choose role
                                    </option>
                                    <?php foreach ($roles as $roleKey => $roleInfo): ?>
                                        <option value="<?= esc($roleKey, 'attr') ?>"
                                            <?= $account['role'] === $roleKey ? 'selected' : '' ?>>
                                            <?= esc($roleInfo['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="admin-button admin-button-small" type="submit">Save role</button>
                            </form>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ((int) $account['id'] === $currentId): ?>
                            <span class="admin-cell-secondary">Current account</span>
                        <?php else: ?>
                            <form action="<?= esc(url_to('admin.users.status', $account['id']), 'attr') ?>"
                                  method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="active" value="<?= $account['active'] ? '0' : '1' ?>">
                                <button class="admin-button admin-button-small <?= $account['active'] ? 'admin-button-danger' : 'admin-button-primary' ?>"
                                        type="submit">
                                    <?= $account['active'] ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<header class="admin-page-heading">
    <span class="admin-eyebrow">Super Admin controls</span>
    <h1>Create an administrator</h1>
    <p>Use a unique account and a strong, individual password.</p>
</header>

<?php if ($errors !== []): ?>
    <div class="admin-alert admin-alert-error" role="alert" aria-label="Validation errors">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= url_to('admin.users.store') ?>" method="post" class="admin-form admin-form-card">
    <?= csrf_field() ?>
    <label for="username">Username</label>
    <input id="username"
           name="username"
           type="text"
           maxlength="30"
           autocomplete="username"
           value="<?= esc($username, 'attr') ?>"
           required>

    <label for="email">Email address</label>
    <input id="email"
           name="email"
           type="email"
           maxlength="254"
           autocomplete="email"
           value="<?= esc($email, 'attr') ?>"
           required>

    <label for="role">Administrator role</label>
    <select id="role" name="role" required>
        <?php foreach ($roles as $roleKey => $roleInfo): ?>
            <option value="<?= esc($roleKey, 'attr') ?>"
                <?= $role === $roleKey ? 'selected' : '' ?>>
                <?= esc($roleInfo['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="password">Initial password</label>
    <input id="password"
           name="password"
           type="password"
           autocomplete="new-password"
           required>

    <label for="password_confirm">Confirm initial password</label>
    <input id="password_confirm"
           name="password_confirm"
           type="password"
           autocomplete="new-password"
           required>
    <p class="admin-form-hint">Passwords must be at least 12 characters and pass Shield's password checks.</p>

    <div class="admin-form-actions">
        <button class="admin-button admin-button-primary" type="submit">Create account</button>
        <a class="admin-button admin-button-light" href="<?= url_to('admin.users') ?>">Cancel</a>
    </div>
</form>
<?= $this->endSection() ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#101018">
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>?v=2">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body class="admin-login-body">
<main class="admin-login-shell">
    <a class="admin-login-brand" href="<?= base_url('/') ?>">
        <img src="<?= base_url('assets/images/membley-logo.jpeg') ?>"
             alt=""
             width="64"
             height="64">
        <span>
            <strong>MEMBLEY</strong>
            <small>SECURITY GROUP</small>
        </span>
    </a>

    <section class="admin-login-card" aria-labelledby="loginHeading">
        <span class="admin-eyebrow">Secure administration</span>
        <h1 id="loginHeading">Administrator sign in</h1>
        <p>Use your authorised Membley administrator account.</p>

        <?php if ($error !== null): ?>
            <div class="admin-alert admin-alert-error" role="alert"><?= esc($error) ?></div>
        <?php endif; ?>

        <?php if ($validationErrors !== []): ?>
            <div class="admin-alert admin-alert-error" role="alert" aria-label="Validation errors">
                <ul>
                    <?php foreach ($validationErrors as $validationError): ?>
                        <li><?= esc($validationError) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('admin/login') ?>" method="post" class="admin-form">
            <?= csrf_field() ?>
            <label for="email">Email address</label>
            <input id="email"
                   name="email"
                   type="email"
                   inputmode="email"
                   autocomplete="username"
                   maxlength="254"
                   value="<?= esc($email, 'attr') ?>"
                   required>

            <label for="password">Password</label>
            <input id="password"
                   name="password"
                   type="password"
                   autocomplete="current-password"
                   required>

            <button class="admin-button admin-button-primary" type="submit">Sign in</button>
        </form>
        <p class="admin-login-footnote">Administrator accounts are provisioned by authorised staff.</p>
    </section>
</main>
</body>
</html>

<?php
$currentUser = auth()->user();
$groupTitles = config('AuthGroups')->groups;
$roleLabels = array_map(
    static fn (string $group): string => $groupTitles[$group]['title'] ?? $group,
    $currentUser->getGroups() ?? [],
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#101018">
    <title><?= esc($pageTitle ?? 'Administration | Membley Security Group') ?></title>
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>?v=2">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body class="admin-body">
<a class="admin-skip-link" href="#adminContent">Skip to content</a>

<div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
        <a class="admin-brand" href="<?= site_url('admin') ?>" aria-label="Membley administration home">
            <img src="<?= base_url('assets/images/membley-logo.jpeg') ?>"
                 alt=""
                 width="46"
                 height="46">
            <span>
                <strong>MEMBLEY</strong>
                <small>SECURITY GROUP</small>
            </span>
        </a>

        <nav class="admin-navigation" aria-label="Administration navigation">
            <span class="admin-nav-heading">Workspace</span>
            <a class="admin-nav-link" href="<?= site_url('admin') ?>">Dashboard</a>

            <?php if ($currentUser->can('admin.users.view')): ?>
                <a class="admin-nav-link" href="<?= url_to('admin.users') ?>">Administrator accounts</a>
            <?php endif; ?>

            <?php if ($currentUser->can('content.pages.view')): ?>
                <a class="admin-nav-link" href="<?= url_to('admin.cms.pages') ?>">Pages</a>
            <?php endif; ?>
            <?php if ($currentUser->can('content.news.view')): ?>
                <a class="admin-nav-link" href="<?= url_to('admin.cms.news') ?>">News &amp; blog</a>
            <?php endif; ?>
            <?php if ($currentUser->can('content.announcements.view')): ?>
                <a class="admin-nav-link" href="<?= url_to('admin.cms.announcements') ?>">Announcements</a>
            <?php endif; ?>
            <?php if ($currentUser->can('content.media.view')): ?>
                <a class="admin-nav-link" href="<?= url_to('admin.cms.media') ?>">Media library</a>
            <?php endif; ?>
            <?php if ($currentUser->can('college.courses.view')): ?>
                <a class="admin-nav-link" href="<?= url_to('admin.cms.courses') ?>">Courses</a>
            <?php endif; ?>
            <?php if ($currentUser->can('college.applications.view')): ?>
                <a class="admin-nav-link" href="<?= url_to('admin.applications') ?>">Applications</a>
            <?php endif; ?>
            <?php if ($currentUser->can('college.enquiries.view')): ?>
                <a class="admin-nav-link" href="<?= url_to('admin.college.enquiries') ?>">College enquiries</a>
            <?php endif; ?>
            <?php if ($currentUser->can('security.enquiries.view')): ?>
                <a class="admin-nav-link" href="<?= url_to('admin.security.enquiries') ?>">Security enquiries</a>
            <?php endif; ?>

            <?php if ($currentUser->can('admin.settings.view')): ?>
                <span class="admin-nav-link is-disabled" aria-disabled="true">
                    Website settings <small>Later milestone</small>
                </span>
            <?php endif; ?>
        </nav>

        <div class="admin-sidebar-note">
            <span>Signed in as</span>
            <strong><?= esc($currentUser->username) ?></strong>
            <small><?= esc(implode(', ', $roleLabels)) ?></small>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="admin-menu-toggle"
                    id="adminMenuToggle"
                    type="button"
                    aria-label="Open administration navigation"
                    aria-controls="adminSidebar"
                    aria-expanded="false">
                <span aria-hidden="true">&#9776;</span>
            </button>
            <div class="admin-topbar-user">
                <span><?= esc($currentUser->username) ?></span>
                <form action="<?= site_url('admin/logout') ?>" method="post">
                    <?= csrf_field() ?>
                    <button class="admin-logout" type="submit">Sign out</button>
                </form>
            </div>
        </header>

        <main class="admin-content" id="adminContent">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

<script src="<?= base_url('assets/js/admin.js') ?>" defer></script>
</body>
</html>

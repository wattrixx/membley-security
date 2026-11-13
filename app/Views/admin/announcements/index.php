<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<header class="admin-heading-row admin-page-heading">
    <div>
        <span class="admin-eyebrow">Content management</span>
        <h1>Announcements</h1>
        <p>Published announcements appear on the public homepage and news page only during their active date window.</p>
    </div>
    <a class="admin-button admin-button-primary" href="<?= url_to('admin.cms.announcements.create') ?>">Create announcement</a>
</header>

<?php if (! empty($message)): ?><div class="admin-alert admin-alert-success" role="status"><?= esc($message) ?></div><?php endif; ?>
<?php if (! empty($error)): ?><div class="admin-alert admin-alert-error" role="alert"><?= esc($error) ?></div><?php endif; ?>

<form class="admin-filter-form" method="get" action="<?= url_to('admin.cms.announcements') ?>">
    <label for="announcementSearch">Search announcements</label>
    <input id="announcementSearch" name="q" type="search" value="<?= esc($search, 'attr') ?>" placeholder="Title or URL slug">
    <label for="announcementStatus">Status</label>
    <select id="announcementStatus" name="status">
        <option value="">All statuses</option>
        <?php foreach (['draft', 'published', 'archived'] as $state): ?>
            <option value="<?= esc($state, 'attr') ?>" <?= $status === $state ? 'selected' : '' ?>><?= esc(ucfirst($state)) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="admin-button admin-button-dark" type="submit">Filter</button>
</form>

<?php if ($records === []): ?>
    <section class="admin-empty-state"><h2>No announcements found</h2><p>Draft announcements remain private until published.</p></section>
<?php else: ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <caption>Announcement records and publication windows</caption>
            <thead><tr><th>Announcement</th><th>Status</th><th>Starts</th><th>Ends</th><th>Publication</th><th>Action</th></tr></thead>
            <tbody><?php foreach ($records as $record): ?>
                <tr>
                    <td><strong><?= esc($record['title']) ?></strong><span class="admin-cell-secondary"><?= esc($record['slug']) ?></span></td>
                    <td><?= esc(ucfirst($record['status'])) ?></td>
                    <td><?= esc($record['starts_at'] ?? 'Any time') ?></td>
                    <td><?= esc($record['ends_at'] ?? 'No end date') ?></td>
                    <td><?= esc($record['published_at'] ?? '—') ?></td>
                    <td><a class="admin-button admin-button-light admin-button-small" href="<?= url_to('admin.cms.announcements.edit', $record['id']) ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?></tbody>
        </table>
    </div>
    <?= $pager->links('cmsAnnouncements') ?>
<?php endif; ?>
<?= $this->endSection() ?>

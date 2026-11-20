<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<header class="admin-page-heading">
    <span class="admin-eyebrow">Content management</span>
    <h1>Public pages</h1>
    <p>Only these existing page routes can be edited. System routes and arbitrary slugs are not editable.</p>
</header>

<?php if (! empty($message)): ?><div class="admin-alert admin-alert-success" role="status"><?= esc($message) ?></div><?php endif; ?>
<?php if (! empty($error)): ?><div class="admin-alert admin-alert-error" role="alert"><?= esc($error) ?></div><?php endif; ?>

<div class="admin-table-wrap">
    <table class="admin-table">
        <caption>Managed public page content</caption>
        <thead><tr><th>Page</th><th>Public route</th><th>CMS status</th><th>Last updated</th><th>Action</th></tr></thead>
        <tbody><?php foreach ($records as $record): ?>
            <tr>
                <td><strong><?= esc($record['title']) ?></strong></td>
                <td>/<?= esc(\App\Content\TransportContent::PAGES[$record['slug']]['path'] ?? $record['slug']) ?></td>
                <td><?= esc($record['is_saved'] ? ucfirst($record['status']) : 'Using current public template') ?></td>
                <td><?= esc($record['updated_at'] ?? '—') ?></td>
                <td><a class="admin-button admin-button-light admin-button-small" href="<?= url_to('admin.cms.pages.edit', $record['slug']) ?>">Edit</a></td>
            </tr>
        <?php endforeach; ?></tbody>
    </table>
</div>
<?= $this->endSection() ?>

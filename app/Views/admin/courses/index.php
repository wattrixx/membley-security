<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<header class="admin-heading-row admin-page-heading">
    <div>
        <span class="admin-eyebrow">College administration</span>
        <h1>Courses</h1>
        <p>Manage course information separately from its publication and availability status.</p>
    </div>
    <?php if (auth()->user()->can('college.courses.manage')): ?>
        <a class="admin-button admin-button-primary" href="<?= url_to('admin.cms.courses.create') ?>">Create course</a>
    <?php endif; ?>
</header>

<?php if (! empty($message)): ?><div class="admin-alert admin-alert-success" role="status"><?= esc($message) ?></div><?php endif; ?>
<?php if (! empty($error)): ?><div class="admin-alert admin-alert-error" role="alert"><?= esc($error) ?></div><?php endif; ?>

<form class="admin-filter-form" method="get" action="<?= url_to('admin.cms.courses') ?>">
    <label for="courseSearch">Search courses</label>
    <input id="courseSearch" name="q" type="search" value="<?= esc($search, 'attr') ?>" placeholder="Title or URL slug">
    <label for="coursePublication">Publication</label>
    <select id="coursePublication" name="publication">
        <option value="">All publication states</option>
        <?php foreach (['draft', 'published', 'archived'] as $state): ?>
            <option value="<?= esc($state, 'attr') ?>" <?= $publication === $state ? 'selected' : '' ?>><?= esc(ucfirst($state)) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="admin-button admin-button-dark" type="submit">Filter</button>
</form>

<?php if ($courses === []): ?>
    <section class="admin-empty-state">
        <h2>No courses found</h2>
        <p>Courses that you create will appear here. No sample courses are preloaded.</p>
    </section>
<?php else: ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <caption>Course records, including drafts and archived listings</caption>
            <thead><tr><th>Course</th><th>Availability</th><th>Publication</th><th>Updated</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><strong><?= esc($course['title']) ?></strong><span class="admin-cell-secondary"><?= esc($course['slug']) ?></span></td>
                    <td><?= esc(ucfirst($course['status'])) ?></td>
                    <td><?= esc(ucfirst($course['publication_status'])) ?></td>
                    <td><?= esc($course['updated_at'] ?? '—') ?></td>
                    <td><a class="admin-button admin-button-light admin-button-small" href="<?= url_to('admin.cms.courses.edit', $course['id']) ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= $pager->links('cmsCourses') ?>
<?php endif; ?>
<?= $this->endSection() ?>

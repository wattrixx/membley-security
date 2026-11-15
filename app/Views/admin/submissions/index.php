<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<header class="admin-page-heading">
    <span class="admin-eyebrow">Submission management</span>
    <h1><?= esc($title) ?></h1>
    <p>Search and review records available to your role. Filters are kept in your session, not in the URL.</p>
</header>
<?php if (! empty($message)): ?><div class="admin-alert admin-alert-success" role="status"><?= esc($message) ?></div><?php endif; ?>
<?php if (! empty($error)): ?><div class="admin-alert admin-alert-error" role="alert"><?= esc($error) ?></div><?php endif; ?>
<form class="admin-form admin-form-card submission-panel" method="post" action="<?= url_to($route . '.filter') ?>">
    <?= csrf_field() ?>
    <div class="admin-form-grid">
        <div><label for="search">Search</label>
            <input id="search" name="q" type="search" maxlength="180" value="<?= esc($filters['q'] ?? '', 'attr') ?>" placeholder="<?= $entity === 'application' ? 'Name or reference' : ($entity === 'security_enquiry' ? 'Name, organisation or SEC reference' : 'Name or subject') ?>">
        </div>
        <div><label for="status">Status</label>
            <select id="status" name="status">
                <option value="">All statuses</option>
                <?php foreach ($statuses as $value => $label): ?>
                    <option value="<?= esc($value, 'attr') ?>" <?= ($filters['status'] ?? '') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php if ($entity === 'application'): ?>
        <label for="course">Course</label>
        <select id="course" name="course_id">
            <option value="">All courses</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= esc($course['id'], 'attr') ?>" <?= ($filters['course_id'] ?? '') === (string) $course['id'] ? 'selected' : '' ?>><?= esc($course['title']) ?></option>
            <?php endforeach; ?>
        </select>
    <?php elseif ($entity === 'security_enquiry'): ?>
        <label for="category">Service category</label>
        <select id="category" name="service_category">
            <option value="">All services</option>
            <?php foreach (['personal_protection', 'investigations', 'event_support', 'premises_review', 'other'] as $category): ?>
                <option value="<?= esc($category, 'attr') ?>" <?= ($filters['service_category'] ?? '') === $category ? 'selected' : '' ?>><?= esc(ucwords(str_replace('_', ' ', $category))) ?></option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>
    <div class="admin-form-grid">
        <div><label for="from">From</label><input id="from" name="from" type="date" value="<?= esc($filters['from'] ?? '', 'attr') ?>"></div>
        <div><label for="to">To</label><input id="to" name="to" type="date" value="<?= esc($filters['to'] ?? '', 'attr') ?>"></div>
    </div>
    <button class="admin-button admin-button-dark" type="submit">Filter</button>
</form>
<?php if ($records === []): ?>
    <section class="admin-empty-state"><h2>No records found</h2><p>No submissions match the current filters.</p></section>
<?php else: ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <caption><?= esc($title) ?></caption>
            <thead><tr><th>Name</th><th><?= $entity === 'application' ? 'Course / reference' : ($entity === 'security_enquiry' ? 'Service / reference' : 'Subject') ?></th><th>Status</th><th>Submitted</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($records as $record): ?>
                <tr>
                    <td><?= esc($record[$nameField]) ?></td>
                    <td>
                        <?php if ($entity === 'application'): ?>
                            <?= esc($record['course_title'] ?? 'Course unavailable') ?><span class="admin-cell-secondary"><?= esc($record['application_number']) ?></span>
                        <?php elseif ($entity === 'security_enquiry'): ?>
                            <?= esc(ucwords(str_replace('_', ' ', $record['service_category']))) ?><span class="admin-cell-secondary">SEC-<?= esc($record['id']) ?></span>
                        <?php else: ?><?= esc($record['subject'] ?? '') ?><?php endif; ?>
                    </td>
                    <td><?= esc($statuses[$record['status']] ?? $record['status']) ?></td>
                    <td><?= esc($record['created_at'] ?? '—') ?></td>
                    <td><a class="admin-button admin-button-light admin-button-small" href="<?= url_to($route . '.show', $record['id']) ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= $pagination ?>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<header class="admin-page-heading">
    <span class="admin-eyebrow">Submission management</span>
    <h1><?= esc($title) ?></h1>
    <a class="admin-card-link" href="<?= url_to($route) ?>">Back to list</a>
</header>
<?php if (! empty($message)): ?><div class="admin-alert admin-alert-success" role="status"><?= esc($message) ?></div><?php endif; ?>
<?php if (! empty($error)): ?><div class="admin-alert admin-alert-error" role="alert"><?= esc($error) ?></div><?php endif; ?>
<section class="admin-form-card submission-panel">
    <h2>Submission details</h2>
    <dl>
        <?php
        $fields = match ($entity) {
            'application' => ['application_number' => 'Reference', 'full_name' => 'Full name', 'email' => 'Email', 'phone' => 'Phone', 'country' => 'Country', 'course_title' => 'Course', 'message' => 'Additional information'],
            'college_enquiry' => ['name' => 'Name', 'email' => 'Email', 'phone' => 'Phone', 'subject' => 'Subject', 'message' => 'Message'],
            default => ['contact_name' => 'Contact name', 'organization' => 'Organisation', 'email' => 'Email', 'phone' => 'Phone', 'service_category' => 'Service category', 'message' => 'Message'],
        };
        ?>
        <?php if ($entity === 'security_enquiry'): ?><dt>Reference</dt><dd>SEC-<?= esc($record['id']) ?></dd><?php endif; ?>
        <?php foreach ($fields as $field => $label): ?>
            <dt><?= esc($label) ?></dt><dd><?= nl2br(esc((string) ($record[$field] ?? '—'))) ?></dd>
        <?php endforeach; ?>
        <dt>Submitted</dt><dd><?= esc($record['created_at'] ?? '—') ?></dd>
        <dt>Privacy notice acknowledged</dt><dd><?= ! empty($record['privacy_accepted']) ? 'Yes' : 'Not recorded' ?></dd>
        <dt>Status</dt><dd><?= esc($statuses[$record['status']] ?? $record['status']) ?></dd>
    </dl>
</section>
<?php if ($canManage): ?>
    <section class="admin-form-card submission-panel">
        <h2>Update status</h2>
        <form class="admin-form" action="<?= url_to($route . '.status', $record['id']) ?>" method="post">
            <?= csrf_field() ?>
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <?php foreach ($statuses as $value => $label): ?><option value="<?= esc($value, 'attr') ?>" <?= $record['status'] === $value ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach; ?>
            </select>
            <button class="admin-button admin-button-dark" type="submit">Save status</button>
        </form>
    </section>
    <section class="admin-form-card submission-panel">
        <h2>Add internal note</h2>
        <form class="admin-form" action="<?= url_to($route . '.note', $record['id']) ?>" method="post">
            <?= csrf_field() ?>
            <label for="note">Note</label><textarea id="note" name="note" maxlength="5000" required></textarea>
            <label for="followUp">Follow-up date (optional)</label><input id="followUp" name="follow_up_at" type="date">
            <button class="admin-button admin-button-dark" type="submit">Add note</button>
        </form>
    </section>
<?php endif; ?>
<section class="admin-form-card submission-panel">
    <h2>Internal notes</h2>
    <?php if ($notes === []): ?><p>No notes yet.</p><?php endif; ?>
    <?php foreach ($notes as $note): ?>
        <article>
            <p><strong><?= esc($note['username'] ?? 'Former administrator') ?></strong> &middot; <?= esc($note['created_at']) ?><?php if ($note['follow_up_at'] !== null): ?> &middot; Follow-up: <?= esc($note['follow_up_at']) ?><?php endif; ?></p>
            <p><?= nl2br(esc($note['note'])) ?></p>
        </article>
    <?php endforeach; ?>
</section>
<section class="admin-form-card submission-panel">
    <h2>Administrative history</h2>
    <?php if ($history === []): ?><p>No administrative activity yet.</p><?php endif; ?>
    <?php foreach ($history as $event): ?>
        <p><?= esc($event['created_at']) ?> &middot;
            <?php if ($event['action'] === 'admin.submission_status_changed'):
                parse_str(str_replace(';', '&', $event['details'] ?? ''), $change); ?>
                <?= esc($event['username'] ?? 'Former administrator') ?> &middot; Status: <?= esc($statuses[$change['from'] ?? ''] ?? ($change['from'] ?? 'Unknown')) ?> &rarr; <?= esc($statuses[$change['to'] ?? ''] ?? ($change['to'] ?? 'Unknown')) ?>
            <?php elseif ($event['action'] === 'admin.submission_note_added'): ?>
                <?= esc($event['username'] ?? 'Former administrator') ?> &middot; Internal note added
            <?php else: ?>Submission received<?php endif; ?>
        </p>
    <?php endforeach; ?>
</section>
<?= $this->endSection() ?>

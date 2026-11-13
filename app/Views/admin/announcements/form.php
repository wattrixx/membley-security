<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<header class="admin-page-heading">
    <span class="admin-eyebrow">Content management</span>
    <h1><?= $isEditing ? 'Edit announcement' : 'Create announcement' ?></h1>
    <p>Announcement text is published as plain text; HTML and scripts are not accepted.</p>
</header>

<?php if ($errors !== []): ?>
    <div class="admin-alert admin-alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form class="admin-form admin-form-card admin-form-wide" method="post"
      action="<?= $isEditing ? url_to('admin.cms.announcements.update', $record['id']) : url_to('admin.cms.announcements.store') ?>">
    <?= csrf_field() ?>
    <label for="announcementTitle">Title</label>
    <input id="announcementTitle" name="title" maxlength="220" required value="<?= esc($record['title'], 'attr') ?>">

    <label for="announcementSlug">URL slug <span class="admin-form-hint-inline">Generated from the title if blank</span></label>
    <input id="announcementSlug" name="slug" maxlength="240" value="<?= esc($record['slug'], 'attr') ?>">

    <label for="announcementContent">Content</label>
    <textarea id="announcementContent" name="content" rows="9" maxlength="50000" required><?= esc($record['content']) ?></textarea>

    <label for="announcementImage">Featured image (optional)</label>
    <select id="announcementImage" name="featured_media_id">
        <option value="">No image</option>
        <?php foreach ($media as $item): ?>
            <option value="<?= esc((string) $item['id'], 'attr') ?>" <?= (string) $record['featured_media_id'] === (string) $item['id'] ? 'selected' : '' ?>>
                <?= esc($item['original_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="announcementStatus">Status</label>
    <select id="announcementStatus" name="status" required>
        <?php foreach (['draft', 'published', 'archived'] as $state): ?>
            <option value="<?= esc($state, 'attr') ?>" <?= $record['status'] === $state ? 'selected' : '' ?>><?= esc(ucfirst($state)) ?></option>
        <?php endforeach; ?>
    </select>

    <div class="admin-form-grid">
        <div>
            <label for="announcementStarts">Display starts at (UTC, optional)</label>
            <input id="announcementStarts" name="starts_at" type="datetime-local"
                   value="<?= esc(str_replace(' ', 'T', substr((string) $record['starts_at'], 0, 16)), 'attr') ?>">
        </div>
        <div>
            <label for="announcementEnds">Display ends at (UTC, optional)</label>
            <input id="announcementEnds" name="ends_at" type="datetime-local"
                   value="<?= esc(str_replace(' ', 'T', substr((string) $record['ends_at'], 0, 16)), 'attr') ?>">
        </div>
    </div>
    <p class="admin-form-hint">The end date must be after the start date. Leave either blank for an open-ended window.</p>

    <label for="announcementPublishedAt">Publish at (UTC)</label>
    <input id="announcementPublishedAt" name="published_at" type="datetime-local"
           value="<?= esc(str_replace(' ', 'T', substr((string) $record['published_at'], 0, 16)), 'attr') ?>">
    <p class="admin-form-hint">A future time schedules publication. Leave blank to publish immediately when status is Published.</p>

    <div class="admin-form-actions">
        <button class="admin-button admin-button-primary" type="submit"><?= $isEditing ? 'Save announcement' : 'Create announcement' ?></button>
        <button class="admin-button admin-button-light" type="submit"
                formaction="<?= url_to('admin.cms.announcements.preview') ?>" formmethod="post" formtarget="_blank">Preview</button>
        <a class="admin-button admin-button-light" href="<?= url_to('admin.cms.announcements') ?>">Cancel</a>
    </div>
</form>
<?= $this->endSection() ?>

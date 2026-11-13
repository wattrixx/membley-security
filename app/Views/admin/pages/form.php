<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<header class="admin-page-heading">
    <span class="admin-eyebrow">Fixed page: /<?= esc($slug) ?></span>
    <h1>Edit <?= esc($record['title']) ?></h1>
    <p>The route is locked. Content is plain text; executable code and arbitrary HTML cannot be published.</p>
</header>

<?php if ($errors !== []): ?>
    <div class="admin-alert admin-alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form class="admin-form admin-form-card admin-form-wide" method="post"
      action="<?= url_to('admin.cms.pages.update', $slug) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="slug" value="<?= esc($slug, 'attr') ?>">

    <label for="pageTitle">Page title</label>
    <input id="pageTitle" name="title" maxlength="220" required value="<?= esc($record['title'], 'attr') ?>">

    <label for="pageContent">Page content</label>
    <textarea id="pageContent" name="content" rows="14" maxlength="50000" required><?= esc($record['content']) ?></textarea>

    <label for="pageImage">Featured image (optional)</label>
    <select id="pageImage" name="featured_media_id">
        <option value="">No image</option>
        <?php foreach ($media as $item): ?>
            <option value="<?= esc((string) $item['id'], 'attr') ?>" <?= (string) ($record['featured_media_id'] ?? '') === (string) $item['id'] ? 'selected' : '' ?>>
                <?= esc($item['original_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <div class="admin-form-grid">
        <div>
            <label for="pageMetaTitle">SEO title</label>
            <input id="pageMetaTitle" name="meta_title" maxlength="220" value="<?= esc($record['meta_title'], 'attr') ?>">
        </div>
        <div>
            <label for="pageMetaDescription">SEO description</label>
            <textarea id="pageMetaDescription" name="meta_description" rows="3" maxlength="300"><?= esc($record['meta_description']) ?></textarea>
        </div>
    </div>

    <label for="pageStatus">Status</label>
    <select id="pageStatus" name="status" required>
        <?php foreach (['draft', 'published', 'archived'] as $state): ?>
            <option value="<?= esc($state, 'attr') ?>" <?= $record['status'] === $state ? 'selected' : '' ?>><?= esc(ucfirst($state)) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="pagePublishedAt">Publish at (UTC)</label>
    <input id="pagePublishedAt" name="published_at" type="datetime-local"
           value="<?= esc(str_replace(' ', 'T', substr((string) $record['published_at'], 0, 16)), 'attr') ?>">
    <p class="admin-form-hint">A future time schedules publication. Until a page is published, its existing public template remains visible.</p>

    <div class="admin-form-actions">
        <button class="admin-button admin-button-primary" type="submit">Save page</button>
        <button class="admin-button admin-button-light" type="submit"
                formaction="<?= url_to('admin.cms.pages.preview') ?>" formmethod="post" formtarget="_blank">Preview</button>
        <a class="admin-button admin-button-light" href="<?= url_to('admin.cms.pages') ?>">Cancel</a>
    </div>
</form>
<?= $this->endSection() ?>

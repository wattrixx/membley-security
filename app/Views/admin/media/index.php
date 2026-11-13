<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<header class="admin-page-heading">
    <span class="admin-eyebrow">Content management</span>
    <h1>Media library</h1>
    <p>JPEG, PNG and WebP images up to 5 MB are accepted. Uploads receive generated filenames and are not executable.</p>
</header>

<?php if (! empty($message)): ?><div class="admin-alert admin-alert-success" role="status"><?= esc($message) ?></div><?php endif; ?>
<?php if (! empty($error)): ?><div class="admin-alert admin-alert-error" role="alert"><?= esc($error) ?></div><?php endif; ?>

<?php if (auth()->user()->can('content.media.manage')): ?>
    <form class="admin-form admin-form-card admin-media-upload" method="post"
          action="<?= url_to('admin.cms.media.upload') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <h2>Upload an image</h2>
        <label for="mediaFile">Image file</label>
        <input id="mediaFile" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
        <label for="mediaAltText">Alternative text</label>
        <input id="mediaAltText" name="alt_text" maxlength="255">
        <button class="admin-button admin-button-primary" type="submit">Upload image</button>
    </form>
<?php endif; ?>

<?php if ($records === []): ?>
    <section class="admin-empty-state"><h2>No media uploaded</h2><p>Uploaded images will be available to authorized content and course editors.</p></section>
<?php else: ?>
    <div class="admin-media-grid">
        <?php foreach ($records as $record): ?>
            <article class="admin-media-card">
                <img src="<?= esc(base_url($record['file_path']), 'attr') ?>"
                     alt="<?= esc($record['alt_text'] ?? '', 'attr') ?>"
                     loading="lazy" decoding="async">
                <div class="admin-media-details">
                    <strong><?= esc($record['original_name']) ?></strong>
                    <small><?= esc($record['mime_type']) ?> · <?= esc(number_format((int) $record['file_size'] / 1024)) ?> KB</small>
                    <?php if (auth()->user()->can('content.media.manage')): ?>
                        <form class="admin-form admin-media-alt-form" method="post"
                              action="<?= url_to('admin.cms.media.alt', $record['id']) ?>">
                            <?= csrf_field() ?>
                            <label for="alt-<?= esc((string) $record['id'], 'attr') ?>">Alternative text</label>
                            <input id="alt-<?= esc((string) $record['id'], 'attr') ?>" name="alt_text" maxlength="255"
                                   value="<?= esc($record['alt_text'] ?? '', 'attr') ?>">
                            <button class="admin-button admin-button-light admin-button-small" type="submit">Save alt text</button>
                        </form>
                        <form method="post" action="<?= url_to('admin.cms.media.delete', $record['id']) ?>">
                            <?= csrf_field() ?>
                            <button class="admin-button admin-button-danger admin-button-small" type="submit">Delete image</button>
                        </form>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?= $pager->links('cmsMedia') ?>
<?php endif; ?>
<?= $this->endSection() ?>

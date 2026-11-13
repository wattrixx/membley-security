<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<header class="admin-page-heading">
    <span class="admin-eyebrow">Content management</span>
    <h1><?= $isEditing ? 'Edit article' : 'Create article' ?></h1>
    <p>Article body content is plain text; HTML and scripts are not published.</p>
</header>

<?php if ($errors !== []): ?>
    <div class="admin-alert admin-alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form class="admin-form admin-form-card admin-form-wide" method="post"
      action="<?= $isEditing ? url_to('admin.cms.news.update', $record['id']) : url_to('admin.cms.news.store') ?>">
    <?= csrf_field() ?>
    <label for="newsTitle">Article title</label>
    <input id="newsTitle" name="title" maxlength="220" required value="<?= esc($record['title'], 'attr') ?>">

    <label for="newsSlug">URL slug <span class="admin-form-hint-inline">Generated from the title if blank</span></label>
    <input id="newsSlug" name="slug" maxlength="240" value="<?= esc($record['slug'], 'attr') ?>">

    <div class="admin-form-grid">
        <div>
            <label for="newsCategory">Category</label>
            <input id="newsCategory" name="category" maxlength="150" value="<?= esc($record['category'], 'attr') ?>">
        </div>
        <div>
            <label for="newsAuthor">Author</label>
            <input id="newsAuthor" value="<?= esc($record['author_name'], 'attr') ?>" disabled>
            <p class="admin-form-hint">The signed-in administrator is recorded as the author on creation.</p>
        </div>
    </div>

    <label for="newsExcerpt">Excerpt</label>
    <textarea id="newsExcerpt" name="excerpt" rows="3" maxlength="5000"><?= esc($record['excerpt']) ?></textarea>

    <label for="newsContent">Article content</label>
    <textarea id="newsContent" name="content" rows="12" maxlength="50000" required><?= esc($record['content']) ?></textarea>

    <label for="newsImage">Featured image</label>
    <select id="newsImage" name="featured_media_id">
        <option value="">No image</option>
        <?php foreach ($media as $item): ?>
            <option value="<?= esc((string) $item['id'], 'attr') ?>" <?= (string) $record['featured_media_id'] === (string) $item['id'] ? 'selected' : '' ?>>
                <?= esc($item['original_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <div class="admin-form-grid">
        <div>
            <label for="newsMetaTitle">SEO title</label>
            <input id="newsMetaTitle" name="meta_title" maxlength="220" value="<?= esc($record['meta_title'], 'attr') ?>">
        </div>
        <div>
            <label for="newsMetaDescription">SEO description</label>
            <textarea id="newsMetaDescription" name="meta_description" rows="3" maxlength="300"><?= esc($record['meta_description']) ?></textarea>
        </div>
    </div>

    <label for="newsStatus">Publication status</label>
    <select id="newsStatus" name="status" required>
        <?php foreach (['draft', 'published', 'archived'] as $state): ?>
            <option value="<?= esc($state, 'attr') ?>" <?= $record['status'] === $state ? 'selected' : '' ?>><?= esc(ucfirst($state)) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="newsPublishedAt">Publish at (UTC)</label>
    <input id="newsPublishedAt" name="published_at" type="datetime-local"
           value="<?= esc(str_replace(' ', 'T', substr((string) $record['published_at'], 0, 16)), 'attr') ?>">
    <p class="admin-form-hint">A future time schedules publication. Leave blank to publish immediately when status is Published.</p>

    <div class="admin-form-actions">
        <button class="admin-button admin-button-primary" type="submit"><?= $isEditing ? 'Save article' : 'Create article' ?></button>
        <button class="admin-button admin-button-light" type="submit"
                formaction="<?= url_to('admin.cms.news.preview') ?>" formmethod="post" formtarget="_blank">Preview</button>
        <a class="admin-button admin-button-light" href="<?= url_to('admin.cms.news') ?>">Cancel</a>
    </div>
</form>
<?= $this->endSection() ?>

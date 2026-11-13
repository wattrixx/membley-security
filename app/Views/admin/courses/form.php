<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<header class="admin-page-heading">
    <span class="admin-eyebrow">College administration</span>
    <h1><?= $isEditing ? 'Edit course' : 'Create course' ?></h1>
    <p>Keep proposed or in-development courses clearly labelled. Enter a fee only after it has been confirmed.</p>
</header>

<?php if ($errors !== []): ?>
    <div class="admin-alert admin-alert-error" role="alert">
        <ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form class="admin-form admin-form-card admin-form-wide" method="post"
      action="<?= $isEditing ? url_to('admin.cms.courses.update', $record['id']) : url_to('admin.cms.courses.store') ?>">
    <?= csrf_field() ?>
    <label for="courseTitle">Course title</label>
    <input id="courseTitle" name="title" maxlength="200" required value="<?= esc($record['title'], 'attr') ?>">

    <label for="courseSlug">URL slug <span class="admin-form-hint-inline">Generated from the title if blank</span></label>
    <input id="courseSlug" name="slug" maxlength="220" value="<?= esc($record['slug'], 'attr') ?>">

    <label for="courseShort">Short description</label>
    <textarea id="courseShort" name="short_description" rows="3" maxlength="2000"><?= esc($record['short_description']) ?></textarea>

    <label for="courseDescription">Course description</label>
    <textarea id="courseDescription" name="description" rows="8" maxlength="50000"><?= esc($record['description']) ?></textarea>

    <label for="courseRequirements">Requirements</label>
    <textarea id="courseRequirements" name="requirements" rows="4" maxlength="20000"><?= esc($record['requirements']) ?></textarea>

    <label for="courseOutcomes">Learning outcomes</label>
    <textarea id="courseOutcomes" name="outcomes" rows="4" maxlength="20000"><?= esc($record['outcomes']) ?></textarea>

    <div class="admin-form-grid">
        <div>
            <label for="courseCategory">Category</label>
            <select id="courseCategory" name="category_id">
                <option value="">No category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= esc((string) $category['id'], 'attr') ?>"
                        <?= (string) $record['category_id'] === (string) $category['id'] ? 'selected' : '' ?>>
                        <?= esc($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($categories === []): ?><p class="admin-form-hint">No course categories are configured yet.</p><?php endif; ?>
        </div>
        <div>
            <label for="courseDuration">Duration</label>
            <input id="courseDuration" name="duration" maxlength="100" value="<?= esc($record['duration'], 'attr') ?>">
        </div>
    </div>

    <label for="courseAvailability">Availability</label>
    <select id="courseAvailability" name="status" required>
        <?php foreach (['available' => 'Available', 'proposed' => 'Proposed', 'development' => 'In development'] as $value => $label): ?>
            <option value="<?= esc($value, 'attr') ?>" <?= $record['status'] === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="coursePublication">Publication state</label>
    <select id="coursePublication" name="publication_status" required>
        <?php foreach (['draft', 'published', 'archived'] as $state): ?>
            <option value="<?= esc($state, 'attr') ?>" <?= $record['publication_status'] === $state ? 'selected' : '' ?>><?= esc(ucfirst($state)) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="coursePublishedAt">Publish at (UTC)</label>
    <input id="coursePublishedAt" name="published_at" type="datetime-local"
           value="<?= esc(str_replace(' ', 'T', substr((string) $record['published_at'], 0, 16)), 'attr') ?>">
    <p class="admin-form-hint">A future time schedules publication. Leave blank to publish immediately when the state is Published.</p>

    <label for="courseFee">Course fee (optional)</label>
    <input id="courseFee" name="fee" type="number" min="0" step="0.01" value="<?= esc((string) ($record['fee'] ?? ''), 'attr') ?>">
    <label class="admin-checkbox-label">
        <input type="checkbox" name="fee_verified" value="1" <?= ! empty($record['fee_verified']) ? 'checked' : '' ?>>
        This fee has been confirmed and approved for publication
    </label>

    <label for="courseImage">Featured image</label>
    <select id="courseImage" name="featured_media_id">
        <option value="">No image</option>
        <?php foreach ($media as $item): ?>
            <option value="<?= esc((string) $item['id'], 'attr') ?>"
                <?= (string) $record['featured_media_id'] === (string) $item['id'] ? 'selected' : '' ?>>
                <?= esc($item['original_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if ($media === []): ?><p class="admin-form-hint">Upload images through the Media Library before selecting one here.</p><?php endif; ?>

    <label class="admin-checkbox-label">
        <input type="checkbox" name="is_featured" value="1" <?= ! empty($record['is_featured']) ? 'checked' : '' ?>>
        Feature this course on the homepage
    </label>

    <div class="admin-form-actions">
        <button class="admin-button admin-button-primary" type="submit"><?= $isEditing ? 'Save course' : 'Create course' ?></button>
        <button class="admin-button admin-button-light" type="submit"
                formaction="<?= url_to('admin.cms.courses.preview') ?>" formmethod="post" formtarget="_blank">Preview</button>
        <a class="admin-button admin-button-light" href="<?= url_to('admin.cms.courses') ?>">Cancel</a>
    </div>
</form>
<?= $this->endSection() ?>

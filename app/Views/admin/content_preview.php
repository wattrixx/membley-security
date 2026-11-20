<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<header class="admin-page-heading">
    <span class="admin-eyebrow">Unsaved preview</span>
    <h1><?= esc($previewTitle) ?></h1>
    <?php if (! empty($previewExcerpt)): ?><p><?= esc($previewExcerpt) ?></p><?php endif; ?>
</header>

<section class="admin-preview-card">
    <?php if (! empty($previewImage)): ?>
        <img class="admin-preview-image" src="<?= esc(base_url($previewImage), 'attr') ?>" alt="">
    <?php endif; ?>
    <?php if (! empty($previewMetadata)): ?>
        <dl class="admin-preview-metadata">
            <?php foreach ($previewMetadata as $label => $value): ?>
                <?php if ($value !== null && $value !== ''): ?>
                    <div><dt><?= esc($label) ?></dt><dd><?= esc((string) $value) ?></dd></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </dl>
    <?php endif; ?>
    <?php if (! empty($previewContent)): ?>
        <div class="admin-preview-copy">
            <?php if (! empty($previewTransportContent)): ?>
                <?= view('partials/transport_content', ['content' => $previewContent]) ?>
            <?php else: ?>
                <?= nl2br(esc($previewContent)) ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <a class="admin-button admin-button-light" href="<?= url_to('admin.dashboard') ?>">Return to dashboard</a>
</section>
<?= $this->endSection() ?>

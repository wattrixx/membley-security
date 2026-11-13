<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section class="admin-empty-state">
    <span class="admin-eyebrow">Future module</span>
    <h1><?= esc($moduleTitle) ?></h1>
    <p>This module is not implemented yet. No records are displayed or changed here.</p>
    <a class="admin-button admin-button-dark" href="<?= site_url('admin') ?>">Return to dashboard</a>
</section>
<?= $this->endSection() ?>

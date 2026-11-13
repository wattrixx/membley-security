<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section class="admin-empty-state">
    <span class="admin-eyebrow">Access restricted</span>
    <h1>You do not have permission to view this area.</h1>
    <p><?= esc($error ?? 'Ask a Super Admin if you believe this access is required.') ?></p>
    <a class="admin-button admin-button-dark" href="<?= site_url('admin') ?>">Return to dashboard</a>
</section>
<?= $this->endSection() ?>

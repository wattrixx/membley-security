<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="section">
    <div class="container submission-confirmation">
        <span class="eyebrow">Form submission</span>
        <h1><?= esc($pageTitle) ?></h1>
        <p><?= esc($message) ?></p>
        <a class="button button-dark" href="<?= base_url('contact') ?>">Contact the team</a>
    </div>
</section>
<?= $this->endSection() ?>

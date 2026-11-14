<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="section">
    <div class="container submission-confirmation">
        <?php if ($confirmation !== []): ?>
            <span class="eyebrow">Submission received</span>
            <h1>Thank you. Your submission has been received.</h1>
            <?php if (($confirmation['type'] ?? '') === 'application' && ! empty($confirmation['reference'])): ?>
                <p>Your application reference is <strong><?= esc($confirmation['reference']) ?></strong>. Keep it for your records.</p>
            <?php else: ?>
                <p>The relevant team will review your message. This page does not display submitted contact details.</p>
            <?php endif; ?>
        <?php else: ?>
            <span class="eyebrow">Submission status</span>
            <h1>No active confirmation is available.</h1>
            <p>If you recently submitted a form, avoid sending it again. You can contact the relevant team using the verified phone number.</p>
        <?php endif; ?>
        <div class="section-cta">
            <a class="button button-dark" href="<?= base_url('/') ?>">Return home</a>
            <a class="text-link" href="tel:+254704084328">Call +254 704 084 328</a>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

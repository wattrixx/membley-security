<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Contact</span>
        <h1><?= esc($contactTitle) ?></h1>
        <p><?= esc($contactDescription) ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="content-grid contact-options">
            <?php foreach ($contacts as $contact): ?>
                <article class="content-panel">
                    <span class="eyebrow"><?= esc($contact['purpose']) ?></span>
                    <h2><?= esc($contact['organisation']) ?></h2>
                    <p>Call the shared contact number and ask for this team.</p>
                    <p>
                        <a class="contact-direct" href="tel:<?= esc($phone, 'attr') ?>">
                            <?= esc($phoneDisplay) ?>
                        </a>
                    </p>
                    <p>
                        <a class="text-link"
                           href="<?= esc($mapUrl, 'attr') ?>"
                           target="_blank"
                           rel="noopener noreferrer">Open location in Google Maps</a>
                    </p>
                    <a class="button button-dark" href="<?= esc(base_url($contact['path']), 'attr') ?>">
                        Visit this section
                    </a>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="placeholder-note contact-notice">
            <p>
                Online enquiry and application forms are not yet available.
                Please call the relevant team for current information. No
                email address or separate team phone number has been supplied.
            </p>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

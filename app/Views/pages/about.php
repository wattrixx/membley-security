<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <span class="eyebrow">About the Group</span>
        <h1>One group. Two specialist organisations.</h1>
        <p>
            Membley Security Group is the shared website brand for Membley
            College of Security Studies and Membley Investigations and
            Surveillance Company.
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <span class="eyebrow">Our organisations</span>
            <h2>Choose the team that fits your next step.</h2>
            <p>
                The College handles courses, applications and employer
                training. The Security Services company handles investigation,
                protection and security-service enquiries.
            </p>
        </div>

        <div class="content-grid">
            <article class="content-panel">
                <span class="eyebrow">Professional learning</span>
                <h3>Membley College of Security Studies</h3>
                <p>
                    Explore professional development for security roles,
                    serving officers and employers. Course availability and
                    intake information are confirmed by Admissions.
                </p>
                <a class="button button-dark" href="<?= base_url('college') ?>">Explore the College</a>
            </article>

            <article class="content-panel">
                <span class="eyebrow">Security services</span>
                <h3>Membley Investigations and Surveillance Company</h3>
                <p>
                    Discuss an investigation, protection requirement, event
                    or premises concern with the team responsible for security
                    service enquiries.
                </p>
                <a class="button button-dark" href="<?= base_url('security-services') ?>">Explore Security Services</a>
            </article>
        </div>
    </div>
</section>

<section class="section section-paper">
    <div class="container content-grid">
        <div class="content-panel">
            <h2>Our Vision</h2>
            <p><?= esc(\App\Content\AboutContent::VISION) ?></p>
        </div>
        <div class="content-panel">
            <h2>Our Mission</h2>
            <p><?= esc(\App\Content\AboutContent::MISSION) ?></p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <h2>Our Core Values</h2>
        </div>
        <div class="content-grid">
            <?php foreach (\App\Content\AboutContent::VALUES as $name => $description): ?>
                <article class="content-panel">
                    <h3><?= esc($name) ?></h3>
                    <p><?= esc($description) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-paper">
    <div class="container">
        <div class="section-heading">
            <h2>Our Company Background</h2>
            <p>About Membley Security &amp; College</p>
        </div>
        <?php foreach (\App\Content\AboutContent::BACKGROUND as $paragraph): ?>
            <p><?= esc($paragraph) ?></p>
        <?php endforeach; ?>
        <h3>Truth. Intelligence. Discretion.</h3>
        <p>These words appear in the Group emblem.</p>
    </div>
</section>

<section class="section">
    <div class="container section-cta">
        <div>
            <span class="eyebrow">Get in touch</span>
            <h2>Speak with the relevant team.</h2>
        </div>
        <a class="button button-primary" href="<?= base_url('contact') ?>">Contact Membley</a>
    </div>
</section>

<?= $this->endSection() ?>

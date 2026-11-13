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
        <div>
            <span class="eyebrow">The emblem</span>
            <h2>Truth. Intelligence. Discretion.</h2>
            <p>
                These words appear in the supplied Group emblem. They are
                presented here as part of its visual identity, not as a
                substitute for formally approved values.
            </p>
        </div>
        <div class="content-panel">
            <h3>Company background and statements</h3>
            <p>
                Approved company history, vision and mission statements have
                not been provided for publication. They can be added here
                after confirmation.
            </p>
        </div>
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

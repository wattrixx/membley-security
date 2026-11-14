<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Membley Investigations and Surveillance Company</span>
        <h1>Discuss the security support your organisation needs.</h1>
        <p>
            Explain the situation, what you need to establish or arrange, and
            when support may be required. The team can discuss the request and
            confirm whether it fits its service scope.
        </p>
        <a class="button button-primary" href="<?= base_url('security-services/contact') ?>">Contact Security Services</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <span class="eyebrow">Service enquiries</span>
            <h2>Start with a conversation about the situation.</h2>
            <p>
                Service scope, availability, fees and engagement arrangements
                are confirmed with the company before any work begins.
            </p>
        </div>

        <div class="content-grid">
            <article class="content-panel">
                <span class="eyebrow">Protection and travel</span>
                <h3>Personal protection and secure travel</h3>
                <p>
                    Ask about support for an executive visit, scheduled
                    engagement or transport requirement. The company will
                    confirm the scope and coordination arrangements for each
                    request.
                </p>
            </article>

            <article class="content-panel">
                <span class="eyebrow">Investigations</span>
                <h3>Business and workplace enquiries</h3>
                <p>
                    Discuss a concern such as suspected fraud, theft,
                    misconduct or recurring loss. The company can review the
                    information available and agree an appropriate scope
                    before an engagement.
                </p>
            </article>

            <article class="content-panel">
                <span class="eyebrow">Events and premises</span>
                <h3>Event support and site reviews</h3>
                <p>
                    Ask about event coordination, venue concerns or a premises
                    risk assessment. The team will discuss the objectives,
                    location and reporting needs for a proposed review.
                </p>
            </article>
        </div>
    </div>
</section>

<section class="section section-paper">
    <div class="container section-cta">
        <div>
            <span class="eyebrow">Enquiries</span>
            <h2>Request a discussion with the Security Services team.</h2>
            <p>
                Send a general service enquiry. Do not include sensitive
                security information in the online form.
            </p>
        </div>
        <a class="button button-dark" href="<?= base_url('security-services/enquiry') ?>">Send a Security Services enquiry</a>
    </div>
</section>

<?= $this->endSection() ?>

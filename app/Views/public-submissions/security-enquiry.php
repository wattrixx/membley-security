<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Membley Investigations and Surveillance Company</span>
        <h1>Security Services enquiry</h1>
        <p>Share a general description of the service you are looking for. The team will confirm the appropriate scope and next steps.</p>
    </div>
</section>

<section class="section">
    <div class="container submission-layout">
        <div class="submission-main">
            <?= view('public-submissions/_errors', ['errors' => $errors]) ?>
            <form class="public-form" method="post" action="<?= base_url('security-services/enquiry') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="submission_token" value="<?= esc($submissionToken, 'attr') ?>">
                <div class="submission-honeypot" aria-hidden="true">
                    <label for="securityWebsite">Leave this field empty</label>
                    <input id="securityWebsite" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>

                <label for="securityName">Contact name <span aria-hidden="true">*</span></label>
                <input id="securityName" name="contact_name" type="text" maxlength="180" autocomplete="name" required
                       value="<?= esc($values['contact_name'] ?? '', 'attr') ?>">

                <label for="securityOrganization">Organization <span class="form-optional">(optional)</span></label>
                <input id="securityOrganization" name="organization" type="text" maxlength="200" autocomplete="organization"
                       value="<?= esc($values['organization'] ?? '', 'attr') ?>">

                <div class="submission-form-grid">
                    <div>
                        <label for="securityEmail">Email address <span aria-hidden="true">*</span></label>
                        <input id="securityEmail" name="email" type="email" maxlength="180" autocomplete="email" required
                               value="<?= esc($values['email'] ?? '', 'attr') ?>">
                    </div>
                    <div>
                        <label for="securityPhone">Phone number <span aria-hidden="true">*</span></label>
                        <input id="securityPhone" name="phone" type="tel" maxlength="50" autocomplete="tel" required
                               value="<?= esc($values['phone'] ?? '', 'attr') ?>">
                    </div>
                </div>

                <label for="securityCategory">Service category <span aria-hidden="true">*</span></label>
                <select id="securityCategory" name="service_category" required>
                    <option value="">Choose a service category</option>
                    <?php
                    $categories = [
                        'personal_protection' => 'Personal protection and secure travel',
                        'investigations'     => 'Business and workplace investigations',
                        'event_support'      => 'Event support',
                        'premises_review'    => 'Premises review',
                        'other'              => 'Other',
                    ];
                    ?>
                    <?php foreach ($categories as $value => $label): ?>
                        <option value="<?= esc($value, 'attr') ?>"
                            <?= ($values['service_category'] ?? '') === $value ? 'selected' : '' ?>>
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="securityMessage">General enquiry <span aria-hidden="true">*</span></label>
                <textarea id="securityMessage" name="message" rows="7" maxlength="5000" required><?= esc($values['message'] ?? '') ?></textarea>

                <div class="placeholder-note security-form-warning">
                    <p>Do not submit security plans, access codes, vulnerability reports, confidential client details or evidence through this form. For urgent matters, call <a href="tel:+254704084328">+254 704 084 328</a> to agree an appropriate contact method.</p>
                </div>

                <div class="privacy-consent">
                    <input id="securityPrivacy" name="privacy_accepted" type="checkbox" value="1" required
                        <?= ($values['privacy_accepted'] ?? '') === '1' ? 'checked' : '' ?>>
                    <label for="securityPrivacy">I have read the <a href="<?= base_url('privacy') ?>">Privacy Notice</a> and agree to the use of these details to respond to my enquiry.</label>
                </div>

                <button class="button button-dark" type="submit">Send Security Services enquiry</button>
            </form>
        </div>
        <aside class="submission-aside">
            <h2>Discuss your request</h2>
            <p>Service scope, availability and engagement arrangements are confirmed directly with the team.</p>
            <a class="contact-direct" href="tel:+254704084328">+254 704 084 328</a>
        </aside>
    </div>
</section>
<?= $this->endSection() ?>

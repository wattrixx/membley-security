<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Membley College of Security Studies</span>
        <h1>Ask Admissions</h1>
        <p>Use this form for questions about courses, entry requirements, intakes or employer training.</p>
    </div>
</section>

<section class="section">
    <div class="container submission-layout">
        <div class="submission-main">
            <?= view('public-submissions/_errors', ['errors' => $errors]) ?>
            <form class="public-form" method="post" action="<?= base_url('college/enquiry') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="submission_token" value="<?= esc($submissionToken, 'attr') ?>">
                <div class="submission-honeypot" aria-hidden="true">
                    <label for="collegeWebsite">Leave this field empty</label>
                    <input id="collegeWebsite" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>

                <label for="collegeName">Name <span aria-hidden="true">*</span></label>
                <input id="collegeName" name="name" type="text" maxlength="180" autocomplete="name" required
                       value="<?= esc($values['name'] ?? '', 'attr') ?>">

                <div class="submission-form-grid">
                    <div>
                        <label for="collegeEmail">Email address <span aria-hidden="true">*</span></label>
                        <input id="collegeEmail" name="email" type="email" maxlength="180" autocomplete="email" required
                               value="<?= esc($values['email'] ?? '', 'attr') ?>">
                    </div>
                    <div>
                        <label for="collegePhone">Phone number <span class="form-optional">(optional)</span></label>
                        <input id="collegePhone" name="phone" type="tel" maxlength="50" autocomplete="tel"
                               value="<?= esc($values['phone'] ?? '', 'attr') ?>">
                    </div>
                </div>

                <label for="collegeSubject">Subject <span aria-hidden="true">*</span></label>
                <input id="collegeSubject" name="subject" type="text" maxlength="220" required
                       value="<?= esc($values['subject'] ?? '', 'attr') ?>">

                <label for="collegeMessage">Enquiry <span aria-hidden="true">*</span></label>
                <textarea id="collegeMessage" name="message" rows="7" maxlength="10000" required><?= esc($values['message'] ?? '') ?></textarea>

                <div class="privacy-consent">
                    <input id="collegePrivacy" name="privacy_accepted" type="checkbox" value="1" required
                        <?= ($values['privacy_accepted'] ?? '') === '1' ? 'checked' : '' ?>>
                    <label for="collegePrivacy">I have read the <a href="<?= base_url('privacy') ?>">Privacy Notice</a> and agree to the use of these details to respond to my enquiry.</label>
                </div>

                <button class="button button-dark" type="submit">Send College enquiry</button>
            </form>
        </div>
        <aside class="submission-aside">
            <h2>Need to speak with Admissions?</h2>
            <p><a class="contact-direct" href="tel:+254704084328">+254 704 084 328</a></p>
            <p>Do not include identity documents or sensitive personal information in your enquiry.</p>
            <a class="text-link" href="<?= base_url('college/apply') ?>">Ready to apply? View available courses.</a>
        </aside>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Membley College of Security Studies</span>
        <h1>Apply for a course</h1>
        <p>Send your contact details and course of interest to the Admissions team. Intake dates and arrangements will be confirmed directly.</p>
    </div>
</section>

<section class="section">
    <div class="container submission-layout">
        <div class="submission-main">
            <?= view('public-submissions/_errors', ['errors' => $errors]) ?>
            <?php if ($courses === []): ?>
                <div class="empty-state">
                    <h2>Applications are not open yet</h2>
                    <p>There are no published courses currently accepting applications. Contact Admissions to ask about upcoming intakes.</p>
                    <a class="button button-dark" href="<?= base_url('college/enquiry') ?>">Ask Admissions</a>
                </div>
            <?php else: ?>
                <form class="public-form" method="post" action="<?= base_url('college/apply') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="submission_token" value="<?= esc($submissionToken, 'attr') ?>">
                    <div class="submission-honeypot" aria-hidden="true">
                        <label for="applicationWebsite">Leave this field empty</label>
                        <input id="applicationWebsite" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <label for="applicationName">Full name <span aria-hidden="true">*</span></label>
                    <input id="applicationName" name="full_name" type="text" maxlength="180" autocomplete="name" required
                           value="<?= esc($values['full_name'] ?? '', 'attr') ?>">

                    <div class="submission-form-grid">
                        <div>
                            <label for="applicationEmail">Email address <span aria-hidden="true">*</span></label>
                            <input id="applicationEmail" name="email" type="email" maxlength="180" autocomplete="email" required
                                   value="<?= esc($values['email'] ?? '', 'attr') ?>">
                        </div>
                        <div>
                            <label for="applicationPhone">Phone number <span aria-hidden="true">*</span></label>
                            <input id="applicationPhone" name="phone" type="tel" maxlength="50" autocomplete="tel" required
                                   value="<?= esc($values['phone'] ?? '', 'attr') ?>">
                        </div>
                    </div>

                    <label for="applicationCountry">Country <span aria-hidden="true">*</span></label>
                    <input id="applicationCountry" name="country" type="text" maxlength="100" autocomplete="country-name" required
                           value="<?= esc($values['country'] ?? '', 'attr') ?>">

                    <label for="applicationCourse">Course of interest <span aria-hidden="true">*</span></label>
                    <select id="applicationCourse" name="course_id" required>
                        <option value="">Select an available course</option>
                        <?php foreach ($courses as $course): ?>
                            <?php
                            $selectedId = (string) ($values['course_id'] ?? $selectedCourse['id'] ?? '');
                            ?>
                            <option value="<?= esc((string) $course['id'], 'attr') ?>"
                                <?= $selectedId === (string) $course['id'] ? 'selected' : '' ?>>
                                <?= esc($course['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="applicationMessage">Additional information <span class="form-optional">(optional)</span></label>
                    <textarea id="applicationMessage" name="message" rows="5" maxlength="2000"><?= esc($values['message'] ?? '') ?></textarea>

                    <div class="privacy-consent">
                        <input id="applicationPrivacy" name="privacy_accepted" type="checkbox" value="1" required
                            <?= ($values['privacy_accepted'] ?? '') === '1' ? 'checked' : '' ?>>
                        <label for="applicationPrivacy">I have read the <a href="<?= base_url('privacy') ?>">Privacy Notice</a> and agree to the use of these details to respond to my application.</label>
                    </div>

                    <button class="button button-dark" type="submit">Submit application</button>
                </form>
            <?php endif; ?>
        </div>

        <aside class="submission-aside">
            <h2>Before you apply</h2>
            <p>Only courses marked as available and published can be selected. Admissions will confirm intake availability and next steps.</p>
            <p>Do not include identity documents or other sensitive personal information in the additional information field.</p>
            <a class="text-link" href="<?= base_url('college/enquiry') ?>">Have a question? Send a College enquiry.</a>
        </aside>
    </div>
</section>
<?= $this->endSection() ?>

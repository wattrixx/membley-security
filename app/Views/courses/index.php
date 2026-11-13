<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Membley College of Security Studies</span>
        <h1>Courses and professional development.</h1>
        <p>
            Course availability, dates and assessment arrangements are
            confirmed by Admissions for each intake. Proposed or developing
            programmes are identified separately.
        </p>
    </div>
</section>

<?php if ($availableCourses === [] && $developmentCourses === []): ?>
    <section class="section">
        <div class="container">
            <div class="empty-state">
                <h2>No course listings are published yet.</h2>
                <p>
                    Contact the College to ask about current intakes and
                    programmes in development.
                </p>
                <a class="button button-dark" href="<?= base_url('college/contact') ?>">Ask Admissions</a>
            </div>
        </div>
    </section>
<?php else: ?>
    <?php if ($availableCourses !== []): ?>
        <section class="section">
            <div class="container">
                <div class="section-heading">
                    <span class="eyebrow">Published listings</span>
                    <h2>Available courses</h2>
                    <p>Ask Admissions to confirm the next intake details.</p>
                </div>
                <div class="content-grid">
                    <?php foreach ($availableCourses as $course): ?>
                        <?= view('partials/course_card', ['course' => $course]) ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($developmentCourses !== []): ?>
        <section class="section section-paper">
            <div class="container">
                <div class="section-heading">
                    <span class="eyebrow">Published proposals</span>
                    <h2>Proposed and in-development programmes</h2>
                    <p>
                        These listings are not open for application. Contact
                        Admissions to register interest.
                    </p>
                </div>
                <div class="content-grid">
                    <?php foreach ($developmentCourses as $course): ?>
                        <?= view('partials/course_card', ['course' => $course]) ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
<?php endif; ?>

<section class="section">
    <div class="container section-cta">
        <div>
            <span class="eyebrow">Admissions</span>
            <h2>Need help choosing a programme?</h2>
            <p>Ask the College about entry requirements and intake availability.</p>
        </div>
        <a class="button button-primary" href="<?= base_url('college/contact') ?>">Contact Admissions</a>
    </div>
</section>

<?= $this->endSection() ?>

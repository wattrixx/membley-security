<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Membley College of Security Studies</span>
        <h1>Build your skills for greater responsibility.</h1>
        <p>
            Explore training for people preparing to enter security, serving
            officers developing their skills and employers training their teams.
        </p>
        <div class="hero-actions">
            <a class="button button-primary" href="<?= base_url('college/courses') ?>">Explore Courses</a>
            <a class="button button-ghost" href="<?= base_url('college/contact') ?>">Ask Admissions</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container content-grid">
        <div>
            <span class="eyebrow">How we approach training</span>
            <h2>Connect learning with the work.</h2>
            <p>
                The College's short-course approach focuses on practical
                tasks. Exercises, case discussions and simulated workplace
                situations help learners connect each lesson with the
                responsibilities of a security role.
            </p>
        </div>
        <div class="content-panel">
            <h3>What learners can practise</h3>
            <p>
                Depending on the programme, learners may practise writing an
                incident report, preparing a team briefing, recording
                information accurately or supporting a client professionally.
            </p>
            <p>
                Course pages show intended learning outcomes, the audience and
                entry requirements when those details have been published.
            </p>
        </div>
    </div>
</section>

<section class="section section-paper">
    <div class="container">
        <div class="section-heading">
            <span class="eyebrow">For organisations</span>
            <h2>Training for your team.</h2>
            <p>
                Employers can discuss training for security officers, control
                room operators, supervisors and staff with workplace safety
                responsibilities. Share the roles involved and the skills
                your team wants to develop.
            </p>
        </div>
        <a class="button button-dark" href="<?= base_url('college/contact') ?>">Discuss Employer Training</a>
    </div>
</section>

<section class="section" id="admissions">
    <div class="container">
        <div class="section-heading">
            <span class="eyebrow">Admissions</span>
            <h2>Check the details before applying.</h2>
            <p>
                Course availability, dates and assessment arrangements are
                confirmed by Admissions for each intake. Application forms are
                not yet available on this website.
            </p>
        </div>
        <ol class="step-list">
            <li>Select a course and ask Admissions whether an intake is open.</li>
            <li>Confirm the entry requirements, schedule and final fee directly with the College.</li>
            <li>Follow the application instructions provided by Admissions.</li>
        </ol>
        <div class="section-cta">
            <a class="button button-dark" href="<?= base_url('college/courses') ?>">View Course Listings</a>
            <a class="button button-primary" href="<?= base_url('college/contact') ?>">Contact Admissions</a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <span class="eyebrow"><?= esc($course['status_label']) ?> · Membley College of Security Studies</span>
        <h1><?= esc($course['title']) ?></h1>
        <?php if (!empty($course['short_description'])): ?>
            <p><?= esc($course['short_description']) ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container detail-layout">
        <article class="detail-copy">
            <?php if ($course['featured_image_path'] !== null): ?>
                <img class="detail-image"
                     src="<?= esc(base_url($course['featured_image_path']), 'attr') ?>"
                     alt="<?= esc($course['featured_image_alt'], 'attr') ?>"
                     loading="lazy"
                     decoding="async">
            <?php endif; ?>

            <?php if (!empty($course['description'])): ?>
                <div class="prose-copy"><?= nl2br(esc($course['description'])) ?></div>
            <?php endif; ?>

            <?php if (!empty($course['requirements'])): ?>
                <section class="detail-section">
                    <h2>Entry requirements</h2>
                    <div class="prose-copy"><?= nl2br(esc($course['requirements'])) ?></div>
                </section>
            <?php endif; ?>

            <?php if (!empty($course['outcomes'])): ?>
                <section class="detail-section">
                    <h2>Learning outcomes</h2>
                    <div class="prose-copy"><?= nl2br(esc($course['outcomes'])) ?></div>
                </section>
            <?php endif; ?>
        </article>

        <aside class="detail-aside" aria-label="Course information">
            <h2>Course information</h2>
            <dl class="detail-facts">
                <?php if (! empty($course['category_name'])): ?>
                    <div>
                        <dt>Category</dt>
                        <dd><?= esc($course['category_name']) ?></dd>
                    </div>
                <?php endif; ?>
                <div>
                    <dt>Listing status</dt>
                    <dd><?= esc($course['status_label']) ?></dd>
                </div>
                <?php if (!empty($course['duration'])): ?>
                    <div>
                        <dt>Duration</dt>
                        <dd><?= esc($course['duration']) ?></dd>
                    </div>
                <?php endif; ?>
                <?php if ($course['fee_display'] !== null): ?>
                    <div>
                        <dt>Course fee</dt>
                        <dd><?= esc($course['fee_display']) ?></dd>
                    </div>
                <?php endif; ?>
            </dl>
            <p>
                Dates, admission requirements and assessment arrangements
                must be confirmed by Admissions. Unverified fees and
                certification claims are not published here.
            </p>
            <?php if ($course['status'] === 'available'): ?>
                <a class="button button-dark" href="<?= esc(base_url('college/apply/' . $course['slug']), 'attr') ?>">Apply for this course</a>
            <?php else: ?>
                <a class="button button-dark" href="<?= base_url('college/enquiry') ?>">Register interest</a>
            <?php endif; ?>
            <p><a class="text-link" href="<?= base_url('college/enquiry') ?>">Ask Admissions about this course</a></p>
        </aside>
    </div>
</section>

<section class="section section-paper">
    <div class="container">
        <a class="text-link" href="<?= base_url('college/courses') ?>">Back to all courses</a>
    </div>
</section>

<?= $this->endSection() ?>

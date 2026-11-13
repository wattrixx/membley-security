<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<article>
    <header class="page-hero">
        <div class="container">
            <span class="eyebrow">Membley Security Group</span>
            <h1><?= esc($page['title']) ?></h1>
        </div>
    </header>
    <section class="section">
        <div class="container article-content">
            <?php if (! empty($page['featured_image_path'])): ?>
                <img class="detail-image"
                     src="<?= esc(base_url($page['featured_image_path']), 'attr') ?>"
                     alt="<?= esc($page['featured_image_alt'], 'attr') ?>"
                     loading="lazy"
                     decoding="async">
            <?php endif; ?>
            <div class="prose-copy">
            <?= nl2br(esc($page['content'])) ?>
            </div>
        </div>
    </section>
    <section class="section section-paper">
        <div class="container section-cta">
            <div>
                <span class="eyebrow"><?= esc($page['slug'] === 'college' ? 'College' : ($page['slug'] === 'security-services' ? 'Security Services' : 'Membley Security Group')) ?></span>
                <h2><?= esc($page['slug'] === 'about' ? 'Explore more about Membley.' : 'Speak with the relevant team.') ?></h2>
            </div>
            <?php if ($page['slug'] === 'college'): ?>
                <a class="button button-dark" href="<?= base_url('college/courses') ?>">View courses</a>
                <a class="button button-primary" href="<?= base_url('college/contact') ?>">Contact Admissions</a>
            <?php elseif ($page['slug'] === 'security-services'): ?>
                <a class="button button-primary" href="<?= base_url('security-services/contact') ?>">Contact Security Services</a>
            <?php else: ?>
                <a class="button button-primary" href="<?= base_url('contact') ?>">Contact Membley</a>
            <?php endif; ?>
        </div>
    </section>
</article>

<?= $this->endSection() ?>

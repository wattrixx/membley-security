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
                <?php if ($page['slug'] === 'about'): ?>
                    <?php
                    $headings = ['Our Vision', 'Our Mission', 'Our Core Values', 'Our Company Background'];
                    $subheadings = array_keys(\App\Content\AboutContent::VALUES);
                    $subheadings[] = 'About Membley Security & College';
                    ?>
                    <?php foreach (preg_split('/\R/u', $page['content']) as $line): ?>
                        <?php if (in_array(trim($line), $headings, true)): ?>
                            <h2><?= esc(trim($line)) ?></h2>
                        <?php elseif (in_array(trim($line), $subheadings, true)): ?>
                            <h3><?= esc(trim($line)) ?></h3>
                        <?php else: ?>
                            <?= esc($line) ?><br>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?= nl2br(esc($page['content'])) ?>
                <?php endif; ?>
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

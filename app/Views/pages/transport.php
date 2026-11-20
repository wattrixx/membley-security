<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<article>
    <header class="page-hero">
        <div class="container">
            <span class="eyebrow">Membley Security Group</span>
            <h1><?= esc($transportPage['title']) ?></h1>
            <p>Transport and logistics information from the Group.</p>
        </div>
    </header>
    <section class="section">
        <div class="container article-content">
            <img class="detail-image" src="<?= esc(base_url($transportPage['image']), 'attr') ?>"
                 alt="<?= esc($transportPage['alt'], 'attr') ?>" width="1600" height="1067" loading="lazy" decoding="async">
            <div class="placeholder-note"><p>Illustrative information and stock photography. Services and images are subject to confirmation.</p></div>
            <div class="prose-copy"><?= nl2br(esc($transportPage['content'])) ?></div>
        </div>
    </section>
    <section class="section section-paper">
        <div class="container">
            <h2>Explore Transport &amp; Logistics</h2>
            <?= view('partials/transport_navigation') ?>
        </div>
    </section>
</article>
<?= $this->endSection() ?>

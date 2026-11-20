<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<article>
    <header class="page-hero">
        <div class="container">
            <span class="eyebrow">Membley Security Group</span>
            <h1><?= esc($transportPage['title']) ?></h1>
            <p>Transport, logistics and supply chain solutions from the Group.</p>
            <a class="button button-primary" href="<?= base_url('contact') ?>">Discuss your requirements</a>
        </div>
    </header>
    <section class="section">
        <div class="container <?= $transportOverview ? 'transport-body' : 'article-content' ?>">
            <?php if (! empty($transportPage['image'])): ?>
                <img class="detail-image transport-image" src="<?= esc(base_url($transportPage['image']), 'attr') ?>"
                     alt="<?= esc($transportPage['alt'], 'attr') ?>" loading="lazy" decoding="async">
                <?php if (str_starts_with($transportPage['image'], 'assets/images/transport-')): ?>
                    <p class="transport-image-caption">Stock photography for illustration.</p>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($transportOverview): ?>
                <?= view('partials/transport_content', ['content' => $transportPage['content']]) ?>
            <?php else: ?>
                <div class="prose-copy"><?= nl2br(esc($transportPage['content'])) ?></div>
            <?php endif; ?>
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

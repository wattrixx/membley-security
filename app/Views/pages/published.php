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
        <div class="container article-content prose-copy">
            <?= nl2br(esc($page['content'])) ?>
        </div>
    </section>
</article>

<?= $this->endSection() ?>

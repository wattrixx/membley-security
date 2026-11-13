<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<article>
    <header class="page-hero">
        <div class="container article-heading">
            <span class="eyebrow">News and Updates</span>
            <h1><?= esc($post['title']) ?></h1>
            <div class="article-byline">
                <time datetime="<?= esc($post['published_at'], 'attr') ?>">
                    <?= esc(date('F j, Y', strtotime($post['published_at']))) ?>
                </time>
                <?php if (!empty($post['author_name'])): ?>
                    <span><?= esc($post['author_name']) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="section">
        <div class="container article-content">
            <?php if ($post['featured_image_path'] !== null): ?>
                <img class="detail-image"
                     src="<?= esc(base_url($post['featured_image_path']), 'attr') ?>"
                     alt=""
                     loading="lazy"
                     decoding="async">
            <?php endif; ?>
            <div class="prose-copy"><?= nl2br(esc($post['content'])) ?></div>
            <a class="text-link" href="<?= base_url('news') ?>">Back to News and Updates</a>
        </div>
    </section>
</article>

<?= $this->endSection() ?>

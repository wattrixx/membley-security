<article class="announcement-card">
    <?php if (! empty($announcement['featured_image_path'])): ?>
        <img src="<?= esc(base_url($announcement['featured_image_path']), 'attr') ?>"
             alt="<?= esc($announcement['featured_image_alt'] ?? '', 'attr') ?>"
             loading="lazy"
             decoding="async">
    <?php endif; ?>
    <div>
        <time datetime="<?= esc($announcement['published_at'], 'attr') ?>">
            <?= esc(date('F j, Y', strtotime($announcement['published_at']))) ?>
        </time>
        <h3><?= esc($announcement['title']) ?></h3>
        <div class="prose-copy"><?= nl2br(esc($announcement['content'])) ?></div>
    </div>
</article>

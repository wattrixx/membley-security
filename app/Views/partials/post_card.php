<?php $postImagePath = $post['featured_image_path'] ?? null; ?>
<article class="content-card">
    <?php if ($postImagePath !== null): ?>
        <img class="content-card-image"
             src="<?= esc(base_url($postImagePath), 'attr') ?>"
             alt="<?= esc($post['featured_image_alt'] ?? '', 'attr') ?>"
             loading="lazy"
             decoding="async">
    <?php endif; ?>
    <div class="content-card-body">
        <?php if (! empty($post['category'])): ?>
            <span class="content-status"><?= esc($post['category']) ?></span>
        <?php endif; ?>
        <time class="content-date" datetime="<?= esc($post['published_at'], 'attr') ?>">
            <?= esc(date('F j, Y', strtotime($post['published_at']))) ?>
        </time>
        <h2>
            <a href="<?= esc(base_url('news/' . rawurlencode($post['slug'])), 'attr') ?>">
                <?= esc($post['title']) ?>
            </a>
        </h2>
        <?php if (!empty($post['excerpt'])): ?>
            <p><?= esc($post['excerpt']) ?></p>
        <?php endif; ?>
        <a class="text-link" href="<?= esc(base_url('news/' . rawurlencode($post['slug'])), 'attr') ?>">
            Read article
        </a>
    </div>
</article>

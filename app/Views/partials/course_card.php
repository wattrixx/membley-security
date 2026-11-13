<?php
$courseImagePath = $course['featured_image_path'] ?? null;
$courseStatusLabel = $course['status_label'] ?? match ($course['status']) {
    'available' => 'Available',
    'proposed' => 'Proposed',
    default => 'In development',
};
?>
<article class="content-card">
    <?php if ($courseImagePath !== null): ?>
        <img class="content-card-image"
             src="<?= esc(base_url($courseImagePath), 'attr') ?>"
             alt=""
             loading="lazy"
             decoding="async">
    <?php endif; ?>
    <div class="content-card-body">
        <span class="content-status"><?= esc($courseStatusLabel) ?></span>
        <h3>
            <a href="<?= esc(base_url('college/courses/' . rawurlencode($course['slug'])), 'attr') ?>">
                <?= esc($course['title']) ?>
            </a>
        </h3>
        <?php if (!empty($course['short_description'])): ?>
            <p><?= esc($course['short_description']) ?></p>
        <?php endif; ?>
        <a class="text-link"
           href="<?= esc(base_url('college/courses/' . rawurlencode($course['slug'])), 'attr') ?>">
            View course details
        </a>
    </div>
</article>

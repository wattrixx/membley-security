<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Membley Security Group</span>
        <h1>News and updates.</h1>
        <p>Published announcements and articles from the Group.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($posts === []): ?>
            <div class="empty-state">
                <h2>No news articles are published yet.</h2>
                <p>Check back for confirmed news and announcements.</p>
            </div>
        <?php else: ?>
            <div class="content-grid">
                <?php foreach ($posts as $post): ?>
                    <?= view('partials/post_card', ['post' => $post]) ?>
                <?php endforeach; ?>
            </div>
            <?php if ($pager->getPageCount() > 1): ?>
                <nav class="content-pagination" aria-label="News pages">
                    <?= $pager->links() ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>

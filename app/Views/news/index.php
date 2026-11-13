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
        <?php if ($announcements !== []): ?>
            <div class="section-heading">
                <span class="eyebrow">Announcements</span>
                <h2>Important updates</h2>
            </div>
            <div class="announcement-list">
                <?php foreach ($announcements as $announcement): ?>
                    <?= view('partials/announcement_card', ['announcement' => $announcement]) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($posts === [] && $announcements === []): ?>
            <div class="empty-state">
                <h2>No news articles are published yet.</h2>
                <p>Check back for confirmed news and announcements.</p>
            </div>
        <?php elseif ($posts !== []): ?>
            <div class="section-heading">
                <span class="eyebrow">News and articles</span>
                <h2>Latest stories</h2>
            </div>
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

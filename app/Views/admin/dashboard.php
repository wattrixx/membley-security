<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<header class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">Membley Security Group</span>
        <h1>Administration dashboard</h1>
        <p>Welcome, <?= esc($username) ?>. Your dashboard only shows areas assigned to your role.</p>
    </div>
</header>

<section class="admin-summary-grid" aria-label="Role-specific record counts">
    <?php foreach ($cards as $card): ?>
        <article class="admin-summary-card">
            <span class="admin-summary-label"><?= esc($card['label']) ?></span>
            <strong class="admin-summary-count"><?= esc((string) $card['count']) ?></strong>
            <p><?= esc($card['description']) ?></p>
            <?php if ($card['available']): ?>
                <a class="admin-card-link" href="<?= esc($card['href'], 'attr') ?>">Open account management</a>
            <?php else: ?>
                <span class="admin-future-label">Management is not implemented yet.</span>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</section>

<?php if ($cards === []): ?>
    <section class="admin-empty-state">
        <h2>No dashboard modules are assigned</h2>
        <p>Contact a Super Admin if you believe you need additional access.</p>
    </section>
<?php endif; ?>
<?= $this->endSection() ?>

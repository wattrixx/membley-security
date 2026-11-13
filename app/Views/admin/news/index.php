<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<header class="admin-heading-row admin-page-heading">
    <div>
        <span class="admin-eyebrow">Content management</span>
        <h1>News &amp; blog</h1>
        <p>Create articles, add categories and schedule their publication.</p>
    </div>
    <a class="admin-button admin-button-primary" href="<?= url_to('admin.cms.news.create') ?>">Create article</a>
</header>

<?php if (! empty($message)): ?><div class="admin-alert admin-alert-success" role="status"><?= esc($message) ?></div><?php endif; ?>
<?php if (! empty($error)): ?><div class="admin-alert admin-alert-error" role="alert"><?= esc($error) ?></div><?php endif; ?>

<form class="admin-filter-form" method="get" action="<?= url_to('admin.cms.news') ?>">
    <label for="newsSearch">Search articles</label>
    <input id="newsSearch" name="q" type="search" value="<?= esc($search, 'attr') ?>" placeholder="Title, category or slug">
    <label for="newsStatus">Status</label>
    <select id="newsStatus" name="status">
        <option value="">All statuses</option>
        <?php foreach (['draft', 'published', 'archived'] as $state): ?>
            <option value="<?= esc($state, 'attr') ?>" <?= $status === $state ? 'selected' : '' ?>><?= esc(ucfirst($state)) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="admin-button admin-button-dark" type="submit">Filter</button>
</form>

<?php if ($posts === []): ?>
    <section class="admin-empty-state"><h2>No articles found</h2><p>Draft articles remain private until published.</p></section>
<?php else: ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <caption>News articles, including drafts and scheduled posts</caption>
            <thead><tr><th>Article</th><th>Category</th><th>Author</th><th>Status</th><th>Publication date</th><th>Action</th></tr></thead>
            <tbody><?php foreach ($posts as $post): ?>
                <tr>
                    <td><strong><?= esc($post['title']) ?></strong><span class="admin-cell-secondary"><?= esc($post['slug']) ?></span></td>
                    <td><?= esc($post['category'] ?: '—') ?></td>
                    <td><?= esc($post['author_name'] ?: '—') ?></td>
                    <td><?= esc(ucfirst($post['status'])) ?></td>
                    <td><?= esc($post['published_at'] ?? '—') ?></td>
                    <td><a class="admin-button admin-button-light admin-button-small" href="<?= url_to('admin.cms.news.edit', $post['id']) ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?></tbody>
        </table>
    </div>
    <?= $pager->links('cmsNews') ?>
<?php endif; ?>
<?= $this->endSection() ?>

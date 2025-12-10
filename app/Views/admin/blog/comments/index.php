<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="admin-comments">
    <div class="page-header">
        <div class="header-left">
            <a href="<?= base_url('admin/blog') ?>" class="back-link">← Blog</a>
            <h1>Modération des Commentaires</h1>
            <div class="stats-badges">
                <span class="badge badge-warning"><?= $stats['pending'] ?> en attente</span>
                <span class="badge badge-success"><?= $stats['approved'] ?> approuvés</span>
                <span class="badge badge-danger"><?= $stats['spam'] ?> spam</span>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="filters-bar">
        <div class="filter-tabs">
            <a href="<?= base_url('admin/blog/comments') ?>"
                class="tab <?= empty($currentStatus) ? 'active' : '' ?>">Tous</a>
            <a href="<?= base_url('admin/blog/comments?status=pending') ?>"
                class="tab <?= $currentStatus === 'pending' ? 'active' : '' ?>">En attente</a>
            <a href="<?= base_url('admin/blog/comments?status=approved') ?>"
                class="tab <?= $currentStatus === 'approved' ? 'active' : '' ?>">Approuvés</a>
            <a href="<?= base_url('admin/blog/comments?status=spam') ?>"
                class="tab <?= $currentStatus === 'spam' ? 'active' : '' ?>">Spam</a>
            <a href="<?= base_url('admin/blog/comments?status=trash') ?>"
                class="tab <?= $currentStatus === 'trash' ? 'active' : '' ?>">Corbeille</a>
        </div>
    </div>

    <?php if (empty($comments)): ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
            </svg>
            <h3>Aucun commentaire</h3>
            <p>Pas de commentaires <?= $currentStatus ? 'avec ce statut' : '' ?> pour le moment.</p>
        </div>
    <?php else: ?>

        <!-- Bulk Actions -->
        <form action="<?= base_url('admin/blog/comments/bulk-approve') ?>" method="POST" id="bulk-form">
            <?= csrf_field() ?>
            <div class="bulk-actions">
                <label class="checkbox-label">
                    <input type="checkbox" id="select-all">
                    Tout sélectionner
                </label>
                <button type="submit" class="btn btn-sm btn-success">Approuver la sélection</button>
            </div>

            <div class="comments-list">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-card <?= $comment['status'] === 'pending' ? 'pending' : '' ?>">
                        <div class="comment-select">
                            <input type="checkbox" name="comment_ids[]" value="<?= $comment['id'] ?>">
                        </div>
                        <div class="comment-content">
                            <div class="comment-header">
                                <div class="comment-author">
                                    <div class="avatar"><?= strtoupper(substr($comment['author_name'], 0, 1)) ?></div>
                                    <div class="author-info">
                                        <strong><?= esc($comment['author_name']) ?></strong>
                                        <span class="email"><?= esc($comment['author_email']) ?></span>
                                    </div>
                                </div>
                                <div class="comment-meta">
                                    <span
                                        class="status-badge status-<?= $comment['status'] ?>"><?= ucfirst($comment['status']) ?></span>
                                    <time
                                        datetime="<?= $comment['created_at'] ?>"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></time>
                                </div>
                            </div>

                            <div class="comment-article">
                                Sur : <a href="<?= base_url('blog/' . $comment['article_slug']) ?>" target="_blank">
                                    <?= esc($comment['article_title']) ?>
                                </a>
                            </div>

                            <div class="comment-text">
                                <?= nl2br(esc($comment['content'])) ?>
                            </div>

                            <div class="comment-actions">
                                <?php if ($comment['status'] !== 'approved'): ?>
                                    <form action="<?= base_url('admin/blog/comments/approve/' . $comment['id']) ?>" method="POST"
                                        class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-success">Approuver</button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($comment['status'] !== 'spam'): ?>
                                    <form action="<?= base_url('admin/blog/comments/spam/' . $comment['id']) ?>" method="POST"
                                        class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-warning">Spam</button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($comment['status'] !== 'trash'): ?>
                                    <form action="<?= base_url('admin/blog/comments/trash/' . $comment['id']) ?>" method="POST"
                                        class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Corbeille</button>
                                    </form>
                                <?php else: ?>
                                    <form action="<?= base_url('admin/blog/comments/delete/' . $comment['id']) ?>" method="POST"
                                        class="inline" onsubmit="return confirm('Supprimer définitivement ?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer définitivement</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
    document.getElementById('select-all')?.addEventListener('change', function () {
        document.querySelectorAll('input[name="comment_ids[]"]').forEach(cb => cb.checked = this.checked);
    });
</script>

<?= $this->endSection() ?>
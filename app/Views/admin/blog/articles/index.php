<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="admin-blog">
    <div class="page-header">
        <div class="header-left">
            <h1>Gestion du Blog</h1>
            <div class="stats-badges">
                <span class="badge badge-primary"><?= $stats['total'] ?> articles</span>
                <span class="badge badge-success"><?= $stats['published'] ?> publiés</span>
                <span class="badge badge-warning"><?= $stats['draft'] ?> brouillons</span>
                <span class="badge badge-info"><?= $stats['scheduled'] ?> programmés</span>
            </div>
        </div>
        <div class="header-actions">
            <a href="<?= base_url('admin/blog/create') ?>" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Nouvel Article
            </a>
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
            <a href="<?= base_url('admin/blog') ?>" class="tab <?= empty($currentStatus) ? 'active' : '' ?>">Tous</a>
            <a href="<?= base_url('admin/blog?status=published') ?>"
                class="tab <?= $currentStatus === 'published' ? 'active' : '' ?>">Publiés</a>
            <a href="<?= base_url('admin/blog?status=draft') ?>"
                class="tab <?= $currentStatus === 'draft' ? 'active' : '' ?>">Brouillons</a>
            <a href="<?= base_url('admin/blog?status=scheduled') ?>"
                class="tab <?= $currentStatus === 'scheduled' ? 'active' : '' ?>">Programmés</a>
            <a href="<?= base_url('admin/blog?status=archived') ?>"
                class="tab <?= $currentStatus === 'archived' ? 'active' : '' ?>">Archivés</a>
        </div>
        <div class="filter-actions">
            <a href="<?= base_url('admin/blog/categories') ?>" class="btn btn-outline">Catégories</a>
            <a href="<?= base_url('admin/blog/comments') ?>" class="btn btn-outline">Commentaires</a>
            <a href="<?= base_url('admin/blog/stats') ?>" class="btn btn-outline">Statistiques</a>
        </div>
    </div>

    <!-- Articles Table -->
    <?php if (empty($articles)): ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <polyline points="10 9 9 9 8 9" />
            </svg>
            <h3>Aucun article</h3>
            <p>Commencez par créer votre premier article de blog.</p>
            <a href="<?= base_url('admin/blog/create') ?>" class="btn btn-primary">Créer un article</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Statut</th>
                        <th>Vues</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td class="article-title-cell">
                                <a href="<?= base_url('admin/blog/edit/' . $article['id']) ?>" class="article-title">
                                    <?= esc($article['title']) ?>
                                </a>
                                <?php if ($article['is_featured']): ?>
                                    <span class="badge badge-star" title="À la une">★</span>
                                <?php endif; ?>
                            </td>
                            <td class="author-cell">
                                <?= esc(trim(($article['first_name'] ?? '') . ' ' . ($article['last_name'] ?? '')) ?: 'N/A') ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = match ($article['status']) {
                                    'published' => 'success',
                                    'draft' => 'warning',
                                    'scheduled' => 'info',
                                    'archived' => 'secondary',
                                    default => 'secondary'
                                };
                                $statusLabel = match ($article['status']) {
                                    'published' => 'Publié',
                                    'draft' => 'Brouillon',
                                    'scheduled' => 'Programmé',
                                    'archived' => 'Archivé',
                                    default => $article['status']
                                };
                                ?>
                                <span class="status-badge status-<?= $statusClass ?>"><?= $statusLabel ?></span>
                            </td>
                            <td class="views-cell"><?= number_format($article['view_count'] ?? 0) ?></td>
                            <td class="date-cell">
                                <?php if ($article['status'] === 'published' && $article['published_at']): ?>
                                    <?= date('d/m/Y', strtotime($article['published_at'])) ?>
                                <?php else: ?>
                                    <?= date('d/m/Y', strtotime($article['updated_at'])) ?>
                                <?php endif; ?>
                            </td>
                            <td class="actions-cell">
                                <div class="action-buttons">
                                    <a href="<?= base_url('admin/blog/edit/' . $article['id']) ?>" class="btn btn-sm btn-icon"
                                        title="Modifier">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </a>
                                    <a href="<?= base_url('admin/blog/preview/' . $article['id']) ?>"
                                        class="btn btn-sm btn-icon" title="Prévisualiser" target="_blank">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                    </a>
                                    <?php if ($article['status'] === 'draft'): ?>
                                        <form action="<?= base_url('admin/blog/publish/' . $article['id']) ?>" method="POST"
                                            class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-icon btn-success" title="Publier">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <polyline points="9 11 12 14 22 4" />
                                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                                                </svg>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form action="<?= base_url('admin/blog/delete/' . $article['id']) ?>" method="POST"
                                        class="inline" onsubmit="return confirm('Archiver cet article ?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Archiver">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
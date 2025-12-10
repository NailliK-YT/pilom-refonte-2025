<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="admin-categories">
    <div class="page-header">
        <div class="header-left">
            <a href="<?= base_url('admin/blog') ?>" class="back-link">← Blog</a>
            <h1>Catégories du Blog</h1>
        </div>
        <div class="header-actions">
            <a href="<?= base_url('admin/blog/categories/create') ?>" class="btn btn-primary">
                + Nouvelle Catégorie
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (empty($categories)): ?>
        <div class="empty-state">
            <h3>Aucune catégorie</h3>
            <p>Créez votre première catégorie de blog.</p>
            <a href="<?= base_url('admin/blog/categories/create') ?>" class="btn btn-primary">Créer une catégorie</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Slug</th>
                        <th>Articles</th>
                        <th>Ordre</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td>
                                <?php if ($category['parent_id']): ?>
                                    <span class="indent">— </span>
                                <?php endif; ?>
                                <strong><?= esc($category['name']) ?></strong>
                            </td>
                            <td><code><?= esc($category['slug']) ?></code></td>
                            <td><?= $category['article_count'] ?></td>
                            <td><?= $category['sort_order'] ?></td>
                            <td>
                                <span class="status-badge status-<?= $category['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $category['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="actions-cell">
                                <a href="<?= base_url('admin/blog/categories/edit/' . $category['id']) ?>"
                                    class="btn btn-sm btn-icon" title="Modifier">
                                    ✏️
                                </a>
                                <?php if ($category['article_count'] == 0): ?>
                                    <form action="<?= base_url('admin/blog/categories/delete/' . $category['id']) ?>" method="POST"
                                        class="inline" onsubmit="return confirm('Supprimer cette catégorie ?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Supprimer">🗑️</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="admin-category-form">
    <div class="page-header">
        <a href="<?= base_url('admin/blog/categories') ?>" class="back-link">← Catégories</a>
        <h1><?= $isEdit ? 'Modifier la catégorie' : 'Nouvelle Catégorie' ?></h1>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form
        action="<?= $isEdit ? base_url('admin/blog/categories/edit/' . $category['id']) : base_url('admin/blog/categories/create') ?>"
        method="POST" class="form-card">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="name">Nom *</label>
            <input type="text" id="name" name="name" required class="form-control"
                value="<?= esc($category['name'] ?? old('name')) ?>">
        </div>

        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" class="form-control"
                value="<?= esc($category['slug'] ?? old('slug')) ?>" placeholder="auto-généré">
        </div>

        <div class="form-group">
            <label for="parent_id">Catégorie parente</label>
            <select id="parent_id" name="parent_id" class="form-control">
                <option value="">— Aucune —</option>
                <?php foreach ($parentCategories as $parent): ?>
                    <option value="<?= $parent['id'] ?>" <?= ($category['parent_id'] ?? '') === $parent['id'] ? 'selected' : '' ?>>
                        <?= esc($parent['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"
                class="form-control"><?= esc($category['description'] ?? old('description')) ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="sort_order">Ordre d'affichage</label>
                <input type="number" id="sort_order" name="sort_order" class="form-control"
                    value="<?= $category['sort_order'] ?? 0 ?>" min="0">
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" <?= ($category['is_active'] ?? true) ? 'checked' : '' ?>>
                    Catégorie active
                </label>
            </div>
        </div>

        <hr>
        <h3>SEO</h3>

        <div class="form-group">
            <label for="meta_title">Meta Title</label>
            <input type="text" id="meta_title" name="meta_title" class="form-control"
                value="<?= esc($category['meta_title'] ?? '') ?>" maxlength="70">
        </div>

        <div class="form-group">
            <label for="meta_description">Meta Description</label>
            <textarea id="meta_description" name="meta_description" rows="2" class="form-control"
                maxlength="160"><?= esc($category['meta_description'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <a href="<?= base_url('admin/blog/categories') ?>" class="btn btn-outline">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Mettre à jour' : 'Créer la catégorie' ?>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
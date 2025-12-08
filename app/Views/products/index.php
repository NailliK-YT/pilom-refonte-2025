<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Produits') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/products.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h1 class="page-title"><?= esc($title) ?></h1>
    <a href="<?= base_url('products/create') ?>" class="btn btn-primary">
        Nouveau produit →
    </a>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success">
        <?= session('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-error">
        <?= session('error') ?>
    </div>
<?php endif; ?>

<!-- Filtres de recherche -->
<div class="card">
    <h3>Recherche et filtres</h3>
    <form method="get" action="<?= base_url('products') ?>">
        <div class="form-group">
            <label class="form-label" for="search">Recherche</label>
            <input type="text" 
                   id="search" 
                   name="search" 
                   class="form-control"
                   placeholder="Nom, référence ou description..."
                   value="<?= esc($filters['keywords'] ?? '') ?>">
        </div>

        <div class="d-flex gap-2">
            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="category">Catégorie</label>
                <select id="category" name="category" class="form-control">
                    <option value="">Toutes les catégories</option>
                    <?php if (isset($categories) && !empty($categories)): ?>
                        <?php foreach ($categories as $id => $name): ?>
                            <option value="<?= esc($id) ?>" 
                                    <?= ($filters['category_id'] ?? '') == $id ? 'selected' : '' ?>>
                                <?= esc($name) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="min_price">Prix min (€)</label>
                <input type="number" 
                       id="min_price" 
                       name="min_price" 
                       class="form-control"
                       step="0.01"
                       value="<?= esc($filters['min_price'] ?? '') ?>">
            </div>

            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="max_price">Prix max (€)</label>
                <input type="number" 
                       id="max_price" 
                       name="max_price" 
                       class="form-control"
                       step="0.01"
                       value="<?= esc($filters['max_price'] ?? '') ?>">
            </div>

            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="sort_by">Trier par</label>
                <select id="sort_by" name="sort_by" class="form-control">
                    <option value="created_at" <?= ($filters['sort_by'] ?? 'created_at') == 'created_at' ? 'selected' : '' ?>>Date</option>
                    <option value="name" <?= ($filters['sort_by'] ?? '') == 'name' ? 'selected' : '' ?>>Nom</option>
                    <option value="price_ht" <?= ($filters['sort_by'] ?? '') == 'price_ht' ? 'selected' : '' ?>>Prix</option>
                </select>
            </div>
        </div>

        <div class="form-check">
            <input type="checkbox" 
                   id="archived" 
                   name="archived" 
                   class="form-check-input"
                   value="1"
                   <?= ($filters['is_archived'] ?? false) ? 'checked' : '' ?>>
            <label class="form-check-label" for="archived">
                Afficher les produits archivés
            </label>
        </div>

        <div class="d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary">Rechercher →</button>
            <a href="<?= base_url('products') ?>" class="btn btn-outline">Réinitialiser</a>
        </div>
    </form>
</div>

<!-- Liste des produits -->
<div class="card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><?= $totalProducts ?? 0 ?> produits trouvés</h3>
    </div>

    <?php if (empty($products)): ?>
        <p class="text-muted text-center">Aucun produit trouvé</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th width="80">Image</th>
                    <th>Référence</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Prix HT</th>
                    <th>TVA</th>
                    <th>Prix TTC</th>
                    <th>Statut</th>
                    <th width="250">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <?php if (!empty($product['image_path'])): ?>
                                <img src="<?= base_url('writable/uploads/' . $product['image_path']) ?>" 
                                     alt="<?= esc($product['name']) ?>"
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background: #e0e0e0; border-radius: 4px;"></div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= esc($product['reference']) ?></strong></td>
                        <td><?= esc($product['name']) ?></td>
                        <td class="text-muted"><?= esc($product['category_name'] ?? 'N/A') ?></td>
                        <td><?= number_format($product['price_ht'], 2, ',', ' ') ?> €</td>
                        <td><?= esc($product['tva_rate'] ?? 'N/A') ?> %</td>
                        <td><strong><?= number_format($product['price_ttc'] ?? ($product['price_ht'] * 1.2), 2, ',', ' ') ?> €</strong></td>
                        <td>
                            <?php if ($product['is_archived'] ?? false): ?>
                                <span class="badge badge-danger">Archivé</span>
                            <?php else: ?>
                                <span class="badge badge-success">Actif</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <a href="<?= base_url('products/show/' . $product['id']) ?>" 
                               class="btn btn-sm btn-outline">Voir</a>
                            <a href="<?= base_url('products/edit/' . $product['id']) ?>" 
                               class="btn btn-sm btn-outline">Modifier</a>
                            <form method="post" 
                                  action="<?= base_url('products/archive/' . $product['id']) ?>"
                                  style="display: inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <?= ($product['is_archived'] ?? false) ? 'Restaurer' : 'Archiver' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination simple -->
        <?php if (($totalProducts ?? 0) > ($perPage ?? 20)): ?>
            <div class="pagination">
                <?php
                $totalPages = ceil($totalProducts / $perPage);
                $currentPage = $currentPage ?? 1;
                
                for ($i = 1; $i <= $totalPages; $i++):
                ?>
                    <a href="?page=<?= $i ?><?= isset($filters) ? '&' . http_build_query($filters) : '' ?>" 
                       class="page-link <?= $i == $currentPage ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Catégories') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/products.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h1 class="page-title"><?= esc($title) ?></h1>
    <a href="<?= base_url('categories/create') ?>" class="btn btn-primary">
        Nouvelle catégorie →
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

<div class="card">
    <h2>Arborescence des catégories</h2>

    <?php if (empty($categoryTree)): ?>
        <p class="text-muted text-center">Aucune catégorie trouvée</p>
    <?php else: ?>
        <?php
        function displayTree($categories, $level = 0)
        {
            foreach ($categories as $category) {
                $indent = str_repeat('—', $level);
                echo '<div style="padding: 12px; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">';
                echo '<div>';
                echo '<strong>' . ($indent ? $indent . ' ' : '') . esc($category['name']) . '</strong>';
                if (!empty($category['description'])) {
                    echo '<br><span class="text-muted" style="font-size: 14px; margin-left: ' . ($level * 20) . 'px;">' . esc($category['description']) . '</span>';
                }
                echo '</div>';
                echo '<div class="table-actions">';
                echo '<a href="' . base_url('categories/edit/' . $category['id']) . '" class="btn btn-sm btn-outline">Modifier</a>';
                echo '<form method="post" action="' . base_url('categories/delete/' . $category['id']) . '" style="display:inline;" onsubmit="return confirm(\'Confirmer la suppression ?\')">';
                echo csrf_field();
                echo '<button type="submit" class="btn btn-sm btn-danger">Supprimer</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';

                if (isset($category['children']) && !empty($category['children'])) {
                    displayTree($category['children'], $level + 1);
                }
            }
        }
        displayTree($categoryTree);
        ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
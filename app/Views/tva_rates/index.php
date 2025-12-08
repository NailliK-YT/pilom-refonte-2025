<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Taux de TVA') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/products.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h1 class="page-title"><?= esc($title) ?></h1>
    <a href="<?= base_url('tva-rates/create') ?>" class="btn btn-primary">
        Nouveau taux →
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

<?php if (!empty($search)): ?>
    <div class="card">
        <p>Recherche : <strong><?= esc($search) ?></strong>
            <a href="<?= base_url('tva-rates') ?>">Effacer</a>
        </p>
    </div>
<?php endif; ?>

<div class="card">
    <form method="get" action="<?= base_url('tva-rates') ?>" class="mb-3">
        <div class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Rechercher un taux..."
                value="<?= esc($search ?? '') ?>">
            <button type="submit" class="btn btn-outline">Rechercher</button>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Libellé</th>
                <th>Taux (%)</th>
                <th>Par défaut</th>
                <th>Date création</th>
                <th width="200">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tvaRates)): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        Aucun taux de TVA trouvé
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($tvaRates as $rate): ?>
                    <tr>
                        <td><strong><?= esc($rate['label']) ?></strong></td>
                        <td><?= number_format($rate['rate'], 2, ',', ' ') ?> %</td>
                        <td>
                            <?php if ($rate['is_default']): ?>
                                <span class="badge badge-success">Oui</span>
                            <?php else: ?>
                                <span class="badge badge-info">Non</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted">
                            <?= date('d/m/Y', strtotime($rate['created_at'])) ?>
                        </td>
                        <td class="table-actions">
                            <a href="<?= base_url('tva-rates/edit/' . $rate['id']) ?>" class="btn btn-sm btn-outline">
                                Modifier
                            </a>
                            <form method="post" action="<?= base_url('tva-rates/delete/' . $rate['id']) ?>"
                                style="display: inline;" onsubmit="return confirm('Confirmer la suppression ?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (isset($pager)): ?>
        <div class="pagination">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
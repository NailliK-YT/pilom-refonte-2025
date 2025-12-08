<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Factures Récurrentes<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="page-header-left">
        <h2>Factures Récurrentes</h2>
    </div>
    <div class="page-header-right">
        <a href="<?= base_url('recurring-invoices/create') ?>" class="btn btn-primary">
            Nouvelle Facture Récurrente
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Client</th>
                <th>Fréquence</th>
                <th>Montant</th>
                <th>Prochaine facture</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recurring)): ?>
                <tr>
                    <td colspan="6" class="text-center">Aucune facture récurrente</td>
                </tr>
            <?php else: ?>
                <?php foreach ($recurring as $item): ?>
                    <tr>
                        <td><?= esc($item['contact_nom']) ?></td>
                        <td>
                            <?php
                            $freq = [
                                'monthly' => 'Mensuelle',
                                'quarterly' => 'Trimestrielle',
                                'yearly' => 'Annuelle'
                            ];
                            echo $freq[$item['frequency']] ?? $item['frequency'];
                            ?>
                        </td>
                        <td><?= number_format($item['amount'], 2, ',', ' ') ?> €</td>
                        <td><?= date('d/m/Y', strtotime($item['next_invoice_date'])) ?></td>
                        <td>
                            <span class="badge badge-<?= $item['status'] === 'active' ? 'success' : ($item['status'] === 'paused' ? 'warning' : 'secondary') ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= base_url('recurring-invoices/edit/' . $item['id']) ?>" class="btn btn-sm btn-secondary">Modifier</a>
                            <?php if ($item['status'] === 'active'): ?>
                                <form method="post" action="<?= base_url('recurring-invoices/pause/' . $item['id']) ?>" style="display: inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-warning">Pause</button>
                                </form>
                            <?php elseif ($item['status'] === 'paused'): ?>
                                <form method="post" action="<?= base_url('recurring-invoices/resume/' . $item['id']) ?>" style="display: inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-success">Reprendre</button>
                                </form>
                            <?php endif; ?>
                            <form method="post" action="<?= base_url('recurring-invoices/cancel/' . $item['id']) ?>" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr ?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-danger">Annuler</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>

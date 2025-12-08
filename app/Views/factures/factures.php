<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Factures<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/commercial.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Gestion des factures</h1>
        <p class="text-muted">Consultez et gérez toutes vos factures</p>
    </div>
    <a href="<?= base_url('factures/create') ?>" class="btn btn-primary">
        <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
        </svg>
        Nouvelle facture
    </a>
</div>

<div class="card">
    <div class="filters">
        <div class="filter-group" style="flex: 1; min-width: 200px;">
            <label class="form-label">Recherche</label>
            <input type="text" name="search" class="form-control" placeholder="Rechercher une facture..."
                   value="<?= esc($search ?? '') ?>"
                   onkeyup="if(event.key==='Enter') window.location.href='<?= base_url('factures') ?>?search='+this.value+'&statut=<?= esc($statut ?? '') ?>'">
        </div>

        <div class="filter-group">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-control form-select"
                    onchange="window.location.href='<?= base_url('factures') ?>?statut=' + this.value + '&search=<?= esc($search ?? '') ?>'">
                <option value="">Tous les statuts</option>
                <option value="brouillon" <?= ($statut ?? '') === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                <option value="envoyée" <?= ($statut ?? '') === 'envoyée' ? 'selected' : '' ?>>Envoyée</option>
                <option value="payée partiellement" <?= ($statut ?? '') === 'payée partiellement' ? 'selected' : '' ?>>Payée partiellement</option>
                <option value="payée" <?= ($statut ?? '') === 'payée' ? 'selected' : '' ?>>Payée</option>
                <option value="en retard" <?= ($statut ?? '') === 'en retard' ? 'selected' : '' ?>>En retard</option>
            </select>
        </div>
    </div>

    <p class="text-muted mt-3">
        <strong><?= count($factures) ?> facture(s) trouvée(s)</strong>
    </p>
</div>

<?php if (empty($factures)): ?>
    <div class="card">
        <div class="empty-state">
            <svg viewBox="0 0 20 20" fill="currentColor" style="width: 48px; height: 48px; opacity: 0.5;">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
            </svg>
            <h3>Aucune facture trouvée</h3>
            <p>Créez votre première facture</p>
            <a href="<?= base_url('factures/create') ?>" class="btn btn-primary">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Nouvelle facture
            </a>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($factures as $f): ?>
        <div class="devis-card">
            <div class="devis-header">
                <div class="devis-title">
                    Facture #<?= esc($f['numero_facture']) ?> - <?= esc($f['prenom'] . ' ' . $f['nom']) ?>
                </div>
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <?php
                    $statusClassMap = [
                        'brouillon' => 'statut-gris',
                        'envoye' => 'statut-bleu',
                        'partiel' => 'statut-orange',
                        'payee' => 'statut-vert',
                        'en_retard' => 'statut-rouge',
                        // Mapping French values if needed, though controller usually handles raw values
                        'envoyée' => 'statut-bleu',
                        'payée partiellement' => 'statut-orange',
                        'payée' => 'statut-vert',
                        'en retard' => 'statut-rouge',
                    ];
                    $etat = strtolower($f['statut']);
                    $classe = $statusClassMap[$etat] ?? 'statut-gris';
                    ?>
                    <span class="statut <?= $classe ?>">
                        <?= ucfirst(esc($f['statut'])) ?>
                    </span>

                    <a href="<?= base_url('factures/edit/' . $f['id']) ?>" class="btn btn-secondary">Modifier</a>
                    
                    <form action="<?= base_url('factures/delete/' . $f['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>

                    <a href="<?= base_url('factures/pdf/' . $f['id']) ?>" class="btn btn-success">PDF</a>
                    
                    <a href="<?= base_url('factures/send/' . $f['id']) ?>" class="btn btn-secondary">Envoyer</a>

                    <?php if ($etat === 'en retard' || $etat === 'en_retard'): ?>
                        <a href="<?= base_url('factures/reminder/' . $f['id']) ?>" class="btn btn-warning">Relancer</a>
                    <?php endif; ?>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1.5rem; padding: 1rem 1.5rem;">

                <!-- Section gauche : Détails -->
                <div class="devis-details single-column">
                    <div class="devis-detail">
                        <div class="detail-label">Date d'émission</div>
                        <div class="detail-value"><?= date('j/n/Y', strtotime($f['date_emission'])) ?></div>
                    </div>
                    <div class="devis-detail">
                        <div class="detail-label">Date d'échéance</div>
                        <div class="detail-value"><?= date('j/n/Y', strtotime($f['date_echeance'])) ?></div>
                    </div>
                    <?php if (!empty($f['numero_devis'])): ?>
                        <div class="devis-detail">
                            <div class="detail-label">Devis associé</div>
                            <div class="detail-value">#<?= esc($f['numero_devis']) ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="devis-detail">
                        <div class="detail-label">Entreprise</div>
                        <div class="detail-value"><?= esc($f['entreprise']) ?></div>
                    </div>
                </div>

                <!-- Section droite : Montants -->
                <div style="background-color: #e8f5e9; padding: 1.25rem; border-radius: 8px; border-left: 4px solid #28a745; width: 260px; flex-shrink: 0; ">
                    <div style="color: #155724; font-weight: 700; margin-bottom: 1rem; font-size: 15px;">
                        Montants
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1rem;">

                        <div class="devis-detail">
                            <div class="detail-label" style="font-size: 11px; color: #777; text-transform: uppercase;">Montant HT</div>
                            <div class="detail-value" style="font-weight: 600;">
                                <?= number_format($f['montant_ht'], 2, ',', ' ') ?> €
                            </div>
                        </div>

                        <div class="devis-detail">
                            <div class="detail-label" style="font-size: 11px; color: #777; text-transform: uppercase;">TVA</div>
                            <div class="detail-value" style="font-weight: 600;">
                                <?= number_format($f['montant_tva'], 2, ',', ' ') ?> €
                            </div>
                        </div>

                        <?php $montantTTC = $f['montant_ht'] + $f['montant_tva']; ?>
                        <div class="devis-detail">
                            <div class="detail-label" style="font-size: 11px; color: #777; text-transform: uppercase;">Montant TTC</div>
                            <div class="detail-value" style="
                                font-weight: 700;
                                color: #28a745;
                                background-color: #d4edda;
                                padding: 0.4rem 0.75rem;
                                borderRadius: 4px;
                                border: 1px solid #c3e6cb;
                                display: inline-block;
                            ">
                                <?= number_format($montantTTC, 2, ',', ' ') ?> €
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>

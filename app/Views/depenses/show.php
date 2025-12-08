<?= $this->extend('layouts/template') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/depenses.css') ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="depense-detail-container">
    <div class="page-header">
        <div class="header-title">
            <h1><i class="fas fa-file-invoice"></i> Détail de la dépense</h1>
        </div>
        <div class="header-actions">
            <a href="<?= base_url('depenses/edit/' . $depense['id']) ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="<?= base_url('depenses') ?>" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="detail-grid">
        <!-- Informations principales -->
        <div class="detail-section card">
            <h3 class="section-title">Informations générales</h3>

            <div class="detail-row">
                <span class="label">Date :</span>
                <span class="value"><?= format_date_fr($depense['date']) ?></span>
            </div>

            <div class="detail-row">
                <span class="label">Description :</span>
                <span class="value"><?= esc($depense['description']) ?></span>
            </div>

            <div class="detail-row">
                <span class="label">Catégorie :</span>
                <span class="value">
                    <span class="category-badge"
                        style="background-color: <?= esc($depense['categorie_couleur'] ?? '#999') ?>">
                        <?= esc($depense['categorie_nom'] ?? '-') ?>
                    </span>
                </span>
            </div>

            <div class="detail-row">
                <span class="label">Fournisseur :</span>
                <span class="value">
                    <?php if (!empty($depense['fournisseur_nom'])): ?>
                        <a href="<?= base_url('fournisseurs/show/' . $depense['fournisseur_id']) ?>">
                            <?= esc($depense['fournisseur_nom']) ?>
                        </a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </span>
            </div>

            <div class="detail-row">
                <span class="label">Statut :</span>
                <span class="value"><?= get_statut_badge($depense['statut']) ?></span>
            </div>

            <?php if ($depense['recurrent']): ?>
                <div class="detail-row">
                    <span class="label">Récurrence :</span>
                    <span class="value">
                        <span class="badge badge-info">
                            <i class="fas fa-sync"></i> Dépense récurrente
                        </span>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Montants -->
        <div class="detail-section card">
            <h3 class="section-title">Montants</h3>

            <div class="detail-row">
                <span class="label">Montant HT :</span>
                <span class="value"><?= format_montant($depense['montant_ht']) ?></span>
            </div>

            <div class="detail-row">
                <span class="label">Taux TVA :</span>
                <span class="value"><?= number_format($depense['tva_rate'] ?? 0, 2) ?>%</span>
            </div>

            <div class="detail-row">
                <span class="label">Montant TVA :</span>
                <span class="value">
                    <?= format_montant($depense['montant_ttc'] - $depense['montant_ht']) ?>
                </span>
            </div>

            <div class="detail-row total-row">
                <span class="label"><strong>Montant TTC :</strong></span>
                <span class="value"><strong><?= format_montant($depense['montant_ttc']) ?></strong></span>
            </div>

            <div class="detail-row">
                <span class="label">Méthode de paiement :</span>
                <span class="value">
                    <?= get_methode_paiement_icon($depense['methode_paiement']) ?>
                    <?= get_methode_paiement_label($depense['methode_paiement']) ?>
                </span>
            </div>
        </div>

        <!-- Justificatif -->
        <div class="detail-section card">
            <h3 class="section-title">Justificatif</h3>

            <?php if ($depense['justificatif_path']): ?>
                <div class="justificatif-preview">
                    <?php
                    $ext = pathinfo($depense['justificatif_path'], PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
                    ?>

                    <?php if ($isImage): ?>
                        <img src="<?= get_justificatif_url($depense['justificatif_path']) ?>" alt="Justificatif"
                            class="justificatif-image">
                    <?php else: ?>
                        <div class="file-icon-large">
                            <?= get_file_icon($depense['justificatif_path']) ?>
                        </div>
                    <?php endif; ?>

                    <a href="<?= get_justificatif_url($depense['justificatif_path']) ?>" target="_blank"
                        class="btn btn-secondary">
                        <i class="fas fa-download"></i> Télécharger
                    </a>
                </div>
            <?php else: ?>
                <p class="text-muted">Aucun justificatif</p>
            <?php endif; ?>
        </div>

        <!-- Historique des modifications -->
        <?php if (!empty($historique)): ?>
            <div class="detail-section card full-width">
                <h3 class="section-title">Historique des modifications</h3>

                <div class="timeline">
                    <?php foreach ($historique as $entry): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <strong><?= esc($entry['user_name'] ?? 'Système') ?></strong>
                                    <span class="timeline-date"><?= $entry['date_modification'] ?></span>
                                </div>
                                <div class="timeline-body">
                                    <?= $entry['formatted_text'] ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
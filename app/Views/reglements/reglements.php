<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Règlements<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.reglement-card {
    background: var(--white, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: 12px;
    margin-bottom: 1rem;
    overflow: hidden;
    transition: all 0.2s ease;
}

.reglement-card:hover {
    border-color: var(--primary-color, #4e51c0);
    box-shadow: 0 4px 12px rgba(78, 81, 192, 0.1);
}

.reglement-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: var(--light-gray, #f1f5f9);
    border-bottom: 1px solid var(--border-color, #e2e8f0);
    flex-wrap: wrap;
    gap: 1rem;
}

.reglement-title {
    font-weight: 600;
    color: var(--dark-gray, #1e293b);
}

.mode-paiement {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    background: #e2e8f0; 
    color: #475569;
}

.reglement-details {
    display: grid;
    gap: 1rem;
}

.reglement-detail .detail-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    color: var(--text-muted, #6b7280);
    margin-bottom: 0.25rem;
}

.reglement-detail .detail-value {
    font-weight: 500;
    color: var(--dark-gray, #1e293b);
}

.filters {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: flex-end;
}

.filter-group {
    min-width: 150px;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Gestion des règlements</h1>
        <p class="text-muted">Suivez les paiements de vos factures</p>
    </div>
    <a href="<?= base_url('reglements/create') ?>" class="btn btn-primary">
        <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
        </svg>
        Nouveau règlement
    </a>
</div>

<div class="card">
    <div class="filters">
        <div class="filter-group" style="flex: 1; min-width: 200px;">
            <label class="form-label">Recherche</label>
            <input type="text" name="search" class="form-control" placeholder="Référence, montant..."
                   value="<?= esc($search ?? '') ?>"
                   onkeyup="if(event.key==='Enter') window.location.href='<?= base_url('reglements') ?>?search='+this.value+'&mode=<?= esc($mode ?? '') ?>'">
        </div>

        <div class="filter-group">
            <label class="form-label">Mode de paiement</label>
            <select name="mode" class="form-control form-select"
                    onchange="window.location.href='<?= base_url('reglements') ?>?mode=' + this.value + '&search=<?= esc($search ?? '') ?>'">
                <option value="">Tous les modes</option>
                <option value="espèces" <?= ($mode ?? '') === 'espèces' ? 'selected' : '' ?>>Espèces</option>
                <option value="chèque" <?= ($mode ?? '') === 'chèque' ? 'selected' : '' ?>>Chèque</option>
                <option value="virement" <?= ($mode ?? '') === 'virement' ? 'selected' : '' ?>>Virement</option>
                <option value="CB" <?= ($mode ?? '') === 'CB' ? 'selected' : '' ?>>CB</option>
            </select>
        </div>
    </div>

    <p class="text-muted mt-3">
        <strong><?= count($reglements ?? []) ?> règlement(s) trouvé(s)</strong>
    </p>
</div>

<?php if (empty($reglements ?? [])): ?>
    <div class="card">
        <div class="empty-state">
            <svg viewBox="0 0 20 20" fill="currentColor" style="width: 48px; height: 48px; opacity: 0.5;">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
            </svg>
            <h3>Aucun règlement trouvé</h3>
            <p>Enregistrez votre premier règlement</p>
            <a href="<?= base_url('reglements/create') ?>" class="btn btn-primary">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Nouveau règlement
            </a>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($reglements as $r): ?>
        <div class="reglement-card">
            <div class="reglement-header">
                <div class="reglement-title">
                    Règlement #<?= esc($r['id']) ?> - <?= date('d/m/Y', strtotime($r['date_reglement'])) ?>
                </div>
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <span class="mode-paiement">
                        <?= ucfirst(esc($r['mode_paiement'])) ?>
                    </span>

                    <a href="<?= base_url('reglements/edit/' . $r['id']) ?>" class="btn btn-secondary">Modifier</a>
                    <form action="<?= base_url('reglements/delete/' . $r['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce règlement ?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1.5rem; padding: 1rem 1.5rem;">

                <!-- Section gauche : Détails -->
                <div class="reglement-details single-column">
                    <div class="reglement-detail">
                        <div class="detail-label">Facture associée</div>
                        <div class="detail-value">
                            <?php if (!empty($r['numero_facture'])): ?>
                                <a href="<?= base_url('factures/show/' . $r['facture_id']) ?>">
                                    #<?= esc($r['numero_facture']) ?>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="reglement-detail">
                        <div class="detail-label">Référence</div>
                        <div class="detail-value"><?= esc($r['reference'] ?: '-') ?></div>
                    </div>
                </div>

                <!-- Section droite : Montant -->
                <div style="background-color: #e8f5e9; padding: 1.25rem; border-radius: 8px; border-left: 4px solid #28a745; width: 200px; flex-shrink: 0; ">
                    <div class="reglement-detail">
                        <div class="detail-label" style="font-size: 11px; color: #777; text-transform: uppercase;">Montant</div>
                        <div class="detail-value" style="
                            font-weight: 700;
                            color: #28a745;
                            font-size: 1.2rem;
                        ">
                            <?= number_format($r['montant'], 2, ',', ' ') ?> €
                        </div>
                    </div>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>

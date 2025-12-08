<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Devis<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/commercial.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Gestion des devis</h1>
        <p class="text-muted">Consultez et gérez tous vos devis professionnels</p>
    </div>
    <a href="<?= base_url('devis/create') ?>" class="btn btn-primary">
        <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
        </svg>
        Nouveau devis
    </a>
</div>

<div class="card">
    <div class="filters">
        <div class="filter-group" style="flex: 1; min-width: 200px;">
            <label class="form-label">Recherche</label>
            <input type="text" name="search" class="form-control" placeholder="Rechercher un devis..."
                   value="<?= esc($search ?? '') ?>"
                   onkeyup="if(event.key==='Enter') window.location.href='<?= base_url('devis') ?>?search='+this.value+'&statut=<?= esc($statut ?? '') ?>'">
        </div>

        <div class="filter-group">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-control form-select"
                    onchange="window.location.href='<?= base_url('devis') ?>?statut=' + this.value + '&search=<?= esc($search ?? '') ?>'">
                <option value="">Tous les statuts</option>
                <option value="brouillon" <?= ($statut ?? '') === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                <option value="envoyé" <?= ($statut ?? '') === 'envoyé' ? 'selected' : '' ?>>Envoyé</option>
                <option value="accepté" <?= ($statut ?? '') === 'accepté' ? 'selected' : '' ?>>Accepté</option>
                <option value="refusé" <?= ($statut ?? '') === 'refusé' ? 'selected' : '' ?>>Refusé</option>
                <option value="expiré" <?= ($statut ?? '') === 'expiré' ? 'selected' : '' ?>>Expiré</option>
            </select>
        </div>
    </div>

    <p class="text-muted mt-3">
        <strong><?= count($devis ?? []) ?> devis trouvé(s)</strong>
    </p>
</div>

<?php if (empty($devis ?? [])): ?>
    <div class="card">
        <div class="empty-state">
            <svg viewBox="0 0 20 20" fill="currentColor" style="width: 48px; height: 48px; opacity: 0.5;">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
            </svg>
            <h3>Aucun devis trouvé</h3>
            <p>Créez votre premier devis</p>
            <a href="<?= base_url('devis/create') ?>" class="btn btn-primary">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Nouveau devis
            </a>
        </div>
    </div>
<?php else: ?>
		<?php foreach ($devis as $d): ?>
			<div class="devis-card">
				<div class="devis-header">
					<div class="devis-title">
						Devis #<?= esc($d['numero_devis']) ?> - <?= esc($d['prenom'] . ' ' . $d['nom']) ?>
					</div>
					<div style="display: flex; gap: 0.75rem; align-items: center;">
						<?php
						$statusClassMap = [
							'brouillon' => 'statut-gris',
							'envoye'   => 'statut-bleu',
							'accepte'  => 'statut-vert',
							'refuse'   => 'statut-orange',
							'expire'   => 'statut-rouge',
						];
						$etat = strtolower($d['statut']);
						$classe = $statusClassMap[$etat] ?? '';
						?>
						<span class="statut <?= $classe ?>">
							<?= ucfirst(esc($d['statut'])) ?>
						</span>

						<a href="<?= base_url('devis/edit/' . $d['id']) ?>" class="btn btn-secondary">Modifier</a>
						<form action="<?= base_url('devis/delete/' . $d['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce devis ?');">
							<?= csrf_field() ?>
							<button type="submit" class="btn btn-danger">Supprimer</button>
						</form>
						<a href="<?= base_url('devis/convertir-en-facture/' . $d['id']) ?>" class="btn btn-secondary">Convertir En Facture</a>
						<a href="<?= base_url('devis/pdf/' . $d['id']) ?>" class="btn btn-success">PDF</a>
					</div>
				</div>

				<div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1.5rem; padding: 1rem 1.5rem;">

					<!-- Section gauche : Détails du Devis -->
					<div class="devis-details single-column">
						<div class="devis-detail">
							<div class="detail-label">Date d'émission</div>
							<div class="detail-value"><?= date('j/n/Y', strtotime($d['date_emission'])) ?></div>
						</div>
						<div class="devis-detail">
							<div class="detail-label">Date de validité</div>
							<div class="detail-value"><?= date('j/n/Y', strtotime($d['date_validite'])) ?></div>
						</div>
						<div class="devis-detail">
							<div class="detail-label">Entreprise</div>
							<div class="detail-value"><?= esc($d['entreprise']) ?></div>
						</div>
					</div>

					<!-- Section gauche : Montant -->
					<div style="background-color: #e8f5e9; padding: 1.25rem; border-radius: 8px; border-left: 4px solid #28a745; width: 260px; flex-shrink: 0; ">
						<div style="color: #155724; font-weight: 700; margin-bottom: 1rem; font-size: 15px;">
							Montants
						</div>

						<div style="display: flex; flex-direction: column; gap: 1rem;">

							<div class="devis-detail">
								<div class="detail-label" style="font-size: 11px; color: #777; text-transform: uppercase;">Montant HT</div>
								<div class="detail-value" style="font-weight: 600;">
									<?= number_format($d['montant_ht'], 2, ',', ' ') ?> €
								</div>
							</div>

							<div class="devis-detail">
								<div class="detail-label" style="font-size: 11px; color: #777; text-transform: uppercase;">TVA</div>
								<div class="detail-value" style="font-weight: 600;">
									<?= number_format($d['montant_tva'], 2, ',', ' ') ?> €
								</div>
							</div>

							<div class="devis-detail">
								<div class="detail-label" style="font-size: 11px; color: #777; text-transform: uppercase;">Montant TTC</div>
								<div class="detail-value" style="
									font-weight: 700;
									color: #28a745;
									background-color: #d4edda;
									padding: 0.4rem 0.75rem;
									border-radius: 4px;
									border: 1px solid #c3e6cb;
									display: inline-block;
								">
									<?= number_format($d['montant_ttc'], 2, ',', ' ') ?> €
								</div>
							</div>

						</div>
					</div>

				</div>
			</div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>

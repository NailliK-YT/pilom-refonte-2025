<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1 style="margin-bottom: 2rem;">
        <i class="fas fa-file-invoice"></i> Ajouter un devis
    </h1>

    <?php if (session()->has('errors')): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach (session()->get('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
		<form action="<?= base_url('devis/store') ?>" method="POST">
			<?= csrf_field() ?>

			<div class="card">

				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">

					<?php
					// Génération numéro auto
					$today = date('Ymd');
					$random = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
					$numeroDevis = "DEV-$today-$random";
					?>

					<!-- NUMERO DE DEVIS -->
					<div class="form-group">
						<label class="form-label">Numéro de devis *</label>
						<input type="text" name="numero_devis" class="form-control"
							value="<?= old('numero_devis', $numeroDevis) ?>" required>
					</div>

					<!-- CONTACT -->
					<div class="form-group">
						<label class="form-label">Contact *</label>
						<select name="contact_id" class="form-select" required>
							<option value="">Sélectionner un contact</option>
							<?php foreach ($contacts as $contact): ?>
								<option value="<?= $contact['id'] ?>" <?= old('contact_id') == $contact['id'] ? 'selected' : '' ?>>
									<?= esc($contact['prenom'] . ' ' . $contact['nom']) ?>
									<?= $contact['entreprise'] ? ' (' . esc($contact['entreprise']) . ')' : '' ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<!-- DATE EMISSION -->
					<div class="form-group">
						<label class="form-label">Date d'émission *</label>
						<input type="date" name="date_emission" class="form-control"
							value="<?= old('date_emission', date('Y-m-d')) ?>" required>
					</div>

					<!-- DATE VALIDITE -->
					<div class="form-group">
						<label class="form-label">Date de validité *</label>
						<input type="date" name="date_validite" class="form-control"
							value="<?= old('date_validite', date('Y-m-d', strtotime('+30 days'))) ?>" required>
					</div>

					<!-- MONTANT TTC -->
					<div class="form-group">
						<label class="form-label">Montant *</label>
						<input type="number" step="0.01" name="montant" class="form-control"
							value="<?= old('montant', 0) ?>" required>
					</div>

					<!-- STATUT -->
					<div class="form-group">
						<label class="form-label">Statut *</label>
						<select name="statut" class="form-select" required>
							<option value="">Sélectionner</option>
							<option value="brouillon" <?= old('statut') === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
							<option value="envoye"   <?= old('statut') === 'envoye' ? 'selected' : '' ?>>Envoyé</option>
							<option value="accepte"  <?= old('statut') === 'accepte' ? 'selected' : '' ?>>Accepté</option>
							<option value="refuse"   <?= old('statut') === 'refuse' ? 'selected' : '' ?>>Refusé</option>
							<option value="expire"   <?= old('statut') === 'expire' ? 'selected' : '' ?>>Expiré</option>
						</select>
					</div>

				</div>
			</div>

			<div style="display: flex; gap: 1rem; margin-top: 2rem;">
				<button type="submit" class="btn btn-primary">
					<i class="fas fa-save"></i> Ajouter le devis
				</button>

				<a href="<?= base_url('devis') ?>" class="btn btn-outline">
					Annuler
				</a>
			</div>

		</form>

</div>
<?= $this->endSection() ?>

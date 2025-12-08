<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1 style="margin-bottom: 2rem;">
        <i class="fas fa-money-bill-wave"></i> Modifier un règlement
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

    <form action="<?= base_url('reglements/update/' . $reglement['id']) ?>" method="POST">
        <?= csrf_field() ?>

        <div class="card">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">

                <!-- FACTURE -->
                <div class="form-group">
                    <label class="form-label">Facture *</label>
                    <select name="facture_id" class="form-select" required>
                        <option value="">Sélectionner une facture</option>
                        <?php foreach ($factures as $f): ?>
                            <option value="<?= $f['id'] ?>" <?= old('facture_id', $reglement['facture_id']) == $f['id'] ? 'selected' : '' ?>>
                                #<?= esc($f['numero_facture']) ?> - <?= esc($f['prenom'] . ' ' . $f['nom']) ?> (<?= number_format($f['montant_ttc'], 2, ',', ' ') ?> €)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- DATE REGLEMENT -->
                <div class="form-group">
                    <label class="form-label">Date du règlement *</label>
                    <input type="date" name="date_reglement" class="form-control"
                           value="<?= old('date_reglement', $reglement['date_reglement']) ?>" required>
                </div>

                <!-- MONTANT -->
                <div class="form-group">
                    <label class="form-label">Montant *</label>
                    <input type="number" step="0.01" name="montant" class="form-control"
                           value="<?= old('montant', $reglement['montant']) ?>" required>
                </div>

                <!-- MODE PAIEMENT -->
                <div class="form-group">
                    <label class="form-label">Mode de paiement *</label>
                    <select name="mode_paiement" class="form-select" required>
                        <option value="">Sélectionner</option>
                        <option value="espèces" <?= old('mode_paiement', $reglement['mode_paiement']) === 'espèces' ? 'selected' : '' ?>>Espèces</option>
                        <option value="chèque"  <?= old('mode_paiement', $reglement['mode_paiement']) === 'chèque' ? 'selected' : '' ?>>Chèque</option>
                        <option value="virement"<?= old('mode_paiement', $reglement['mode_paiement']) === 'virement' ? 'selected' : '' ?>>Virement</option>
                        <option value="CB"      <?= old('mode_paiement', $reglement['mode_paiement']) === 'CB' ? 'selected' : '' ?>>CB</option>
                    </select>
                </div>

                <!-- REFERENCE -->
                <div class="form-group">
                    <label class="form-label">Référence (optionnel)</label>
                    <input type="text" name="reference" class="form-control"
                           value="<?= old('reference', $reglement['reference']) ?>" placeholder="Ex: Numéro de chèque">
                </div>

            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
            <a href="<?= base_url('reglements') ?>" class="btn btn-outline">
                Annuler
            </a>
        </div>

    </form>
</div>
<?= $this->endSection() ?>

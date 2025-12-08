<?= $this->extend('layouts/template') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/depenses.css') ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d depenses-form-container">
    <div class="page-header">
        <div class="header-title">
            <h1><i class=" fas fa-receipt"></i> <?= $depense ? 'Modifier une dépense' : 'Nouvelle dépense' ?></h1>
        </div>
        <div class="header-actions">
            <a href="<?= base_url('depenses') ?>" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-error">
            <strong>Erreurs de validation :</strong>
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= $depense ? base_url('depenses/update/' . $depense['id']) : base_url('depenses/store') ?>" 
          method="post" enctype="multipart/form-data" class="depense-form" id="depenseForm">
        <?= csrf_field() ?>

        <div class="form-grid">
            <!-- Informations générales -->
            <div class="form-section">
                <h3 class="section-title">Informations générales</h3>
                
                <div class="form-group">
                    <label for="date" class="required">Date</label>
                    <input type="date" id="date" name="date" 
                           value="<?= old('date', $depense['date'] ?? date('Y-m-d')) ?>" 
                           class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="description" class="required">Description</label>
                    <textarea id="description" name="description" class="form-control" 
                              rows="3" required><?= old('description', $depense['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="categorie_id" class="required">Catégorie</label>
                    <select id="categorie_id" name="categorie_id" class="form-control" required>
                        <option value="">-- Sélectionnez --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" 
                                    <?= (old('categorie_id', $depense['categorie_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                <?= esc($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fournisseur_id">Fournisseur</label>
                    <select id="fournisseur_id" name="fournisseur_id" class="form-control">
                        <option value="">-- Aucun --</option>
                        <?php foreach ($fournisseurs as $fourn): ?>
                            <option value="<?= $fourn['id'] ?>" 
                                    <?= (old('fournisseur_id', $depense['fournisseur_id'] ?? '') == $fourn['id']) ? 'selected' : '' ?>>
                                <?= esc($fourn['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text">
                        <a href="<?= base_url('fournisseurs/create') ?>" target="_blank">+ Créer un nouveau fournisseur</a>
                    </small>
                </div>
            </div>

            <!-- Montants -->
            <div class="form-section">
                <h3 class="section-title">Montants</h3>

                <div class="form-group">
                    <label for="montant_ht" class="required">Montant HT</label>
                    <div class="input-group">
                        <input type="number" id="montant_ht" name="montant_ht" step="0.01" min="0"
                               value="<?= old('montant_ht', $depense['montant_ht'] ?? '') ?>" 
                               class="form-control" required>
                        <span class="input-suffix">€</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tva_id" class="required">Taux TVA</label>
                    <select id="tva_id" name="tva_id" class="form-control" required>
                        <option value="">-- Sélectionnez --</option>
                        <?php foreach ($tvaRates as $tva): ?>
                            <option value="<?= $tva['id'] ?>" data-rate="<?= $tva['rate'] ?>"
                                    <?= (old('tva_id', $depense['tva_id'] ?? '') == $tva['id']) ? 'selected' : '' ?>>
                                <?= esc($tva['label']) ?> (<?= $tva['rate'] ?>%)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="montant_ttc" class="required">Montant TTC</label>
                    <div class="input-group">
                        <input type="number" id="montant_ttc" name="montant_ttc" step="0.01" min="0"
                               value="<?= old('montant_ttc', $depense['montant_ttc'] ?? '') ?>" 
                               class="form-control" readonly required>
                        <span class="input-suffix">€</span>
                    </div>
                    <small class="form-text">Calculé automatiquement</small>
                </div>

                <div class="form-group">
                    <label for="methode_paiement" class="required">Méthode de paiement</label>
                    <select id="methode_paiement" name="methode_paiement" class="form-control" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="especes" <?= (old('methode_paiement', $depense['methode_paiement'] ?? '') == 'especes') ? 'selected' : '' ?>>Espèces</option>
                        <option value="cheque" <?= (old('methode_paiement', $depense['methode_paiement'] ?? '') == 'cheque') ? 'selected' : '' ?>>Chèque</option>
                        <option value="virement" <?= (old('methode_paiement', $depense['methode_paiement'] ?? '') == 'virement') ? 'selected' : '' ?>>Virement</option>
                        <option value="cb" <?= (old('methode_paiement', $depense['methode_paiement'] ?? '') == 'cb') ? 'selected' : '' ?>>Carte bancaire</option>
                    </select>
                </div>
            </div>

            <!-- Justificatif et Statut -->
            <div class="form-section">
                <h3 class="section-title">Justificatif et statut</h3>

                <div class="form-group">
                    <label for="justificatif">Justificatif</label>
                    <input type="file" id="justificatif" name="justificatif" class="form-control" 
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-text">Formats acceptés : PDF, JPG, PNG (max 5Mo)</small>
                    
                    <?php if ($depense && $depense['justificatif_path']): ?>
                        <div class="current-file">
                            <i class="fas fa-file-pdf"></i>
                            <a href="<?= get_justificatif_url($depense['justificatif_path']) ?>" target="_blank">
                                Voir le justificatif actuel
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="statut">Statut</label>
                    <select id="statut" name="statut" class="form-control">
                        <option value="brouillon" <?= (old('statut', $depense['statut'] ?? 'brouillon') == 'brouillon') ? 'selected' : '' ?>>Brouillon</option>
                        <option value="valide" <?= (old('statut', $depense['statut'] ?? '') == 'valide') ? 'selected' : '' ?>>Validé</option>
                        <option value="archive" <?= (old('statut', $depense['statut'] ?? '') == 'archive') ? 'selected' : '' ?>>Archivé</option>
                    </select>
                </div>

                <!-- Récurrence -->
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="recurrent" name="recurrent" value="1"
                               <?= old('recurrent', $depense['recurrent'] ?? false) ? 'checked' : '' ?>>
                        Dépense récurrente
                    </label>
                </div>

                <div id="recurrence_fields" style="display: none;">
                    <div class="form-group">
                        <label for="frequence_id">Fréquence</label>
                        <select id="frequence_id" name="frequence_id" class="form-control">
                            <option value="">-- Sélectionnez --</option>
                            <?php foreach ($frequences as $freq): ?>
                                <option value="<?= $freq['id'] ?>"
                                        <?= (old('frequence_id', $depense['frequence_id'] ?? '') == $freq['id']) ? 'selected' : '' ?>>
                                    <?= esc($freq['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_debut_recurrence">Date début</label>
                            <input type="date" id="date_debut_recurrence" name="date_debut_recurrence" 
                                   value="<?= old('date_debut_recurrence', date('Y-m-d')) ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="date_fin_recurrence">Date fin (optionnel)</label>
                            <input type="date" id="date_fin_recurrence" name="date_fin_recurrence" 
                                   value="<?= old('date_fin_recurrence', '') ?>" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $depense ? 'Mettre à jour' : 'Enregistrer' ?>
            </button>
            <a href="<?= base_url('depenses') ?>" class="btn btn-outline">
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
// Calcul automatique TTC
document.getElementById('montant_ht').addEventListener('input', calculateTTC);
document.getElementById('tva_id').addEventListener('change', calculateTTC);

function calculateTTC() {
    const montantHT = parseFloat(document.getElementById('montant_ht').value) || 0;
    const tvaSelect = document.getElementById('tva_id');
    const tvaRate = parseFloat(tvaSelect.options[tvaSelect.selectedIndex]?.dataset.rate) || 0;
    
    const montantTTC = montantHT + (montantHT * tvaRate / 100);
    document.getElementById('montant_ttc').value = montantTTC.toFixed(2);
}

// Toggle champs récurrence
document.getElementById('recurrent').addEventListener('change', function() {
    document.getElementById('recurrence_fields').style.display = this.checked ? 'block' : 'none';
});

// Initialiser au chargement
if (document.getElementById('recurrent').checked) {
    document.getElementById('recurrence_fields').style.display = 'block';
}

calculateTTC();
</script>

<?= $this->endSection() ?>

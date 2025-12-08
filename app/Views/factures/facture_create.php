<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1 style="margin-bottom: 2rem;">
        <i class="fas fa-file-invoice-dollar"></i> Ajouter une facture
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

    <form action="<?= base_url('factures/store') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="card">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">

                <?php
                // Génération numéro auto
                $today = date('Ymd');
                $random = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                $numeroFacture = "FAC-$today-$random";
                ?>

                <!-- NUMERO DE FACTURE -->
                <div class="form-group">
                    <label class="form-label">Numéro de facture *</label>
                    <input type="text" name="numero_facture" class="form-control"
                           value="<?= old('numero_facture', $numeroFacture) ?>" required>
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

                <!-- DEVIS LIÉ -->
                <div class="form-group">
                    <label class="form-label">Devis lié (optionnel)</label>
                    <select name="id_devis" class="form-select">
                        <option value="">Aucun</option>
                        <?php foreach ($devis as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= old('id_devis') == $d['id'] ? 'selected' : '' ?>>
                                <?= esc($d['numero_devis'] . ' - ' . $d['nom']) ?>
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

                <!-- DATE ECHEANCE -->
                <div class="form-group">
                    <label class="form-label">Date d'échéance *</label>
                    <input type="date" name="date_echeance" class="form-control"
                           value="<?= old('date_echeance', date('Y-m-d', strtotime('+30 days'))) ?>" required>
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
                        <option value="envoyée"   <?= old('statut') === 'envoyée' ? 'selected' : '' ?>>Envoyée</option>
                        <option value="payée partiellement"  <?= old('statut') === 'payée partiellement' ? 'selected' : '' ?>>Payée partiellement</option>
                        <option value="payée"    <?= old('statut') === 'payée' ? 'selected' : '' ?>>Payée</option>
                        <option value="en retard"<?= old('statut') === 'en retard' ? 'selected' : '' ?>>En retard</option>
                    </select>
                </div>

            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Ajouter la facture
            </button>
            <a href="<?= base_url('factures') ?>" class="btn btn-outline">
                Annuler
            </a>
        </div>

    </form>
</div>
<?= $this->endSection() ?>

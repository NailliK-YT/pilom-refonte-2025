<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1 style="margin-bottom: 2rem;">
        <i class="fas fa-file-invoice"></i> Modifier un devis
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

    <?php if (session()->has('error')): ?>
        <div class="alert alert-error">
            <?= esc(session()->get('error')) ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('devis/update/' . $devis['id']) ?>" method="POST">
        <?= csrf_field() ?>

        <div class="card">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">

                <div class="form-group">
                    <label class="form-label">Numéro de devis *</label>
                    <input type="text" name="numero_devis" class="form-control"
                           value="<?= old('numero_devis', $devis['numero_devis']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Contact *</label>
                    <select name="contact_id" class="form-select" required>
                        <option value="">Sélectionner un contact</option>
                        <?php foreach ($contacts as $contact): ?>
                            <option value="<?= $contact['id'] ?>" <?= old('contact_id', $devis['contact_id']) == $contact['id'] ? 'selected' : '' ?>>
                                <?= esc($contact['prenom'] . ' ' . $contact['nom']) ?>
                                <?= $contact['entreprise'] ? ' (' . esc($contact['entreprise']) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Date d'émission *</label>
                    <input type="date" name="date_emission" class="form-control"
                           value="<?= old('date_emission', $devis['date_emission']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Date de validité *</label>
                    <input type="date" name="date_validite" class="form-control"
                           value="<?= old('date_validite', $devis['date_validite']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Montant *</label>
                    <input type="number" step="0.01" name="montant" class="form-control"
                           value="<?= old('montant', $devis['montant_ttc']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Statut *</label>
                    <select name="statut" class="form-select" required>
                        <option value="">Sélectionner</option>
                        <option value="brouillon" <?= old('statut', $devis['statut']) === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                        <option value="envoye" <?= old('statut', $devis['statut']) === 'envoyé' ? 'selected' : '' ?>>Envoyé</option>
                        <option value="accepte" <?= old('statut', $devis['statut']) === 'accepté' ? 'selected' : '' ?>>Accepté</option>
                        <option value="refuse" <?= old('statut', $devis['statut']) === 'refusé' ? 'selected' : '' ?>>Refusé</option>
                        <option value="expire" <?= old('statut', $devis['statut']) === 'expiré' ? 'selected' : '' ?>>Expiré</option>
                    </select>
                </div>

            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            <a href="<?= base_url('devis') ?>" class="btn btn-outline">
                Annuler
            </a>
        </div>

    </form>
</div>

<?= $this->endSection() ?>

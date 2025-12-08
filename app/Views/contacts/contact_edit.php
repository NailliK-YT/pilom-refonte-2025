<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1 style="margin-bottom: 2rem;">
        <i class="fas fa-address-book"></i> Modifier un contact
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

    <form action="<?= base_url('contacts/update/' . $contact['id']) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="card">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">

                <div class="form-group">
                    <label class="form-label">Prénom *</label>
                    <input type="text" name="prenom" class="form-control"
                           value="<?= old('prenom', $contact['prenom']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="nom" class="form-control"
                           value="<?= old('nom', $contact['nom']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= old('email', $contact['email']) ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control"
                           value="<?= old('telephone', $contact['telephone']) ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="adresse" class="form-control"
                           value="<?= old('adresse', $contact['adresse']) ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Entreprise</label>
                    <input type="text" name="entreprise" class="form-control"
                           value="<?= old('entreprise', $contact['entreprise']) ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Type *</label>
                    <select name="type" class="form-select" required>
                        <option value="">Sélectionner</option>
                        <option value="client" <?= old('type', $contact['type']) === 'client' ? 'selected' : '' ?>>Client</option>
                        <option value="prospect" <?= old('type', $contact['type']) === 'prospect' ? 'selected' : '' ?>>Prospect</option>
                        <option value="fournisseur" <?= old('type', $contact['type']) === 'fournisseur' ? 'selected' : '' ?>>Fournisseur</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Statut *</label>
                    <select name="statut" class="form-select" required>
                        <option value="">Sélectionner</option>
                        <option value="actif" <?= old('statut', $contact['statut']) === 'actif' ? 'selected' : '' ?>>Actif</option>
                        <option value="inactif" <?= old('statut', $contact['statut']) === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                        <option value="archive" <?= old('statut', $contact['statut']) === 'archive' ? 'selected' : '' ?>>Archivé</option>
                    </select>
                </div>

            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            <a href="<?= base_url('contacts') ?>" class="btn btn-outline">
                Annuler
            </a>
        </div>

    </form>
</div>

<?= $this->endSection() ?>

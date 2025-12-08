<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1 style="margin-bottom: 2rem;">
        <i class="fas fa-address-book"></i> Ajouter un contact
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

    <form action="<?= base_url('contacts/store') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="card">

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">

                <div class="form-group">
                    <label class="form-label">Prénom *</label>
                    <input type="text" name="prenom" class="form-control"
                           placeholder="Jean" value="<?= old('prenom') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="nom" class="form-control"
                           placeholder="Dupont" value="<?= old('nom') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="jean.dupont@example.com"
                           value="<?= old('email') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control"
                           placeholder="0612345678"
                           value="<?= old('telephone') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="adresse" class="form-control"
                           placeholder="Ex : 12 rue des Fleurs, 76000 Le Havre"
                           value="<?= old('adresse') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Entreprise</label>
                    <input type="text" name="entreprise" class="form-control"
                           placeholder="Ex : Dupont SARL"
                           value="<?= old('entreprise') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Type *</label>
                    <select name="type" class="form-select" required>
                        <option value="">Sélectionner</option>
                        <option value="client" <?= old('type') === 'client' ? 'selected' : '' ?>>Client</option>
                        <option value="prospect" <?= old('type') === 'prospect' ? 'selected' : '' ?>>Prospect</option>
                        <option value="fournisseur" <?= old('type') === 'fournisseur' ? 'selected' : '' ?>>Fournisseur</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Statut *</label>
                    <select name="statut" class="form-select" required>
                        <option value="">Sélectionner</option>
                        <option value="actif" <?= old('statut') === 'actif' ? 'selected' : '' ?>>Actif</option>
                        <option value="inactif" <?= old('statut') === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                        <option value="archive" <?= old('statut') === 'archive' ? 'selected' : '' ?>>Archivé</option>
                    </select>
                </div>

            </div>
            <!-- END GRID -->
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Ajouter le contact
            </button>

            <a href="<?= base_url('contacts') ?>" class="btn btn-outline">
                Annuler
            </a>
        </div>

    </form>
</div>
<?= $this->endSection() ?>

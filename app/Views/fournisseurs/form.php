<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="page-header mb-4">
        <h1 class="page-title"><?= esc($title) ?></h1>
    </div>

    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-body">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= isset($fournisseur) ? base_url('fournisseurs/update/' . $fournisseur['id']) : base_url('fournisseurs/store') ?>">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="nom" class="form-label">Nom du fournisseur *</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?= old('nom', $fournisseur['nom'] ?? '') ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contact" class="form-label">Nom du contact</label>
                        <input type="text" class="form-control" id="contact" name="contact" value="<?= old('contact', $fournisseur['contact'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="siret" class="form-label">SIRET</label>
                        <input type="text" class="form-control" id="siret" name="siret" value="<?= old('siret', $fournisseur['siret'] ?? '') ?>" placeholder="14 chiffres">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $fournisseur['email'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control" id="telephone" name="telephone" value="<?= old('telephone', $fournisseur['telephone'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="adresse" class="form-label">Adresse</label>
                    <textarea class="form-control" id="adresse" name="adresse" rows="3"><?= old('adresse', $fournisseur['adresse'] ?? '') ?></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= base_url('fournisseurs') ?>" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <?= isset($fournisseur) ? 'Mettre à jour' : 'Créer' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

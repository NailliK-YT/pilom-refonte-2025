<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1 style="margin-bottom: 2rem;">
        <i class="fas fa-folder-plus"></i> Nouveau Dossier
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

    <form action="<?= base_url('documents/create-folder') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="card" style="padding: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">

                <!-- Nom du dossier -->
                <div class="form-group">
                    <label class="form-label">Nom du dossier *</label>
                    <input type="text" name="name" class="form-control" 
                           value="<?= old('name') ?>" required>
                </div>

                <!-- Dossier parent -->
                <div class="form-group">
                    <label class="form-label">Dossier parent (optionnel)</label>
                    <select name="parent_id" class="form-select">
                        <option value="">Racine</option>
                        <?php if (isset($folders) && !empty($folders)): ?>
                            <?php foreach ($folders as $folder): ?>
                                <option value="<?= $folder['id'] ?>" <?= old('parent_id', $parent_id ?? '') == $folder['id'] ? 'selected' : '' ?>>
                                    <?= esc($folder['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Créer le dossier
            </button>
            <a href="<?= base_url('documents') ?>" class="btn btn-outline">
                Annuler
            </a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

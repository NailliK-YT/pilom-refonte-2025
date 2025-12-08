<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Uploader un Document<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <a href="<?= base_url('documents?folder=' . ($folder_id ?? '')) ?>" class="btn btn-secondary">
        ← Retour
    </a>
</div>

<div class="form-card">
    <h2>Uploader un document</h2>
    <form action="<?= base_url('documents/upload') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="folder_id" value="<?= esc($folder_id ?? '') ?>">
        
        <div class="form-group">
            <label for="document">Fichier</label>
            <input type="file" id="document" name="document" class="form-control" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Uploader</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

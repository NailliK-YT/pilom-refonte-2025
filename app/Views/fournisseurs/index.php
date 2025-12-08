<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Fournisseurs<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">
                <i class="fas fa-truck me-2"></i><?= esc($title) ?>
            </h1>
            <p class="text-muted">Gérez vos fournisseurs et prestataires</p>
        </div>
        <a href="<?= base_url('fournisseurs/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau Fournisseur
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="get" action="<?= base_url('fournisseurs') ?>" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher un fournisseur..." value="<?= esc($filters['keywords'] ?? '') ?>">
                    <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
                </div>
            </form>

            <?php if (empty($fournisseurs)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                    <p class="lead text-muted">Aucun fournisseur trouvé</p>
                    <a href="<?= base_url('fournisseurs/create') ?>" class="btn btn-primary mt-2">Créer le premier fournisseur</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>SIRET</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fournisseurs as $fournisseur): ?>
                                <tr>
                                    <td><strong><?= esc($fournisseur['nom']) ?></strong></td>
                                    <td><?= esc($fournisseur['contact'] ?? '-') ?></td>
                                    <td>
                                        <?php if (!empty($fournisseur['email'])): ?>
                                            <a href="mailto:<?= esc($fournisseur['email']) ?>"><?= esc($fournisseur['email']) ?></a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($fournisseur['telephone'] ?? '-') ?></td>
                                    <td><?= esc($fournisseur['siret'] ?? '-') ?></td>
                                    <td class="text-end">
                                        <a href="<?= base_url('fournisseurs/edit/' . $fournisseur['id']) ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteFournisseur('<?= $fournisseur['id'] ?>')" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (isset($pager)): ?>
                    <div class="mt-4">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function deleteFournisseur(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?')) {
        fetch('<?= base_url('fournisseurs/delete/') ?>' + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Erreur lors de la suppression');
            }
        })
        .catch(error => {
            alert('Erreur lors de la suppression');
        });
    }
}
</script>
<?= $this->endSection() ?>

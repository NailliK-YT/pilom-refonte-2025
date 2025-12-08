<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Mes Dépenses<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/depenses.css') ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="depenses-container">
    <div class="page-header">
        <div class="header-title">
            <h1><i class="fas fa-receipt"></i> <?= esc($title) ?></h1>
            <p class="subtitle">Gestion et suivi de vos dépenses professionnelles</p>
        </div>
        <div class="header-actions">
            <a href="<?= base_url('depenses/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Dépense
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Statistiques rapides -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= format_montant($totalDepenses) ?></div>
                <div class="stat-label">Total Dépenses</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= count(array_filter($depenses, fn($d) => $d['statut'] === 'valide')) ?></div>
                <div class="stat-label">Validées</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= count(array_filter($depenses, fn($d) => $d['statut'] === 'brouillon')) ?>
                </div>
                <div class="stat-label">Brouillons</div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filters-panel">
        <form method="get" action="<?= base_url('depenses') ?>" class="filters-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search">Recherche</label>
                    <input type="text" id="search" name="search" value="<?= esc($filters['search'] ?? '') ?>"
                        placeholder="Description, montant..." class="form-control">
                </div>

                <div class="filter-group">
                    <label for="categorie">Catégorie</label>
                    <select id="categorie" name="categorie" class="form-control">
                        <option value="">Toutes</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($filters['categorie_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= esc($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="statut">Statut</label>
                    <select id="statut" name="statut" class="form-control">
                        <option value="">Tous</option>
                        <option value="brouillon" <?= ($filters['statut'] ?? '') == 'brouillon' ? 'selected' : '' ?>>
                            Brouillon</option>
                        <option value="valide" <?= ($filters['statut'] ?? '') == 'valide' ? 'selected' : '' ?>>Validé
                        </option>
                        <option value="archive" <?= ($filters['statut'] ?? '') == 'archive' ? 'selected' : '' ?>>Archivé
                        </option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="date_debut">Date début</label>
                    <input type="date" id="date_debut" name="date_debut"
                        value="<?= esc($filters['date_debut'] ?? '') ?>" class="form-control">
                </div>

                <div class="filter-group">
                    <label for="date_fin">Date fin</label>
                    <input type="date" id="date_fin" name="date_fin" value="<?= esc($filters['date_fin'] ?? '') ?>"
                        class="form-control">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <a href="<?= base_url('depenses') ?>" class="btn btn-outline">
                        <i class="fas fa-times"></i> Réinitialiser
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tableau des dépenses -->
    <div class="table-container">
        <?php if (empty($depenses)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox fa-3x"></i>
                <h3>Aucune dépense trouvée</h3>
                <p>Commencez par créer votre première dépense</p>
                <a href="<?= base_url('depenses/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle Dépense
                </a>
            </div>
        <?php else: ?>
            <table class="depenses-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Catégorie</th>
                        <th>Fournisseur</th>
                        <th>Montant HT</th>
                        <th>TVA</th>
                        <th>Montant TTC</th>
                        <th>Paiement</th>
                        <th>Statut</th>
                        <th>Justificatif</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($depenses as $depense): ?>
                        <tr>
                            <td><?= format_date_fr($depense['date']) ?></td>
                            <td>
                                <strong><?= esc($depense['description']) ?></strong>
                                <?php if ($depense['recurrent']): ?>
                                    <span class="badge badge-info"><i class="fas fa-sync"></i> Récurrent</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($depense['categorie_nom'])): ?>
                                    <span class="category-badge"
                                        style="background-color: <?= esc($depense['categorie_couleur'] ?? '#999') ?>">
                                        <?= esc($depense['categorie_nom']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($depense['fournisseur_nom'] ?? '-') ?></td>
                            <td><?= format_montant($depense['montant_ht']) ?></td>
                            <td><?= number_format($depense['tva_rate'] ?? 0, 2) ?>%</td>
                            <td><strong><?= format_montant($depense['montant_ttc']) ?></strong></td>
                            <td>
                                <?= get_methode_paiement_icon($depense['methode_paiement']) ?>
                                <?= get_methode_paiement_label($depense['methode_paiement']) ?>
                            </td>
                            <td><?= get_statut_badge($depense['statut']) ?></td>
                            <td>
                                <?php if ($depense['justificatif_path']): ?>
                                    <a href="<?= get_justificatif_url($depense['justificatif_path']) ?>" target="_blank"
                                        class="btn-icon" title="Voir le justificatif">
                                        <?= get_file_icon($depense['justificatif_path']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions-col">
                                <div class="action-buttons">
                                    <a href="<?= base_url('depenses/show/' . $depense['id']) ?>" class="btn-icon btn-view"
                                        title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('depenses/edit/' . $depense['id']) ?>" class="btn-icon btn-edit"
                                        title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn-icon btn-delete"
                                        onclick="deleteDepense('<?= $depense['id'] ?>')" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($pager): ?>
                <div class="pagination-container">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    function deleteDepense(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?')) {
            fetch('<?= base_url('depenses/delete/') ?>' + id, {
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
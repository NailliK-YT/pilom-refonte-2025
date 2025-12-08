<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Contacts<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.contacts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.25rem;
}

.contact-card {
    background: var(--white, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.2s ease;
}

.contact-card:hover {
    border-color: var(--primary-color, #4e51c0);
    box-shadow: 0 4px 12px rgba(78, 81, 192, 0.1);
}

.contact-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--dark-gray, #1e293b);
    margin-bottom: 0.5rem;
}

.contact-email, .contact-telephone {
    font-size: 0.875rem;
    color: var(--text-muted, #6b7280);
    margin-bottom: 0.25rem;
}

.contact-adresse, .contact-entreprise {
    font-size: 0.8rem;
    color: var(--text-muted, #6b7280);
    margin-bottom: 0.25rem;
}

.contact-type, .contact-statut {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 0.75rem;
    margin-right: 0.5rem;
}

.contact-type {
    background: var(--primary-light, rgba(78, 81, 192, 0.1));
    color: var(--primary-color, #4e51c0);
}

.contact-statut {
    background: var(--success-light, #dcfce7);
    color: var(--success, #16a34a);
}

.contact-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color, #e2e8f0);
}

.filters {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: flex-end;
}

.filter-group {
    min-width: 150px;
}

@media (max-width: 768px) {
    .filters {
        flex-direction: column;
    }
    .filter-group {
        width: 100%;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Gestion des contacts</h1>
        <p class="text-muted">Gérez tous vos contacts professionnels (clients, prospects, fournisseurs)</p>
    </div>
    <a href="<?= base_url('contacts/create') ?>" class="btn btn-primary">
        <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
        </svg>
        Ajouter un contact
    </a>
</div>

<div class="card">
    <div class="filters">
        <div class="filter-group" style="flex: 1; min-width: 200px;">
            <label class="form-label">Recherche</label>
            <input type="text" name="search" class="form-control" placeholder="Rechercher un contact..." 
                value="<?= esc($search ?? '') ?>" 
                onkeyup="if(event.key==='Enter') window.location.href='<?= base_url('contacts') ?>?search='+this.value">
        </div>

        <div class="filter-group">
            <label class="form-label">Type</label>
            <select name="type" class="form-control form-select" 
                onchange="window.location.href='<?= base_url('contacts') ?>?type='+this.value+'&statut=<?= esc($statut ?? '') ?>'">
                <option value="">Tous les types</option>
                <option value="client" <?= ($type ?? '') === 'client' ? 'selected' : '' ?>>Client</option>
                <option value="prospect" <?= ($type ?? '') === 'prospect' ? 'selected' : '' ?>>Prospect</option>
                <option value="fournisseur" <?= ($type ?? '') === 'fournisseur' ? 'selected' : '' ?>>Fournisseur</option>
            </select>
        </div>

        <div class="filter-group">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-control form-select" 
                onchange="window.location.href='<?= base_url('contacts') ?>?statut='+this.value+'&type=<?= esc($type ?? '') ?>'">
                <option value="">Tous les statuts</option>
                <option value="actif" <?= ($statut ?? '') === 'actif' ? 'selected' : '' ?>>Actif</option>
                <option value="inactif" <?= ($statut ?? '') === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                <option value="archive" <?= ($statut ?? '') === 'archive' ? 'selected' : '' ?>>Archivé</option>
            </select>
        </div>
    </div>

    <p class="text-muted mt-3">
        <strong><?= count($contacts ?? []) ?> contact(s) trouvé(s)</strong>
    </p>
</div>

<?php if (empty($contacts ?? [])): ?>
    <div class="card">
        <div class="empty-state">
            <svg viewBox="0 0 20 20" fill="currentColor" style="width: 48px; height: 48px; opacity: 0.5;">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
            <h3>Aucun contact trouvé</h3>
            <p>Commencez par ajouter votre premier contact</p>
            <a href="<?= base_url('contacts/create') ?>" class="btn btn-primary">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Ajouter un contact
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="contacts-grid">
        <?php foreach ($contacts as $contact): ?>
            <div class="contact-card">
                <div class="contact-name"><?= esc($contact['prenom'] . ' ' . $contact['nom']) ?></div>
                <div class="contact-email"><?= esc($contact['email']) ?></div>
                <div class="contact-telephone"><?= esc($contact['telephone']) ?></div>
                <?php if (!empty($contact['entreprise'])): ?>
                    <div class="contact-entreprise"><?= esc($contact['entreprise']) ?></div>
                <?php endif; ?>
                <?php if (!empty($contact['adresse'])): ?>
                    <div class="contact-adresse"><?= esc($contact['adresse']) ?></div>
                <?php endif; ?>
                <div>
                    <span class="contact-type"><?= ucfirst(esc($contact['type'])) ?></span>
                    <span class="contact-statut"><?= ucfirst(esc($contact['statut'])) ?></span>
                </div>

                <div class="contact-actions">
                    <a href="<?= base_url('contacts/edit/' . $contact['id']) ?>" class="btn btn-sm btn-outline">
                        Modifier
                    </a>
                    <form action="<?= base_url('contacts/delete/' . $contact['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce contact ?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-danger">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

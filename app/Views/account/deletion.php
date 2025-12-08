<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Suppression du compte<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/profile.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Security Sub-navigation -->
<div class="profile-tabs">
    <a href="<?= base_url('account/security') ?>" class="profile-tab">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        Vue d'ensemble
    </a>
    <a href="<?= base_url('account/login-history') ?>" class="profile-tab">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
        </svg>
        Historique connexions
    </a>
    <a href="<?= base_url('account/deletion') ?>" class="profile-tab active">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        Suppression compte
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title" style="color: var(--danger);">
            <svg viewBox="0 0 20 20" fill="currentColor" width="20" height="20">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            Suppression du compte
        </h2>
    </div>

    <?php if (isset($deletionRequest) && $deletionRequest): ?>
        <!-- Active deletion request -->
        <div class="deletion-pending">
            <div class="alert alert-warning">
                <h3>⚠️ Suppression planifiée</h3>
                <p>Votre compte est programmé pour être supprimé dans <strong><?= $daysRemaining ?> jour(s)</strong>.</p>
                <p>Date de suppression :
                    <strong><?= date('d/m/Y', strtotime($deletionRequest['scheduled_deletion_at'])) ?></strong></p>
            </div>

            <?php if (!empty($deletionRequest['reason'])): ?>
                <div class="deletion-reason">
                    <h4>Raison de la suppression :</h4>
                    <p><?= nl2br(esc($deletionRequest['reason'])) ?></p>
                </div>
            <?php endif; ?>

            <div class="alert alert-info">
                <h4>Que se passera-t-il ?</h4>
                <ul>
                    <li>Toutes vos données personnelles seront définitivement supprimées</li>
                    <li>Vos factures et documents seront archivés (obligation légale)</li>
                    <li>Vous ne pourrez plus vous connecter après la suppression</li>
                </ul>
            </div>

            <form action="<?= base_url('account/cancel-deletion') ?>" method="post" class="cancel-deletion-form">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary">Annuler la suppression</button>
            </form>
        </div>

    <?php else: ?>
        <!-- Request deletion form -->
        <div class="deletion-request">
            <div class="alert alert-danger">
                <h3>⚠️ Attention</h3>
                <p>La suppression de votre compte est <strong>irréversible</strong>.</p>
            </div>

            <div class="deletion-info">
                <h4>Avant de continuer :</h4>
                <ul>
                    <li><strong>Période de grâce :</strong> Vous aurez 30 jours pour changer d'avis</li>
                    <li><strong>Données supprimées :</strong> Profil, préférences, historique</li>
                    <li><strong>Données conservées :</strong> Factures (7 ans - obligation légale)</li>
                    <li><strong>Accès bloqué :</strong> Vous ne pourrez plus vous connecter</li>
                </ul>
            </div>

            <form action="<?= base_url('account/request-deletion') ?>" method="post" class="deletion-request-form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="reason">Raison de la suppression (optionnel)</label>
                    <textarea class="form-control" id="reason" name="reason" rows="4"
                        placeholder="Dites-nous pourquoi vous souhaitez supprimer votre compte..."></textarea>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="confirm" value="1" required>
                        Je comprends que cette action est irréversible et que mes données seront supprimées dans 30 jours.
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-danger">Demander la suppression</button>
                    <a href="<?= base_url('account/security') ?>" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/profile.js') ?>"></script>
<?= $this->endSection() ?>
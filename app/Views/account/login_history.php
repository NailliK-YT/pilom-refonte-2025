<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Historique des connexions<?= $this->endSection() ?>

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
    <a href="<?= base_url('account/login-history') ?>" class="profile-tab active">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
        </svg>
        Historique connexions
    </a>
    <a href="<?= base_url('account/deletion') ?>" class="profile-tab">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        Suppression compte
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Historique des connexions</h2>
    </div>

    <?php if (empty($history ?? [])): ?>
        <div class="empty-state">
            <svg viewBox="0 0 20 20" fill="currentColor" style="width: 48px; height: 48px; opacity: 0.5;">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
            </svg>
            <h3>Aucune connexion enregistrée</h3>
            <p>L'historique de vos connexions apparaîtra ici.</p>
        </div>
    <?php else: ?>
        <div class="login-history-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Appareil</th>
                        <th>Navigateur</th>
                        <th>IP</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $entry): ?>
                        <tr class="<?= $entry['success'] ? '' : 'failed' ?>">
                            <td><?= date('d/m/Y H:i', strtotime($entry['login_at'])) ?></td>
                            <td>
                                <?= esc($entry['parsed']['device']) ?>
                                <small>(<?= esc($entry['parsed']['platform']) ?>)</small>
                            </td>+
                            <td><?= esc($entry['parsed']['browser']) ?></td>
                            <td><?= esc($entry['ip_address']) ?></td>
                            <td>
                                <?php if ($entry['success'] === 't'): ?>
                                    <span class="badge badge-success">✓ Réussie</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">✗ Échouée</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="form-actions">
        <a href="<?= base_url('account/security') ?>" class="btn btn-outline">
            <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
            </svg>
            Retour
        </a>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/profile.js') ?>"></script>
<?= $this->endSection() ?>
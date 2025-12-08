<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Paramètres') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/settings.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Settings Sub-navigation -->
<div class="settings-tabs">
    <a href="<?= base_url('settings/company') ?>" class="settings-tab <?= strpos(uri_string(), 'settings/company') !== false && strpos(uri_string(), 'legal') === false && strpos(uri_string(), 'invoicing') === false ? 'active' : '' ?>">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
        </svg>
        Informations
    </a>
    <a href="<?= base_url('settings/company/legal') ?>" class="settings-tab <?= strpos(uri_string(), 'legal') !== false ? 'active' : '' ?>">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"/>
        </svg>
        Mentions légales
    </a>
    <a href="<?= base_url('settings/company/invoicing') ?>" class="settings-tab <?= strpos(uri_string(), 'invoicing') !== false ? 'active' : '' ?>">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z" clip-rule="evenodd"/>
        </svg>
        Facturation
    </a>
</div>

<div class="card">
    <?= $this->renderSection('settings_content') ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/settings.js') ?>"></script>
<?= $this->endSection() ?>
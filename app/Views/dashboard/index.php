<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
Tableau de Bord
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1>Tableau de Bord</h1>
<p>Bienvenue sur votre espace de gestion.</p>

<div class="features-grid">
    <div class="feature-card">
        <h3>Chiffre d'Affaires</h3>
        <p style="font-size: 24px; font-weight: bold; color: var(--primary-color);">0,00 €</p>
        <p style="font-size: 14px; color: #666;">Ce mois-ci</p>
    </div>
    <div class="feature-card">
        <h3>Factures en attente</h3>
        <p style="font-size: 24px; font-weight: bold; color: #eab308;">0</p>
    </div>
    <div class="feature-card">
        <h3>Nouveaux Clients</h3>
        <p style="font-size: 24px; font-weight: bold; color: var(--secondary-color);">0</p>
    </div>
</div>
<?= $this->endSection() ?>

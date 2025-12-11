<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
Tableau de Bord
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/dashboard-kpi.css') ?>">
<style>
/* Chart.js responsive containers */
.chart-container { position: relative; height: 250px; }
.chart-container-doughnut { height: 220px; }
.chart-container-bar { height: 220px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Quick Actions Bar -->
<?= $this->include('dashboard/partials/quick_actions') ?>

<!-- KPI Cards -->
<?= $this->include('dashboard/partials/kpi_cards') ?>

<!-- Charts Section -->
<?= $this->include('dashboard/partials/charts') ?>

<!-- Bottom Grid: Invoices & Notifications -->
<div class="dashboard-bottom-grid">
    <!-- Recent Invoices -->
    <?= $this->include('dashboard/partials/recent_invoices') ?>
    
    <!-- Notifications -->
    <?= $this->include('dashboard/partials/notifications') ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script src="<?= base_url('js/dashboard.js') ?>"></script>
<?= $this->endSection() ?>

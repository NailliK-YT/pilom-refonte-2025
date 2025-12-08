<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Statistiques<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="page-header-left">
        <h2>Statistiques & Rapports - <?= $currentYear ?></h2>
    </div>
    <div class="page-header-right">
        <a href="<?= base_url('statistics/export?year=' . $currentYear) ?>" class="btn btn-secondary">
            Exporter CSV
        </a>
    </div>
</div>

<!-- KPIs -->
<div class="stats-kpis">
    <div class="kpi-card">
        <div class="kpi-icon revenue">
            <svg viewBox="0 0 20 20" fill="currentColor"><path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
        </div>
        <div class="kpi-content">
            <span class="kpi-label">CA Total</span>
            <span class="kpi-value"><?= number_format($totalInvoiced, 2, ',', ' ') ?> €</span>
        </div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-icon expenses">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5z" clip-rule="evenodd"/></svg>
        </div>
        <div class="kpi-content">
            <span class="kpi-label">Dépenses</span>
            <span class="kpi-value"><?= number_format($totalExpenses, 2, ',', ' ') ?> €</span>
        </div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-icon margin <?= $margin >= 0 ? 'positive' : 'negative' ?>">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/></svg>
        </div>
        <div class="kpi-content">
            <span class="kpi-label">Marge</span>
            <span class="kpi-value <?= $margin >= 0 ? 'positive' : 'negative' ?>"><?= number_format($margin, 2, ',', ' ') ?> €</span>
        </div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-icon pending">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
        </div>
        <div class="kpi-content">
            <span class="kpi-label">Factures en attente</span>
            <span class="kpi-value"><?= $pendingInvoices ?></span>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="charts-grid">
    <div class="chart-card">
        <h3>Évolution CA & Dépenses</h3>
        <canvas id="revenueExpensesChart"></canvas>
    </div>
    
    <div class="chart-card">
        <h3>Marge Mensuelle</h3>
        <canvas id="marginChart"></canvas>
    </div>
</div>

<!-- Quick Links -->
<div class="stats-links">
    <a href="<?= base_url('statistics/revenue') ?>" class="stat-link-card">
        <h4>Rapport CA détaillé</h4>
        <p>Analyse du chiffre d'affaires</p>
    </a>
    <a href="<?= base_url('statistics/expenses') ?>" class="stat-link-card">
        <h4>Rapport Dépenses</h4>
        <p>Analyse par catégorie</p>
    </a>
    <a href="<?= base_url('statistics/margins') ?>" class="stat-link-card">
        <h4>Rapport Marges</h4>
        <p>Rentabilité mensuelle</p>
    </a>
</div>

<style>
.stats-kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.kpi-card { display: flex; align-items: center; gap: 1rem; padding: 1.5rem; background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); }
.kpi-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.kpi-icon svg { width: 24px; height: 24px; }
.kpi-icon.revenue { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.kpi-icon.expenses { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.kpi-icon.margin { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.kpi-icon.pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.kpi-label { display: block; font-size: 0.875rem; color: var(--text-muted); }
.kpi-value { font-size: 1.5rem; font-weight: 700; color: var(--text-primary); }
.kpi-value.positive { color: #10b981; }
.kpi-value.negative { color: #ef4444; }
.charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.chart-card { background: var(--bg-card); border-radius: 12px; padding: 1.5rem; border: 1px solid var(--border-color); }
.chart-card h3 { margin-bottom: 1rem; font-size: 1rem; color: var(--text-primary); }
.stats-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; }
.stat-link-card { display: block; padding: 1.5rem; background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); text-decoration: none; transition: all 0.2s; }
.stat-link-card:hover { border-color: var(--primary); transform: translateY(-2px); }
.stat-link-card h4 { color: var(--text-primary); margin-bottom: 0.5rem; }
.stat-link-card p { color: var(--text-muted); font-size: 0.875rem; margin: 0; }
</style>

<script>
const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
const revenueData = <?= json_encode(array_values($monthlyRevenue)) ?>;
const expensesData = <?= json_encode(array_values($monthlyExpenses)) ?>;
const marginData = revenueData.map((r, i) => r - expensesData[i]);

new Chart(document.getElementById('revenueExpensesChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [
            { label: 'CA', data: revenueData, backgroundColor: 'rgba(16, 185, 129, 0.8)' },
            { label: 'Dépenses', data: expensesData, backgroundColor: 'rgba(239, 68, 68, 0.8)' }
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
});

new Chart(document.getElementById('marginChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Marge',
            data: marginData,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>
<?= $this->endSection() ?>

<!-- Charts Section -->
<div class="charts-grid">
    <!-- Revenue Evolution Chart -->
    <div class="chart-card chart-card-large">
        <div class="chart-header">
            <h3 class="chart-title">Évolution du chiffre d'affaires</h3>
            <div class="chart-filter">
                <select id="period-filter" class="chart-select">
                    <option value="6" selected>6 mois</option>
                    <option value="12">12 mois</option>
                    <option value="3">3 mois</option>
                </select>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="revenueEvolutionChart"></canvas>
        </div>
    </div>

    <!-- Revenue by Category Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h3 class="chart-title">Répartition des revenus</h3>
        </div>
        <div class="chart-container chart-container-doughnut">
            <canvas id="revenueCategoryChart"></canvas>
        </div>
    </div>

    <!-- Invoice Status Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h3 class="chart-title">Statut des factures</h3>
        </div>
        <div class="chart-container chart-container-bar">
            <canvas id="invoiceStatusChart"></canvas>
        </div>
    </div>
</div>

<script>
// Chart.js initialization data (passed from PHP)
window.dashboardChartData = {
    revenueEvolution: <?= json_encode($revenueEvolution ?? ['labels' => [], 'data' => []]) ?>,
    revenueByCategory: <?= json_encode($revenueByCategory ?? ['labels' => [], 'data' => []]) ?>,
    invoiceStatus: <?= json_encode($invoiceStatus ?? ['labels' => [], 'data' => []]) ?>
};
</script>

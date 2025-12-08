<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="header-left">
        <h1>Trésorerie</h1>
        <p class="page-subtitle">Suivez vos flux financiers en temps réel</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('treasury/alerts') ?>" class="btn btn-outline">
            <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z"/>
            </svg>
            Gérer les alertes
        </a>
        <a href="<?= base_url('treasury/create') ?>" class="btn btn-primary">
            <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
            </svg>
            Nouvelle entrée
        </a>
    </div>
</div>

<?php if (!empty($triggeredAlerts)): ?>
    <div class="alert alert-warning">
        <strong>⚠️ Alertes déclenchées :</strong>
        <?php foreach ($triggeredAlerts as $alert): ?>
            <span class="alert-badge"><?= esc($alert['name']) ?></span>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="treasury-summary">
    <div class="summary-card balance-card <?= $summary['current_balance'] >= 0 ? 'positive' : 'negative' ?>">
        <div class="card-icon">💰</div>
        <div class="card-content">
            <span class="card-label">Solde actuel</span>
            <span class="card-value"><?= number_format($summary['current_balance'], 2, ',', ' ') ?> €</span>
        </div>
    </div>
    
    <div class="summary-card entries-card">
        <div class="card-icon">📈</div>
        <div class="card-content">
            <span class="card-label">Entrées (période)</span>
            <span class="card-value positive-text">+<?= number_format($summary['total_entries'], 2, ',', ' ') ?> €</span>
        </div>
    </div>
    
    <div class="summary-card exits-card">
        <div class="card-icon">📉</div>
        <div class="card-content">
            <span class="card-label">Sorties (période)</span>
            <span class="card-value negative-text">-<?= number_format($summary['total_exits'], 2, ',', ' ') ?> €</span>
        </div>
    </div>
    
    <div class="summary-card flow-card <?= $summary['net_flow'] >= 0 ? 'positive' : 'negative' ?>">
        <div class="card-icon">📊</div>
        <div class="card-content">
            <span class="card-label">Flux net</span>
            <span class="card-value"><?= ($summary['net_flow'] >= 0 ? '+' : '') . number_format($summary['net_flow'], 2, ',', ' ') ?> €</span>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="treasury-chart-section">
    <div class="section-header">
        <h2>Évolution des flux</h2>
        <div class="chart-filters">
            <button class="btn btn-sm active" data-months="6">6 mois</button>
            <button class="btn btn-sm" data-months="12">12 mois</button>
        </div>
    </div>
    <div class="chart-container">
        <canvas id="treasuryChart"></canvas>
    </div>
</div>

<!-- Recent Transactions -->
<div class="treasury-transactions">
    <div class="section-header">
        <h2>Dernières transactions</h2>
        <a href="<?= base_url('treasury/history') ?>" class="btn btn-text">Voir tout →</a>
    </div>
    
    <?php if (empty($recentEntries)): ?>
        <div class="empty-state">
            <p>Aucune transaction enregistrée</p>
            <a href="<?= base_url('treasury/create') ?>" class="btn btn-primary">Ajouter une entrée</a>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Catégorie</th>
                    <th class="text-right">Montant</th>
                    <th class="text-right">Solde</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentEntries as $entry): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($entry['transaction_date'])) ?></td>
                        <td><?= esc($entry['description'] ?? '-') ?></td>
                        <td>
                            <span class="badge badge-<?= $entry['category'] ?>">
                                <?= ucfirst($entry['category'] ?? 'Autre') ?>
                            </span>
                        </td>
                        <td class="text-right <?= $entry['type'] === 'entry' ? 'positive-text' : 'negative-text' ?>">
                            <?= $entry['type'] === 'entry' ? '+' : '-' ?><?= number_format(abs($entry['amount']), 2, ',', ' ') ?> €
                        </td>
                        <td class="text-right"><?= number_format($entry['balance_after'], 2, ',', ' ') ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Alerts Status -->
<?php if (!empty($alerts)): ?>
<div class="treasury-alerts-preview">
    <div class="section-header">
        <h2>Alertes configurées</h2>
    </div>
    <div class="alerts-grid">
        <?php foreach ($alerts as $alert): ?>
            <div class="alert-card <?= $alert['is_active'] ? 'active' : 'inactive' ?>">
                <div class="alert-info">
                    <span class="alert-name"><?= esc($alert['name']) ?></span>
                    <span class="alert-threshold">
                        <?= $alert['threshold_type'] === 'below' ? 'En dessous de' : 'Au-dessus de' ?>
                        <?= number_format($alert['threshold_amount'], 2, ',', ' ') ?> €
                    </span>
                </div>
                <span class="alert-status <?= $alert['is_active'] ? 'status-active' : 'status-inactive' ?>">
                    <?= $alert['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<style>
.treasury-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.summary-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e5e7eb;
}

.summary-card.positive { border-left: 4px solid #10b981; }
.summary-card.negative { border-left: 4px solid #ef4444; }

.card-icon {
    font-size: 2rem;
    background: #f3f4f6;
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
}

.card-label {
    display: block;
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 4px;
}

.card-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
}

.positive-text { color: #10b981 !important; }
.negative-text { color: #ef4444 !important; }

.treasury-chart-section {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 30px;
    border: 1px solid #e5e7eb;
}

.chart-container {
    height: 300px;
    position: relative;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h2 {
    font-size: 1.25rem;
    color: #1f2937;
    margin: 0;
}

.chart-filters .btn {
    margin-left: 8px;
}

.chart-filters .btn.active {
    background: #4E51C0;
    color: white;
}

.treasury-transactions {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 30px;
    border: 1px solid #e5e7eb;
}

.alerts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.alert-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.alert-card.active { border-left: 3px solid #10b981; }
.alert-card.inactive { opacity: 0.6; }

.alert-name {
    display: block;
    font-weight: 500;
    color: #1f2937;
}

.alert-threshold {
    display: block;
    font-size: 0.875rem;
    color: #6b7280;
}

.status-active { color: #10b981; font-weight: 500; }
.status-inactive { color: #9ca3af; }

.alert-badge {
    background: #fef3c7;
    color: #92400e;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.875rem;
    margin-left: 8px;
}

.treasury-alerts-preview {
    background: white;
    border-radius: 12px;
    padding: 24px;
    border: 1px solid #e5e7eb;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('treasuryChart').getContext('2d');
    
    const labels = <?= $chartLabels ?>;
    const entriesData = <?= $chartEntries ?>;
    const exitsData = <?= $chartExits ?>;
    
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels.map(l => {
                const [year, month] = l.split('-');
                const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
                return months[parseInt(month) - 1] + ' ' + year.slice(2);
            }),
            datasets: [
                {
                    label: 'Entrées',
                    data: entriesData,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                },
                {
                    label: 'Sorties',
                    data: exitsData,
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR') + ' €';
                        }
                    }
                }
            }
        }
    });
});
</script>

<?= $this->endSection() ?>

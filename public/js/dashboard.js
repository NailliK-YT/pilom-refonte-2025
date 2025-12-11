/**
 * Pilom Dashboard JavaScript
 * Chart.js initialization and AJAX data refresh
 */

document.addEventListener('DOMContentLoaded', function() {
    // Color scheme from Pilom charte graphique
    const colors = {
        primary: '#4e51c0',
        primaryLight: 'rgba(78, 81, 192, 0.2)',
        secondary: '#1fc187',
        secondaryLight: 'rgba(31, 193, 135, 0.2)',
        warning: '#f59e0b',
        warningLight: 'rgba(245, 158, 11, 0.2)',
        danger: '#dc2626',
        dangerLight: 'rgba(220, 38, 38, 0.2)',
        success: '#16a34a',
        successLight: 'rgba(22, 163, 74, 0.2)',
        gray: '#6b7280',
        grayLight: 'rgba(107, 114, 128, 0.2)'
    };

    // Chart instances
    let revenueChart = null;
    let categoryChart = null;
    let statusChart = null;

    // Initialize charts
    initCharts();

    // Period filter handler
    const periodFilter = document.getElementById('period-filter');
    if (periodFilter) {
        periodFilter.addEventListener('change', function() {
            refreshChartData(this.value);
        });
    }

    // Auto-refresh every 5 minutes
    setInterval(refreshAllData, 300000);

    /**
     * Initialize all charts
     */
    function initCharts() {
        const data = window.dashboardChartData || {};

        // Revenue Evolution Line Chart
        const revenueCtx = document.getElementById('revenueEvolutionChart');
        if (revenueCtx) {
            revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: data.revenueEvolution?.labels || [],
                    datasets: [{
                        label: 'Chiffre d\'affaires',
                        data: data.revenueEvolution?.data || [],
                        borderColor: colors.primary,
                        backgroundColor: colors.primaryLight,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: colors.primary,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return formatCurrency(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(value, true);
                                }
                            },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Revenue by Category Doughnut Chart
        const categoryCtx = document.getElementById('revenueCategoryChart');
        if (categoryCtx) {
            categoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: data.revenueByCategory?.labels || [],
                    datasets: [{
                        data: data.revenueByCategory?.data || [],
                        backgroundColor: [
                            colors.primary,
                            colors.secondary,
                            colors.warning,
                            colors.success,
                            colors.gray
                        ],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + formatCurrency(context.raw);
                                }
                            }
                        }
                    }
                }
            });
        }

        // Invoice Status Bar Chart
        const statusCtx = document.getElementById('invoiceStatusChart');
        if (statusCtx) {
            const statusColors = {
                'Brouillon': colors.gray,
                'En attente': colors.warning,
                'Envoyée': colors.primary,
                'Payée': colors.success,
                'Annulée': colors.danger,
                'Partielle': colors.secondary
            };

            const labels = data.invoiceStatus?.labels || [];
            const backgroundColors = labels.map(label => statusColors[label] || colors.gray);

            statusChart = new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Factures',
                        data: data.invoiceStatus?.data || [],
                        backgroundColor: backgroundColors,
                        borderRadius: 6,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    }

    /**
     * Refresh chart data via AJAX
     */
    function refreshChartData(period) {
        fetch(`${window.location.origin}/dashboard/getChartData?period=${period}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (revenueChart && data.revenueEvolution) {
                revenueChart.data.labels = data.revenueEvolution.labels;
                revenueChart.data.datasets[0].data = data.revenueEvolution.data;
                revenueChart.update('none');
            }
            if (categoryChart && data.revenueByCategory) {
                categoryChart.data.labels = data.revenueByCategory.labels;
                categoryChart.data.datasets[0].data = data.revenueByCategory.data;
                categoryChart.update('none');
            }
            if (statusChart && data.invoiceStatus) {
                statusChart.data.labels = data.invoiceStatus.labels;
                statusChart.data.datasets[0].data = data.invoiceStatus.data;
                statusChart.update('none');
            }
        })
        .catch(error => console.error('Error refreshing chart data:', error));
    }

    /**
     * Refresh KPI data via AJAX
     */
    function refreshKpiData() {
        fetch(`${window.location.origin}/dashboard/getKpiData`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const revenueEl = document.getElementById('kpi-revenue');
            const trendEl = document.getElementById('kpi-revenue-trend');
            const pendingEl = document.getElementById('kpi-pending');
            const treasuryEl = document.getElementById('kpi-treasury');
            const customersEl = document.getElementById('kpi-customers');

            if (revenueEl) revenueEl.textContent = formatCurrency(data.revenue);
            if (pendingEl) pendingEl.textContent = data.pending_invoices;
            if (treasuryEl) treasuryEl.textContent = formatCurrency(data.treasury_balance);
            if (customersEl) customersEl.textContent = data.active_customers;

            if (trendEl) {
                const change = data.revenue_change;
                trendEl.className = 'kpi-trend ' + (change >= 0 ? 'trend-up' : 'trend-down');
                const arrow = change >= 0 
                    ? '<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>'
                    : '<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
                trendEl.innerHTML = arrow + Math.abs(change) + '% vs mois préc.';
            }
        })
        .catch(error => console.error('Error refreshing KPI data:', error));
    }

    /**
     * Refresh all dashboard data
     */
    function refreshAllData() {
        refreshKpiData();
        const period = document.getElementById('period-filter')?.value || 6;
        refreshChartData(period);
    }

    /**
     * Format number as currency
     */
    function formatCurrency(value, compact = false) {
        if (compact && value >= 1000) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR',
                notation: 'compact',
                maximumFractionDigits: 1
            }).format(value);
        }
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(value);
    }
});

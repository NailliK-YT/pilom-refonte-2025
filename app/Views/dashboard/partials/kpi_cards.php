<!-- KPI Cards Section -->
<div class="kpi-grid">
    <!-- Revenue Card -->
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon-primary">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="kpi-content">
            <span class="kpi-label">Chiffre d'affaires</span>
            <span class="kpi-value" id="kpi-revenue"><?= number_format($kpi['revenue'] ?? 0, 2, ',', ' ') ?> €</span>
            <span class="kpi-trend <?= ($kpi['revenue_change'] ?? 0) >= 0 ? 'trend-up' : 'trend-down' ?>" id="kpi-revenue-trend">
                <?php if (($kpi['revenue_change'] ?? 0) >= 0): ?>
                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                <?php else: ?>
                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                <?php endif; ?>
                <?= abs($kpi['revenue_change'] ?? 0) ?>% vs mois préc.
            </span>
        </div>
    </div>

    <!-- Pending Invoices Card -->
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon-warning">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="kpi-content">
            <span class="kpi-label">Factures en attente</span>
            <span class="kpi-value kpi-value-warning" id="kpi-pending"><?= $kpi['pending_invoices'] ?? 0 ?></span>
            <span class="kpi-subtext">À traiter</span>
        </div>
    </div>

    <!-- Treasury Card -->
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon-success">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="kpi-content">
            <span class="kpi-label">Solde trésorerie</span>
            <span class="kpi-value <?= ($kpi['treasury_balance'] ?? 0) >= 0 ? 'kpi-value-success' : 'kpi-value-danger' ?>" id="kpi-treasury">
                <?= number_format($kpi['treasury_balance'] ?? 0, 2, ',', ' ') ?> €
            </span>
            <span class="kpi-subtext">Solde actuel</span>
        </div>
    </div>

    <!-- Active Customers Card -->
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon-secondary">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
        </div>
        <div class="kpi-content">
            <span class="kpi-label">Clients actifs</span>
            <span class="kpi-value" id="kpi-customers"><?= $kpi['active_customers'] ?? 0 ?></span>
            <span class="kpi-subtext">Ce mois-ci</span>
        </div>
    </div>
</div>

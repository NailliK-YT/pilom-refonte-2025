<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="header-left">
        <a href="<?= base_url('treasury') ?>" class="back-link">← Retour à la trésorerie</a>
        <h1>Alertes de trésorerie</h1>
        <p class="page-subtitle">Configurez des alertes pour surveiller votre solde</p>
    </div>
</div>

<div class="alerts-page">
    <div class="current-balance-card">
        <span class="label">Solde actuel</span>
        <span class="value <?= $currentBalance >= 0 ? 'positive' : 'negative' ?>">
            <?= number_format($currentBalance, 2, ',', ' ') ?> €
        </span>
    </div>

    <!-- Create Alert Form -->
    <div class="create-alert-section">
        <h2>Créer une nouvelle alerte</h2>
        
        <form action="<?= base_url('treasury/alerts/store') ?>" method="post" class="alert-form">
            <?= csrf_field() ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Nom de l'alerte <span class="required">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?= old('name') ?>" required placeholder="Ex: Solde bas">
                </div>

                <div class="form-group">
                    <label for="threshold_type">Condition <span class="required">*</span></label>
                    <select class="form-control" id="threshold_type" name="threshold_type" required>
                        <option value="below" <?= old('threshold_type') === 'below' ? 'selected' : '' ?>>En dessous de</option>
                        <option value="above" <?= old('threshold_type') === 'above' ? 'selected' : '' ?>>Au-dessus de</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="threshold_amount">Seuil <span class="required">*</span></label>
                    <div class="input-with-suffix">
                        <input type="number" step="0.01" class="form-control" id="threshold_amount" name="threshold_amount" 
                               value="<?= old('threshold_amount') ?>" required placeholder="1000">
                        <span class="input-suffix">€</span>
                    </div>
                </div>

                <div class="form-group form-action">
                    <button type="submit" class="btn btn-primary">Créer l'alerte</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Existing Alerts -->
    <div class="alerts-list-section">
        <h2>Alertes configurées</h2>
        
        <?php if (empty($alerts)): ?>
            <div class="empty-state">
                <p>Aucune alerte configurée</p>
                <p class="hint">Créez une alerte pour être notifié quand votre solde atteint un certain seuil.</p>
            </div>
        <?php else: ?>
            <div class="alerts-table">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Condition</th>
                            <th>Seuil</th>
                            <th>Statut</th>
                            <th>Dernière alerte</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alerts as $alert): ?>
                            <tr>
                                <td><strong><?= esc($alert['name']) ?></strong></td>
                                <td><?= $alert['threshold_type'] === 'below' ? 'En dessous de' : 'Au-dessus de' ?></td>
                                <td><?= number_format($alert['threshold_amount'], 2, ',', ' ') ?> €</td>
                                <td>
                                    <span class="status-badge <?= $alert['is_active'] ? 'active' : 'inactive' ?>">
                                        <?= $alert['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $alert['last_triggered_at'] 
                                        ? date('d/m/Y H:i', strtotime($alert['last_triggered_at'])) 
                                        : 'Jamais' ?>
                                </td>
                                <td>
                                    <form action="<?= base_url('treasury/alerts/delete/' . $alert['id']) ?>" method="post" 
                                          style="display: inline;" onsubmit="return confirm('Supprimer cette alerte ?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.alerts-page {
    max-width: 900px;
}

.current-balance-card {
    background: white;
    padding: 20px 30px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.current-balance-card .label {
    font-size: 0.875rem;
    color: #6b7280;
}

.current-balance-card .value {
    font-size: 1.5rem;
    font-weight: 700;
}

.current-balance-card .value.positive { color: #10b981; }
.current-balance-card .value.negative { color: #ef4444; }

.create-alert-section,
.alerts-list-section {
    background: white;
    padding: 24px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    margin-bottom: 30px;
}

.create-alert-section h2,
.alerts-list-section h2 {
    font-size: 1.25rem;
    color: #1f2937;
    margin-bottom: 20px;
}

.alert-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr auto;
    gap: 15px;
    align-items: end;
}

@media (max-width: 768px) {
    .alert-form .form-row {
        grid-template-columns: 1fr;
    }
}

.form-action {
    display: flex;
    align-items: flex-end;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.active {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.inactive {
    background: #f3f4f6;
    color: #6b7280;
}

.input-with-suffix {
    position: relative;
}

.input-with-suffix .form-control {
    padding-right: 40px;
}

.input-suffix {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
}

.back-link {
    color: #6b7280;
    text-decoration: none;
    font-size: 0.875rem;
    margin-bottom: 8px;
    display: inline-block;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #6b7280;
}

.hint {
    font-size: 0.875rem;
    color: #9ca3af;
}
</style>

<?= $this->endSection() ?>

<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Journal d'Audit<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Journal d'Audit<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .audit-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .audit-stats {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .stat-card {
        background: var(--bg-primary, white);
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .stat-card .number {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--primary-color, #3b82f6);
    }

    .stat-card .label {
        font-size: 0.75rem;
        color: var(--text-secondary, #64748b);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .filters-card {
        background: var(--bg-primary, white);
        padding: 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }

    .filters-form {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }

    .filter-group label {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--text-secondary, #64748b);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .filter-group select,
    .filter-group input {
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 0.375rem;
        font-size: 0.875rem;
        min-width: 150px;
    }

    .filter-group select:focus,
    .filter-group input:focus {
        outline: none;
        border-color: var(--primary-color, #3b82f6);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary-color, #3b82f6);
        color: white;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .btn-secondary {
        background: var(--bg-tertiary, #f1f5f9);
        color: var(--text-primary, #1e293b);
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    .btn-export {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .btn-export:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .audit-table-container {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .audit-table {
        width: 100%;
        border-collapse: collapse;
    }

    .audit-table th,
    .audit-table td {
        padding: 0.875rem 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
    }

    .audit-table th {
        background: var(--bg-tertiary, #f8f9fa);
        font-weight: 600;
        color: var(--text-secondary, #64748b);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .audit-table tr:hover {
        background: var(--bg-tertiary, #f8f9fa);
    }

    .action-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .action-login {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .action-logout {
        background: rgba(100, 116, 139, 0.1);
        color: #475569;
    }

    .action-create {
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
    }

    .action-update {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }

    .action-delete {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .action-default {
        background: rgba(100, 116, 139, 0.1);
        color: #64748b;
    }

    .user-cell {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 500;
        color: var(--text-primary, #1e293b);
    }

    .user-email {
        font-size: 0.75rem;
        color: var(--text-secondary, #64748b);
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.25rem;
        padding: 1rem;
        border-top: 1px solid var(--border-color, #e2e8f0);
    }

    .pagination a,
    .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        padding: 0 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        text-decoration: none;
        color: var(--text-primary, #1e293b);
        transition: all 0.2s;
    }

    .pagination a:hover {
        background: var(--bg-tertiary, #f1f5f9);
    }

    .pagination .active {
        background: var(--primary-color, #3b82f6);
        color: white;
    }

    .pagination .disabled {
        color: var(--text-secondary, #94a3b8);
        pointer-events: none;
    }

    .ip-address {
        font-family: monospace;
        font-size: 0.75rem;
        color: var(--text-secondary, #64748b);
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-secondary, #64748b);
    }

    .empty-state svg {
        width: 48px;
        height: 48px;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .audit-table {
            display: block;
            overflow-x: auto;
        }

        .filters-form {
            flex-direction: column;
        }

        .filter-group {
            width: 100%;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="audit-page">
    <div class="audit-header">
        <div class="audit-stats">
            <div class="stat-card">
                <div class="number"><?= number_format($totalLogs) ?></div>
                <div class="label">Total événements</div>
            </div>
        </div>

        <a href="<?= base_url('admin/audit/export?' . http_build_query($filters)) ?>" class="btn btn-export">
            <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                <path fill-rule="evenodd"
                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
            Exporter CSV
        </a>
    </div>

    <div class="filters-card">
        <form method="get" action="<?= base_url('admin/audit') ?>" class="filters-form">
            <div class="filter-group">
                <label>Action</label>
                <select name="action">
                    <option value="">Toutes</option>
                    <?php foreach ($actions as $action): ?>
                        <option value="<?= esc($action) ?>" <?= ($filters['action'] ?? '') === $action ? 'selected' : '' ?>>
                            <?= esc($action) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Utilisateur</label>
                <select name="user_id">
                    <option value="">Tous</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= esc($user['id']) ?>" <?= ($filters['user_id'] ?? '') === $user['id'] ? 'selected' : '' ?>>
                            <?= esc(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '') ?: $user['email']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Date début</label>
                <input type="date" name="date_from" value="<?= esc($filters['date_from'] ?? '') ?>">
            </div>

            <div class="filter-group">
                <label>Date fin</label>
                <input type="date" name="date_to" value="<?= esc($filters['date_to'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd"
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                        clip-rule="evenodd" />
                </svg>
                Filtrer
            </button>

            <?php if (!empty(array_filter($filters))): ?>
                <a href="<?= base_url('admin/audit') ?>" class="btn btn-secondary">Réinitialiser</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="audit-table-container">
        <?php if (!empty($logs)): ?>
            <table class="audit-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th>Entité</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <?php
                        $actionClass = 'action-default';
                        if (strpos($log['action'], 'login') !== false)
                            $actionClass = 'action-login';
                        elseif (strpos($log['action'], 'logout') !== false)
                            $actionClass = 'action-logout';
                        elseif (strpos($log['action'], 'create') !== false || strpos($log['action'], 'invite') !== false)
                            $actionClass = 'action-create';
                        elseif (strpos($log['action'], 'update') !== false || strpos($log['action'], 'change') !== false)
                            $actionClass = 'action-update';
                        elseif (strpos($log['action'], 'delete') !== false || strpos($log['action'], 'remove') !== false || strpos($log['action'], 'suspend') !== false)
                            $actionClass = 'action-delete';
                        ?>
                        <tr>
                            <td>
                                <span title="<?= esc($log['created_at']) ?>">
                                    <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                                </span>
                            </td>
                            <td>
                                <div class="user-cell">
                                    <?php if (!empty($log['first_name']) || !empty($log['last_name'])): ?>
                                        <span
                                            class="user-name"><?= esc(trim($log['first_name'] . ' ' . $log['last_name'])) ?></span>
                                    <?php endif; ?>
                                    <span class="user-email"><?= esc($log['user_email'] ?? 'Système') ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="action-badge <?= $actionClass ?>">
                                    <?= esc($log['action']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($log['entity_type'])): ?>
                                    <span><?= esc($log['entity_type']) ?></span>
                                    <?php if (!empty($log['entity_id'])): ?>
                                        <span class="ip-address">#<?= esc(substr($log['entity_id'], 0, 8)) ?>...</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: var(--text-secondary)">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="ip-address"><?= esc($log['ip_address'] ?? '—') ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php
                    $queryParams = array_filter($filters);
                    ?>

                    <?php if ($currentPage > 1): ?>
                        <a
                            href="<?= base_url('admin/audit?' . http_build_query(array_merge($queryParams, ['page' => $currentPage - 1]))) ?>">
                            ←
                        </a>
                    <?php else: ?>
                        <span class="disabled">←</span>
                    <?php endif; ?>

                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);
                    ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <?php if ($i === $currentPage): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="<?= base_url('admin/audit?' . http_build_query(array_merge($queryParams, ['page' => $i]))) ?>">
                                <?= $i ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a
                            href="<?= base_url('admin/audit?' . http_build_query(array_merge($queryParams, ['page' => $currentPage + 1]))) ?>">
                            →
                        </a>
                    <?php else: ?>
                        <span class="disabled">→</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                        clip-rule="evenodd" />
                </svg>
                <h3>Aucun événement</h3>
                <p>Aucun événement ne correspond aux filtres sélectionnés.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
<!-- Recent Invoices Section -->
<div class="card invoices-card">
    <div class="card-header">
        <h3 class="card-title">Dernières factures</h3>
        <a href="<?= base_url('factures') ?>" class="card-link">Voir tout</a>
    </div>
    
    <div class="table-responsive">
        <table class="table invoices-table" id="recent-invoices-table">
            <thead>
                <tr>
                    <th>Numéro</th>
                    <th>Client</th>
                    <th>Montant TTC</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentInvoices)): ?>
                    <?php foreach ($recentInvoices as $invoice): ?>
                        <tr>
                            <td class="invoice-number"><?= esc($invoice['numero_facture']) ?></td>
                            <td class="invoice-client"><?= esc($invoice['client_name']) ?></td>
                            <td class="invoice-amount"><?= number_format($invoice['montant_ttc'], 2, ',', ' ') ?> €</td>
                            <td class="invoice-date"><?= date('d/m/Y', strtotime($invoice['date_emission'])) ?></td>
                            <td>
                                <?php
                                $statusClasses = [
                                    'brouillon' => 'status-draft',
                                    'en_attente' => 'status-pending',
                                    'envoyee' => 'status-sent',
                                    'payee' => 'status-paid',
                                    'annulee' => 'status-cancelled',
                                    'partiellement_payee' => 'status-partial'
                                ];
                                $statusLabels = [
                                    'brouillon' => 'Brouillon',
                                    'en_attente' => 'En attente',
                                    'envoyee' => 'Envoyée',
                                    'payee' => 'Payée',
                                    'annulee' => 'Annulée',
                                    'partiellement_payee' => 'Partielle'
                                ];
                                $statusClass = $statusClasses[$invoice['statut']] ?? 'status-default';
                                $statusLabel = $statusLabels[$invoice['statut']] ?? ucfirst($invoice['statut']);
                                ?>
                                <span class="status-badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                            </td>
                            <td class="invoice-actions">
                                <a href="<?= base_url('factures/show/' . $invoice['id']) ?>" class="action-btn action-view" title="Voir" target="_blank">
                                    <svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                </a>
                                <a href="<?= base_url('factures/send/' . $invoice['id']) ?>" class="action-btn action-send" title="Envoyer">
                                    <svg viewBox="0 0 20 20" fill="currentColor"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-state">
                            <div class="empty-state-content">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="empty-icon">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                                <p>Aucune facture récente</p>
                                <a href="<?= base_url('factures/create') ?>" class="btn btn-primary btn-sm">Créer une facture</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

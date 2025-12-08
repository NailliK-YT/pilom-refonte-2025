<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Transférer la Propriété<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Transfert de Propriété<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .transfer-page {
        max-width: 600px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary-color, #3b82f6);
        text-decoration: none;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .back-link svg {
        width: 16px;
        height: 16px;
    }

    .form-card {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .card-header {
        padding: 1.25rem;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
    }

    .card-header h2 {
        margin: 0;
        font-size: 1.125rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .company-info {
        background: var(--bg-tertiary, #f8f9fa);
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .company-info h3 {
        margin: 0 0 0.25rem;
        font-size: 1rem;
        color: var(--text-primary, #1e293b);
    }

    .company-info p {
        margin: 0;
        font-size: 0.875rem;
        color: var(--text-secondary, #64748b);
    }

    .warning-box {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid #f59e0b;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .warning-box p {
        margin: 0;
        color: #92400e;
        font-size: 0.875rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        color: var(--text-primary, #1e293b);
        margin-bottom: 0.375rem;
        font-size: 0.875rem;
    }

    .form-control {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: border-color 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color, #3b82f6);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .user-option {
        padding: 0.5rem;
    }

    .card-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color, #e2e8f0);
        background: var(--bg-tertiary, #f8f9fa);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }

    .btn-transfer {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
    }

    .btn-transfer:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
    }

    .btn-secondary {
        background: var(--bg-primary, white);
        color: var(--text-primary, #1e293b);
        border: 1px solid var(--border-color, #e2e8f0);
    }

    .btn-secondary:hover {
        background: var(--bg-tertiary, #f8f9fa);
    }

    .btn svg {
        width: 16px;
        height: 16px;
    }

    .no-users {
        text-align: center;
        padding: 2rem;
        color: var(--text-secondary, #64748b);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="transfer-page">
    <a href="<?= base_url('companies') ?>" class="back-link">
        <svg viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                clip-rule="evenodd" />
        </svg>
        Retour à mes entreprises
    </a>

    <?php if (session()->getFlashdata('error')): ?>
        <div
            style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #dc2626; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <div class="card-header">
            <h2>Transférer la Propriété</h2>
        </div>

        <div class="card-body">
            <div class="company-info">
                <h3><?= esc($company['name']) ?></h3>
                <p>Transférer les droits d'administration à un autre utilisateur</p>
            </div>

            <div class="warning-box">
                <p>
                    <strong>⚠️ Attention :</strong> En transférant la propriété, vous perdrez vos droits
                    d'administration sur cette entreprise.
                    Vous serez rétrogradé au rôle d'utilisateur standard.
                </p>
            </div>

            <?php if (empty($users)): ?>
                <div class="no-users">
                    <p>Aucun autre utilisateur dans cette entreprise.</p>
                    <p>Invitez d'abord des utilisateurs avant de pouvoir transférer la propriété.</p>
                </div>
            <?php else: ?>
                <form method="post" action="<?= base_url('companies/transfer/' . $company['id']) ?>"
                    onsubmit="return confirm('Êtes-vous sûr de vouloir transférer la propriété ? Cette action est irréversible.');">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="new_owner_id">Nouveau propriétaire *</label>
                        <select name="new_owner_id" id="new_owner_id" class="form-control" required>
                            <option value="">— Sélectionner un utilisateur —</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= esc($user['user_id']) ?>">
                                    <?= esc($user['user_name'] ?? $user['email']) ?>
                                    (<?= ucfirst(esc($user['role_name'] ?? 'Utilisateur')) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="card-footer" style="margin: 0 -1.5rem -1.5rem; padding: 1rem 1.5rem;">
                        <a href="<?= base_url('companies') ?>" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-transfer">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8zM12 15a1 1 0 100-2H6.414l1.293-1.293a1 1 0 10-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L6.414 15H12z" />
                            </svg>
                            Transférer la propriété
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
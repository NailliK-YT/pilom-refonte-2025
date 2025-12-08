<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Modifier le rôle<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Modifier le rôle de l'utilisateur<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .edit-form-container {
        max-width: 600px;
        margin: 0 auto;
    }

    .user-profile-card {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-avatar-large {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.5rem;
    }

    .user-avatar-large img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .user-details h3 {
        margin: 0 0 0.25rem;
        color: var(--text-primary, #1e293b);
    }

    .user-details p {
        margin: 0;
        color: var(--text-secondary, #64748b);
        font-size: 0.875rem;
    }

    .edit-card {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .edit-card-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--text-primary, #1e293b);
    }

    .role-selector {
        display: grid;
        gap: 0.75rem;
    }

    .role-option {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem;
        border: 2px solid var(--border-color, #e2e8f0);
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .role-option:hover {
        border-color: var(--primary-color, #3b82f6);
        background: rgba(59, 130, 246, 0.05);
    }

    .role-option.selected {
        border-color: var(--primary-color, #3b82f6);
        background: rgba(59, 130, 246, 0.05);
    }

    .role-info {
        flex: 1;
    }

    .role-name {
        font-weight: 600;
        color: var(--text-primary, #1e293b);
    }

    .role-description {
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: var(--text-secondary, #64748b);
    }

    .edit-card-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--border-color, #e2e8f0);
        background: var(--bg-tertiary, #f8f9fa);
        display: flex;
        justify-content: space-between;
        gap: 1rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
    }

    .btn-secondary {
        background: var(--bg-primary, white);
        color: var(--text-primary, #1e293b);
        border: 1px solid var(--border-color, #e2e8f0);
    }

    .btn-secondary:hover {
        background: var(--bg-tertiary, #f1f5f9);
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="edit-form-container">
    <?php
    $fullName = trim(($userCompany['first_name'] ?? '') . ' ' . ($userCompany['last_name'] ?? ''));
    $displayName = $fullName ?: $userCompany['email'];
    $initial = strtoupper(substr($displayName, 0, 1));
    ?>

    <div class="user-profile-card">
        <div class="user-avatar-large">
            <?php if (!empty($userCompany['avatar'])): ?>
                <img src="<?= base_url($userCompany['avatar']) ?>" alt="<?= esc($displayName) ?>">
            <?php else: ?>
                <?= $initial ?>
            <?php endif; ?>
        </div>
        <div class="user-details">
            <h3><?= esc($displayName) ?></h3>
            <p><?= esc($userCompany['email']) ?></p>
        </div>
    </div>

    <div class="edit-card">
        <form action="<?= base_url('admin/users/update/' . $userCompany['user_id']) ?>" method="post">
            <?= csrf_field() ?>

            <div class="edit-card-body">
                <div class="form-group">
                    <label>Sélectionnez le nouveau rôle</label>
                    <div class="role-selector">
                        <?php foreach ($roles as $role): ?>
                            <label class="role-option <?= $userCompany['role_id'] == $role['id'] ? 'selected' : '' ?>"
                                onclick="this.querySelector('input').checked = true; 
                                            document.querySelectorAll('.role-option').forEach(o => o.classList.remove('selected'));
                                            this.classList.add('selected');">
                                <input type="radio" name="role_id" value="<?= esc($role['id']) ?>"
                                    <?= $userCompany['role_id'] == $role['id'] ? 'checked' : '' ?> required>
                                <div class="role-info">
                                    <div class="role-name"><?= esc(ucfirst($role['name'])) ?></div>
                                    <p class="role-description"><?= esc($role['description']) ?></p>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="edit-card-footer">
                <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
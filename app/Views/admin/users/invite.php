<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Inviter un utilisateur<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Inviter un utilisateur<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .invite-form-container {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .invite-card {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .invite-card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
    }
    
    .invite-card-header h2 {
        margin: 0 0 0.5rem;
        font-size: 1.25rem;
        color: var(--text-primary, #1e293b);
    }
    
    .invite-card-header p {
        margin: 0;
        color: var(--text-secondary, #64748b);
        font-size: 0.875rem;
    }
    
    .invite-card-body {
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
    
    .form-group label .required {
        color: #dc2626;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--primary-color, #3b82f6);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }
    
    .form-control::placeholder {
        color: var(--text-secondary, #94a3b8);
    }
    
    .form-help {
        margin-top: 0.5rem;
        font-size: 0.75rem;
        color: var(--text-secondary, #64748b);
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
    
    .role-option input {
        margin-top: 0.25rem;
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
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .role-badge {
        font-size: 0.625rem;
        padding: 0.125rem 0.375rem;
        border-radius: 9999px;
        font-weight: 500;
    }
    
    .role-admin .role-badge {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }
    
    .role-user .role-badge {
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
    }
    
    .role-comptable .role-badge {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }
    
    .role-description {
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: var(--text-secondary, #64748b);
    }
    
    .invite-card-footer {
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
    
    @media (max-width: 640px) {
        .invite-card-footer {
            flex-direction: column-reverse;
        }
        
        .btn {
            width: 100%;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="invite-form-container">
    <div class="invite-card">
        <div class="invite-card-header">
            <h2>Inviter un nouvel utilisateur</h2>
            <p>Envoyez une invitation par email pour ajouter un collaborateur à <?= esc($company['name'] ?? 'votre entreprise') ?>.</p>
        </div>
        
        <form action="<?= base_url('admin/users/invite') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="invite-card-body">
                <div class="form-group">
                    <label for="email">
                        Adresse email <span class="required">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="collegue@example.com"
                           value="<?= old('email') ?>"
                           required
                           autofocus>
                    <p class="form-help">L'utilisateur recevra un email d'invitation à cette adresse.</p>
                </div>
                
                <div class="form-group">
                    <label>Rôle <span class="required">*</span></label>
                    <div class="role-selector">
                        <?php foreach ($roles as $role): ?>
                            <label class="role-option role-<?= esc($role['name']) ?>" 
                                   onclick="this.querySelector('input').checked = true; 
                                            document.querySelectorAll('.role-option').forEach(o => o.classList.remove('selected'));
                                            this.classList.add('selected');">
                                <input type="radio" 
                                       name="role_id" 
                                       value="<?= esc($role['id']) ?>"
                                       <?= old('role_id') == $role['id'] ? 'checked' : '' ?>
                                       <?= $role['name'] === 'user' && !old('role_id') ? 'checked' : '' ?>
                                       required>
                                <div class="role-info">
                                    <div class="role-name">
                                        <?= esc(ucfirst($role['name'])) ?>
                                        <?php if ($role['name'] === 'admin'): ?>
                                            <span class="role-badge">Tous les droits</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="role-description"><?= esc($role['description']) ?></p>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="message">Message personnalisé (optionnel)</label>
                    <textarea id="message" 
                              name="message" 
                              class="form-control" 
                              rows="3"
                              placeholder="Ajoutez un message personnel à l'invitation..."><?= old('message') ?></textarea>
                </div>
            </div>
            
            <div class="invite-card-footer">
                <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                    </svg>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                    </svg>
                    Envoyer l'invitation
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

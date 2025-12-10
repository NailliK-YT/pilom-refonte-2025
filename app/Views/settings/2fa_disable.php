<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Désactiver l'A2F<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="security-card" style="max-width: 500px; margin: 2rem auto;">
    <div class="card-header" style="text-align: center; padding: 2rem 1.5rem 1rem;">
        <div
            style="width: 64px; height: 64px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
                <path
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h2 style="margin: 0 0 0.5rem; color: #1f2937;">Désactiver l'authentification à deux facteurs</h2>
        <p style="color: #6b7280; margin: 0;">Cette action réduira la sécurité de votre compte.</p>
    </div>

    <div class="card-body" style="padding: 1.5rem;">

        <div style="background: #fef3c7; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
            <p style="margin: 0; color: #92400e; font-size: 0.875rem;">
                <strong>⚠️ Attention :</strong> Une fois désactivée, votre compte sera protégé uniquement par votre mot
                de passe.
            </p>
        </div>

        <form action="<?= base_url('account/2fa/disable') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="password" style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: #374151;">
                    Confirmez votre mot de passe
                </label>
                <input type="password" id="password" name="password" class="form-control" required
                    autocomplete="current-password" placeholder="Entrez votre mot de passe"
                    style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 1rem; box-sizing: border-box;">
            </div>

            <div style="display: flex; gap: 1rem;">
                <a href="<?= base_url('account/security') ?>" class="btn btn-outline"
                    style="flex: 1; text-align: center; padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; text-decoration: none; color: #374151;">
                    Annuler
                </a>
                <button type="submit" class="btn btn-danger"
                    style="flex: 1; padding: 0.75rem 1rem; background: #dc2626; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Désactiver l'A2F
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Codes de secours<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="page-header">
        <div class="success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                <path d="m9 12 2 2 4-4"></path>
            </svg>
        </div>
        <h1>
            <?php if (isset($regenerated) && $regenerated): ?>
                Nouveaux codes de secours générés
            <?php else: ?>
                Authentification à deux facteurs activée !
            <?php endif; ?>
        </h1>
        <p class="subtitle">Conservez ces codes de secours en lieu sûr</p>
    </div>

    <div class="backup-codes-container">
        <div class="alert alert-warning">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <div>
                <strong>Important :</strong> Ces codes ne seront affichés qu'une seule fois.
                Si vous perdez l'accès à votre application d'authentification, utilisez ces codes pour vous connecter.
            </div>
        </div>

        <div class="codes-card">
            <h3>Vos 10 codes de secours</h3>
            <p class="hint">Chaque code ne peut être utilisé qu'une seule fois.</p>

            <div class="codes-grid">
                <?php foreach ($backupCodes as $code): ?>
                    <div class="backup-code">
                        <code><?= esc($code) ?></code>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="codes-actions">
                <button type="button" class="btn btn-secondary" onclick="downloadCodes()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Télécharger
                </button>
                <button type="button" class="btn btn-outline" onclick="copyCodes()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    Copier
                </button>
                <button type="button" class="btn btn-outline" onclick="printCodes()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Imprimer
                </button>
            </div>
        </div>

        <div class="confirmation-section">
            <label class="checkbox-label">
                <input type="checkbox" id="save-confirm" onchange="toggleContinue()">
                <span>J'ai sauvegardé mes codes de secours en lieu sûr</span>
            </label>

            <button type="button" class="btn btn-primary btn-lg" id="continue-btn" disabled
                onclick="window.location.href='<?= base_url('account/security') ?>'">
                Continuer vers les paramètres
            </button>
        </div>
    </div>
</div>

<style>
    .backup-codes-container {
        max-width: 600px;
        margin: 0 auto;
    }

    .success-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #16a34a;
    }

    .page-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .alert-warning {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #f59e0b;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        color: #92400e;
    }

    .alert-warning svg {
        flex-shrink: 0;
        margin-top: 2px;
    }

    .codes-card {
        background: var(--bg-card, white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }

    .codes-card h3 {
        margin: 0 0 0.25rem;
        font-size: 1.1rem;
    }

    .codes-card .hint {
        color: var(--text-secondary, #64748b);
        font-size: 0.875rem;
        margin: 0 0 1rem;
    }

    .codes-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .backup-code {
        background: var(--bg-secondary, #f8fafc);
        padding: 0.75rem;
        border-radius: 8px;
        text-align: center;
    }

    .backup-code code {
        font-family: 'Courier New', monospace;
        font-size: 1.1rem;
        font-weight: 600;
        letter-spacing: 1px;
        color: var(--text-primary, #1e293b);
    }

    .codes-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .codes-actions .btn {
        flex: 1;
        min-width: 120px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .confirmation-section {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        align-items: center;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .checkbox-label input[type="checkbox"] {
        width: 20px;
        height: 20px;
        accent-color: var(--primary-color, #3b82f6);
    }

    #continue-btn {
        width: 100%;
        max-width: 300px;
        transition: all 0.3s ease;
    }

    #continue-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #94a3b8 !important;
        border-color: #94a3b8 !important;
    }

    #continue-btn:not(:disabled) {
        background-color: var(--primary-color, #4e51c0);
    }

    #continue-btn:not(:disabled):hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(78, 81, 192, 0.4);
    }

    @media print {

        .codes-actions,
        .confirmation-section,
        .page-header .subtitle,
        .alert-warning {
            display: none !important;
        }

        .codes-card {
            box-shadow: none;
            border: 2px solid #000;
        }
    }
</style>

<script>
    const backupCodes = <?= json_encode($backupCodes) ?>;

    function toggleContinue() {
        const checkbox = document.getElementById('save-confirm');
        const btn = document.getElementById('continue-btn');
        btn.disabled = !checkbox.checked;
        if (checkbox.checked) {
            btn.removeAttribute('disabled');
        } else {
            btn.setAttribute('disabled', 'disabled');
        }
    }

    function downloadCodes() {
        const content = `Pilom - Codes de secours 2FA
Générés le: ${new Date().toLocaleString('fr-FR')}

Chaque code ne peut être utilisé qu'une seule fois.
Conservez ce fichier en lieu sûr.

${backupCodes.map((code, i) => `${i + 1}. ${code}`).join('\n')}

---
Important: Ne partagez jamais ces codes avec qui que ce soit.
`;

        const blob = new Blob([content], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'pilom-backup-codes.txt';
        a.click();
        URL.revokeObjectURL(url);
    }

    function copyCodes() {
        const text = backupCodes.join('\n');
        navigator.clipboard.writeText(text).then(() => {
            alert('Codes copiés dans le presse-papier !');
        }).catch(() => {
            alert('Erreur lors de la copie. Veuillez copier manuellement.');
        });
    }

    function printCodes() {
        window.print();
    }
</script>
<?= $this->endSection() ?>
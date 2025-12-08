<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dernière étape - Pilom</title>
    <link rel="stylesheet" href="<?= base_url('css/registration.css') ?>">
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <a href="<?= base_url('/') ?>"
                style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 2L2 9V23L16 30L30 23V9L16 2Z" fill="#4E51C0" />
                    <circle cx="16" cy="16" r="6" fill="white" />
                </svg>
                pilom
            </a>
        </div>
    </nav>

    <div class="registration-container">
        <div class="registration-header">
            <h1>Créer votre compte Pilom</h1>
            <p class="subtitle">Essai gratuit de 30 jours • Sans carte bancaire • Annulable à tout moment</p>
        </div>

        <!-- Progress Indicator -->
        <div class="progress-section">
            <div class="progress-label">Étape 3 sur 3</div>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: 100%;"></div>
            </div>
            <div class="progress-percentage">100%</div>
        </div>

        <!-- Step Circles -->
        <div class="step-circles">
            <div class="step-circle completed">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 8L6 11L13 4" stroke="white" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </div>
            <div class="step-line completed"></div>
            <div class="step-circle completed">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 8L6 11L13 4" stroke="white" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </div>
            <div class="step-line completed"></div>
            <div class="step-circle active">3</div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <h2>Dernière étape !</h2>
            <p class="form-subtitle">Confirmez les informations et acceptez les conditions</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <!-- Summary Section -->
            <div class="summary-section">
                <h3>Récapitulatif</h3>

                <div class="summary-item">
                    <span class="summary-label">Email :</span>
                    <span class="summary-value"><?= esc($sessionData['data']['email'] ?? '') ?></span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Entreprise :</span>
                    <span class="summary-value"><?= esc($sessionData['data']['company_name'] ?? '') ?></span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Secteur :</span>
                    <span class="summary-value"><?= esc($sector['name'] ?? '') ?></span>
                </div>
            </div>

            <form action="<?= base_url('register/complete') ?>" method="post" id="step3Form">
                <?= csrf_field() ?>

                <div class="form-group checkbox-group">
                    <label class="checkbox-container">
                        <input type="checkbox" name="accept_terms" id="accept_terms" value="1" required>
                        <span class="checkmark"></span>
                        <span class="checkbox-label">
                            J'accepte les
                            <a href="<?= base_url('terms') ?>" target="_blank">conditions d'utilisation</a>
                            et la
                            <a href="<?= base_url('privacy') ?>" target="_blank">politique de confidentialité</a>
                            de Pilom. Je comprends que je peux annuler mon essai gratuit à tout moment.
                        </span>
                    </label>
                </div>

                <div class="info-box">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18Z"
                            stroke="#4E51C0" stroke-width="2" />
                        <path d="M10 14V10M10 6H10.01" stroke="#4E51C0" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <p>Votre essai gratuit de 30 jours commence aujourd'hui. Vous ne serez pas facturé avant la fin de
                        la période d'essai.</p>
                </div>

                <div class="form-actions">
                    <a href="<?= base_url('register/step2') ?>" class="btn btn-secondary">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 4L7 10L13 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        Retour
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Créer mon compte
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 10H16M16 10L11 5M16 10L11 15" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </form>

            <p class="form-footer">
                Vous avez déjà un compte ? <a href="<?= base_url('login') ?>">Se connecter</a>
            </p>
        </div>
    </div>

    <div class="terms-tooltip" id="termsTooltip">
        <p>Veuillez accepter les conditions d'utilisation pour continuer</p>
    </div>

    <script>
        // Validate terms acceptance
        document.getElementById('step3Form').addEventListener('submit', function (e) {
            const termsAccepted = document.getElementById('accept_terms').checked;

            if (!termsAccepted) {
                e.preventDefault();
                alert('Vous devez accepter les conditions d\'utilisation pour continuer.');
            }
        });
    </script>
</body>

</html>
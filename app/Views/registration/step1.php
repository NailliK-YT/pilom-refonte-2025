<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer votre compte Pilom</title>
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
            <div class="progress-label">Étape 1 sur 3</div>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: 33%;"></div>
            </div>
            <div class="progress-percentage">33%</div>
        </div>

        <!-- Step Circles -->
        <div class="step-circles">
            <div class="step-circle active">1</div>
            <div class="step-line"></div>
            <div class="step-circle">2</div>
            <div class="step-line"></div>
            <div class="step-circle">3</div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <h2>Informations du compte</h2>
            <p class="form-subtitle">Créez votre compte pour commencer</p>

            <?php if (isset($validation)): ?>
                <div class="alert alert-error">
                    <?= $validation->listErrors() ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('register/step1') ?>" method="post" id="step1Form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email">Adresse email professionnelle</label>
                    <input type="email" name="email" id="email" placeholder="vous@entreprise.fr"
                        value="<?= old('email', $sessionData['data']['email'] ?? '') ?>" required>
                    <span class="validation-message" id="emailValidation"></span>
                </div>

                <div class="form-group">
                    <label for="company_name">Nom de l'entreprise</label>
                    <input type="text" name="company_name" id="company_name" placeholder="Mon Entreprise SARL"
                        value="<?= old('company_name', $sessionData['data']['company_name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" placeholder="Minimum 8 caractères" required>
                    <small class="form-hint">Au moins 8 caractères avec lettres et chiffres</small>
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="strength-text" id="strengthText"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirm" id="password_confirm"
                        placeholder="Retapez votre mot de passe" required>
                    <span class="validation-message" id="passwordConfirmValidation"></span>
                </div>

                <button type="submit" class="btn btn-primary">
                    Continuer
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </form>

            <p class="form-footer">
                Vous avez déjà un compte ? <a href="<?= base_url('login') ?>">Se connecter</a>
            </p>
        </div>
    </div>

    <script src="<?= base_url('js/registration.js') ?>"></script>
    <script>
        // Initialize step 1 validation
        if (typeof RegistrationForm !== 'undefined') {
            const form = new RegistrationForm(1);
            form.init();
        }
    </script>
</body>

</html>
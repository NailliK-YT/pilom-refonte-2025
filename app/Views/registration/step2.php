<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre secteur d'activité - Pilom</title>
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
            <div class="progress-label">Étape 2 sur 3</div>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: 67%;"></div>
            </div>
            <div class="progress-percentage">67%</div>
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
            <div class="step-circle active">2</div>
            <div class="step-line"></div>
            <div class="step-circle">3</div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <h2>Votre secteur d'activité</h2>
            <p class="form-subtitle">Cela nous aide à personnaliser votre expérience</p>

            <?php if (isset($validation)): ?>
                <div class="alert alert-error">
                    <?= $validation->listErrors() ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('register/step2') ?>" method="post" id="step2Form">
                <?= csrf_field() ?>

                <input type="hidden" name="business_sector_id" id="selectedSector"
                    value="<?= old('business_sector_id', $sessionData['data']['business_sector_id'] ?? '') ?>">

                <div class="sector-grid">
                    <?php foreach ($sectors as $sector): ?>
                        <div class="sector-card <?= (old('business_sector_id', $sessionData['data']['business_sector_id'] ?? '') == $sector['id']) ? 'selected' : '' ?>"
                            data-sector-id="<?= esc($sector['id']) ?>" onclick="selectSector('<?= esc($sector['id']) ?>')">
                            <div class="sector-name"><?= esc($sector['name']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <span class="validation-message" id="sectorValidation"></span>

                <div class="form-actions">
                    <a href="<?= base_url('register/step1') ?>" class="btn btn-secondary">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 4L7 10L13 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        Retour
                    </a>
                    <button type="submit" class="btn btn-primary" id="continueBtn" disabled>
                        Continuer
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </form>

            <p class="form-footer">
                Vous avez déjà un compte ? <a href="<?= base_url('login') ?>">Se connecter</a>
            </p>
        </div>
    </div>

    <script src="<?= base_url('js/registration.js') ?>"></script>
    <script>
        function selectSector(sectorId) {
            // Remove selected class from all cards
            document.querySelectorAll('.sector-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');

            // Set hidden input value
            document.getElementById('selectedSector').value = sectorId;

            // Enable continue button
            document.getElementById('continueBtn').disabled = false;
        }

        // Check if a sector is already selected
        const selectedValue = document.getElementById('selectedSector').value;
        if (selectedValue) {
            document.getElementById('continueBtn').disabled = false;
        }
    </script>
</body>

</html>
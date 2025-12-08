<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription réussie - Pilom</title>
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
        <div class="success-card">
            <div class="success-icon">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="40" cy="40" r="40" fill="#1fc187" opacity="0.1" />
                    <circle cx="40" cy="40" r="30" fill="#1fc187" />
                    <path d="M25 40L35 50L55 30" stroke="white" stroke-width="4" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </div>

            <h1>Inscription réussie !</h1>
            <p class="success-message">
                Merci de vous être inscrit à Pilom. Votre compte a été créé avec succès.
            </p>

            <div class="info-box success-info">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M2 5L10 10L18 5M2 5L10 14L18 5M2 5V15C2 15.5523 2.44772 16 3 16H17C17.5523 16 18 15.5523 18 15V5"
                        stroke="#4E51C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div>
                    <strong>Vérifiez votre email</strong>
                    <p>Nous avons envoyé un email de vérification à <strong><?= esc($email) ?></strong>.
                        Cliquez sur le lien dans l'email pour activer votre compte.</p>
                </div>
            </div>

            <div class="next-steps">
                <h3>Prochaines étapes :</h3>
                <ol>
                    <li>Vérifiez votre boîte de réception (et vos spams)</li>
                    <li>Cliquez sur le lien de vérification dans l'email</li>
                    <li>Connectez-vous et commencez votre essai gratuit de 30 jours</li>
                </ol>
            </div>

            <div class="success-actions">
                <a href="<?= base_url('login') ?>" class="btn btn-primary">
                    Se connecter
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
                <a href="<?= base_url('/') ?>" class="btn btn-secondary">Retour à l'accueil</a>
            </div>

            <p class="help-text">
                Vous n'avez pas reçu l'email ?
                <a href="#"
                    onclick="alert('La fonctionnalité de renvoi sera disponible prochainement.'); return false;">Renvoyer
                    l'email</a>
            </p>
        </div>
    </div>
</body>

</html>
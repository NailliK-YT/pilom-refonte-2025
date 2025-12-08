<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification email - Pilom</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .email-container {
            background-color: white;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: bold;
            color: #4E51C0;
        }

        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 20px;
            color: #666;
        }

        .btn {
            display: inline-block;
            padding: 14px 32px;
            background-color: #4E51C0;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
        }

        .btn:hover {
            background-color: #3a3d91;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #999;
        }

        .link-text {
            word-break: break-all;
            color: #666;
            font-size: 12px;
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="logo">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 2L2 9V23L16 30L30 23V9L16 2Z" fill="#4E51C0" />
                <circle cx="16" cy="16" r="6" fill="white" />
            </svg>
            pilom
        </div>

        <h1>Bienvenue sur Pilom !</h1>

        <p>Merci de vous être inscrit. Pour commencer à utiliser Pilom, veuillez vérifier votre adresse email en
            cliquant sur le bouton ci-dessous :</p>

        <center>
            <a href="<?= $verification_link ?>" class="btn">Vérifier mon email</a>
        </center>

        <p>Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :</p>
        <div class="link-text"><?= $verification_link ?></div>

        <p><strong>Ce lien expirera dans 24 heures.</strong></p>

        <div class="footer">
            <p>Si vous n'avez pas créé de compte Pilom, vous pouvez ignorer cet email en toute sécurité.</p>
            <p>L'équipe Pilom</p>
        </div>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu #<?= esc($reglement['id']) ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .company-info h1 {
            margin: 0;
            color: #4e51c0;
        }
        .invoice-details {
            text-align: right;
        }
        .addresses {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .address-box {
            width: 45%;
        }
        .address-box h3 {
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background: #f9f9f9;
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #eee;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        .total-row.final {
            font-weight: bold;
            font-size: 1.2em;
            border-top: 2px solid #eee;
            margin-top: 10px;
            padding-top: 10px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.8em;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" 
            style="padding: 10px 20px; background: #4e51c0; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Imprimer / Enregistrer en PDF
        </button>
    </div>

    <div class="header">
        <div class="company-info">
            <h1>Pilom</h1>
            <p>Votre solution de gestion</p>
        </div>
        <div class="invoice-details">
            <h2>REÇU DE PAIEMENT</h2>
            <p><strong>Reçu n° :</strong> <?= esc($reglement['id']) ?></p>
            <p><strong>Date du paiement :</strong> <?= date('d/m/Y', strtotime($reglement['date_reglement'])) ?></p>
            <p><strong>Facture liée :</strong> N° <?= esc($facture['numero_facture']) ?></p>
        </div>
    </div>

    <div class="addresses">
        <div class="address-box">
            <h3>Émetteur</h3>
            <p>
                <strong>Mon Entreprise</strong><br>
                123 Rue de l'Innovation<br>
                75000 Paris<br>
                France<br>
                contact@pilom.fr
            </p>
        </div>
        <div class="address-box">
            <h3>Client</h3>
            <p>
                <strong><?= esc($contact['prenom'] . ' ' . $contact['nom']) ?></strong><br>
                <?php if (!empty($contact['entreprise'])): ?>
                    <?= esc($contact['entreprise']) ?><br>
                <?php endif; ?>
                <?= esc($contact['email']) ?><br>
                <?= esc($contact['telephone'] ?? '') ?>
            </p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Détail du paiement</th>
                <th style="text-align:right;">Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Paiement effectué via : <strong><?= esc($reglement['mode_paiement']) ?></strong></td>
                <td style="text-align:right;"><?= number_format($reglement['montant'], 2, ',', ' ') ?> €</td>
            </tr>
            <?php if (!empty($reglement['reference'])): ?>
            <tr>
                <td>Référence</td>
                <td style="text-align:right;"><?= esc($reglement['reference']) ?></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row final">
            <span>Montant payé</span>
            <span><?= number_format($reglement['montant'], 2, ',', ' ') ?> €</span>
        </div>
    </div>

    <div class="footer">
        <p>Ce document atteste que le paiement ci-dessus a été reçu.</p>
        <p>Pilom - SAS au capital de 10 000 € - SIRET 123 456 789 00012</p>
    </div>

	<script>
        // Lancer l'impression automatiquement si demandé
        window.onload = function() {
            <?php if (isset($autoPrint) && $autoPrint): ?>
            window.print();
            <?php endif; ?>
        }
    </script>

</body>
</html>

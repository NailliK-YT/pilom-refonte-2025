<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #<?= esc($facture['numero_facture']) ?></title>
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
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4e51c0; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Imprimer / Enregistrer en PDF
        </button>
    </div>

    <div class="header">
        <div class="company-info">
            <h1>Pilom</h1>
            <p>Votre solution de gestion</p>
        </div>
        <div class="invoice-details">
            <h2>FACTURE</h2>
            <p><strong>N° :</strong> <?= esc($facture['numero_facture']) ?></p>
            <p><strong>Date :</strong> <?= date('d/m/Y', strtotime($facture['date_emission'])) ?></p>
            <p><strong>Échéance :</strong> <?= date('d/m/Y', strtotime($facture['date_echeance'])) ?></p>
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
                <th>Description</th>
                <th style="text-align: right;">Montant HT</th>
                <th style="text-align: right;">TVA (20%)</th>
                <th style="text-align: right;">Total TTC</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Prestation de services / Vente de produits</td>
                <td style="text-align: right;"><?= number_format($facture['montant_ht'], 2, ',', ' ') ?> €</td>
                <td style="text-align: right;"><?= number_format($facture['montant_tva'], 2, ',', ' ') ?> €</td>
                <td style="text-align: right;"><?= number_format($facture['montant_ttc'], 2, ',', ' ') ?> €</td>
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span>Total HT</span>
            <span><?= number_format($facture['montant_ht'], 2, ',', ' ') ?> €</span>
        </div>
        <div class="total-row">
            <span>Total TVA</span>
            <span><?= number_format($facture['montant_tva'], 2, ',', ' ') ?> €</span>
        </div>
        <div class="total-row final">
            <span>Net à payer</span>
            <span><?= number_format($facture['montant_ttc'], 2, ',', ' ') ?> €</span>
        </div>
    </div>

    <div class="footer">
        <p>Merci de votre confiance !</p>
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

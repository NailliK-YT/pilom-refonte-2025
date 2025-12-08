<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Politique de Confidentialité</h1>
    <p class="page-subtitle">Comment nous protégeons vos données personnelles</p>
</div>

<div class="legal-content">
    <section class="legal-section">
        <h2>1. Introduction</h2>
        <p>
            Chez Pilom, nous accordons une importance primordiale à la protection de vos données personnelles. 
            Cette politique de confidentialité explique comment nous collectons, utilisons et protégeons vos informations 
            conformément au Règlement Général sur la Protection des Données (RGPD).
        </p>
    </section>

    <section class="legal-section">
        <h2>2. Responsable du traitement</h2>
        <p>
            Le responsable du traitement des données est :<br>
            <strong>Pilom SAS</strong><br>
            123 Avenue des Entrepreneurs, 75001 Paris, France<br>
            Email : <a href="mailto:dpo@pilom.fr">dpo@pilom.fr</a>
        </p>
    </section>

    <section class="legal-section">
        <h2>3. Données collectées</h2>
        <p>Nous collectons les données suivantes :</p>
        <ul>
            <li><strong>Données d'identification :</strong> nom, prénom, email, téléphone</li>
            <li><strong>Données professionnelles :</strong> nom de l'entreprise, SIRET, adresse</li>
            <li><strong>Données de connexion :</strong> adresse IP, navigateur, historique de connexion</li>
            <li><strong>Données d'utilisation :</strong> factures, devis, informations clients créées</li>
        </ul>
    </section>

    <section class="legal-section">
        <h2>4. Finalités du traitement</h2>
        <p>Vos données sont utilisées pour :</p>
        <ul>
            <li>La fourniture et l'amélioration de nos services</li>
            <li>La gestion de votre compte utilisateur</li>
            <li>L'envoi de communications relatives au service</li>
            <li>Le respect de nos obligations légales</li>
            <li>La prévention des fraudes et la sécurité</li>
        </ul>
    </section>

    <section class="legal-section">
        <h2>5. Base légale</h2>
        <p>Le traitement de vos données repose sur :</p>
        <ul>
            <li>L'exécution du contrat de service</li>
            <li>Le respect de nos obligations légales</li>
            <li>Votre consentement pour les communications marketing</li>
            <li>Nos intérêts légitimes pour l'amélioration du service</li>
        </ul>
    </section>

    <section class="legal-section">
        <h2>6. Durée de conservation</h2>
        <p>
            Vos données sont conservées pendant la durée de votre abonnement et jusqu'à 3 ans après 
            la fin de la relation commerciale, sauf obligations légales de conservation plus longues.
        </p>
    </section>

    <section class="legal-section">
        <h2>7. Vos droits</h2>
        <p>Conformément au RGPD, vous disposez des droits suivants :</p>
        <ul>
            <li><strong>Droit d'accès :</strong> obtenir une copie de vos données</li>
            <li><strong>Droit de rectification :</strong> corriger vos données inexactes</li>
            <li><strong>Droit à l'effacement :</strong> demander la suppression de vos données</li>
            <li><strong>Droit à la portabilité :</strong> récupérer vos données dans un format structuré</li>
            <li><strong>Droit d'opposition :</strong> vous opposer au traitement de vos données</li>
            <li><strong>Droit à la limitation :</strong> limiter le traitement de vos données</li>
        </ul>
        <p>
            Pour exercer ces droits, contactez-nous à : <a href="mailto:dpo@pilom.fr">dpo@pilom.fr</a>
        </p>
    </section>

    <section class="legal-section">
        <h2>8. Sécurité</h2>
        <p>
            Nous mettons en œuvre des mesures techniques et organisationnelles appropriées pour protéger 
            vos données : chiffrement, accès restreint, sauvegardes régulières, etc.
        </p>
    </section>

    <section class="legal-section">
        <h2>9. Transferts hors UE</h2>
        <p>
            Vos données sont hébergées en France. En cas de transfert hors de l'Union Européenne, 
            nous nous assurons que des garanties appropriées sont en place.
        </p>
    </section>

    <section class="legal-section">
        <h2>10. Contact et réclamation</h2>
        <p>
            Pour toute question ou réclamation relative à la protection de vos données, vous pouvez 
            contacter notre DPO ou déposer une plainte auprès de la CNIL (www.cnil.fr).
        </p>
    </section>

    <p class="legal-update">Dernière mise à jour : <?= date('d/m/Y') ?></p>
</div>

<?= $this->endSection() ?>

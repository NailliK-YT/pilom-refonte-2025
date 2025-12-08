<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Mentions Légales</h1>
    <p class="page-subtitle">Informations légales concernant le site Pilom</p>
</div>

<div class="legal-content">
    <section class="legal-section">
        <h2>1. Éditeur du site</h2>
        <p>
            <strong>Pilom SAS</strong><br>
            Société par Actions Simplifiée au capital de 10 000 €<br>
            Siège social : 123 Avenue des Entrepreneurs, 75001 Paris, France<br>
            RCS Paris B 123 456 789<br>
            N° SIRET : 123 456 789 00012<br>
            N° TVA Intracommunautaire : FR12 123456789
        </p>
        <p>
            <strong>Directeur de la publication :</strong> [Nom du directeur]<br>
            <strong>Contact :</strong> <a href="mailto:contact@pilom.fr">contact@pilom.fr</a>
        </p>
    </section>

    <section class="legal-section">
        <h2>2. Hébergement</h2>
        <p>
            Le site est hébergé par :<br>
            <strong>OVH SAS</strong><br>
            2 rue Kellermann, 59100 Roubaix, France<br>
            Tél : +33 9 72 10 10 07
        </p>
    </section>

    <section class="legal-section">
        <h2>3. Propriété intellectuelle</h2>
        <p>
            L'ensemble du contenu de ce site (textes, images, logos, graphismes, icônes, sons, logiciels, etc.) 
            est la propriété exclusive de Pilom SAS ou de ses partenaires. Toute reproduction, représentation, 
            modification, publication, adaptation de tout ou partie des éléments du site, quel que soit le moyen 
            ou le procédé utilisé, est interdite, sauf autorisation écrite préalable de Pilom SAS.
        </p>
    </section>

    <section class="legal-section">
        <h2>4. Protection des données personnelles</h2>
        <p>
            Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique 
            et Libertés, vous disposez d'un droit d'accès, de rectification, de suppression et de portabilité 
            de vos données personnelles.
        </p>
        <p>
            Pour exercer ces droits ou pour toute question relative à la protection de vos données, 
            contactez notre Délégué à la Protection des Données à : <a href="mailto:dpo@pilom.fr">dpo@pilom.fr</a>
        </p>
        <p>
            Pour plus d'informations, consultez notre <a href="<?= base_url('confidentialite') ?>">Politique de Confidentialité</a>.
        </p>
    </section>

    <section class="legal-section">
        <h2>5. Cookies</h2>
        <p>
            Le site utilise des cookies pour améliorer l'expérience utilisateur et analyser le trafic. 
            Vous pouvez paramétrer votre navigateur pour refuser les cookies ou être alerté lors de leur utilisation.
        </p>
    </section>

    <section class="legal-section">
        <h2>6. Limitation de responsabilité</h2>
        <p>
            Pilom SAS s'efforce d'assurer au mieux l'exactitude et la mise à jour des informations diffusées 
            sur ce site. Toutefois, Pilom SAS ne peut garantir l'exactitude, la précision ou l'exhaustivité 
            des informations mises à disposition sur ce site.
        </p>
    </section>

    <p class="legal-update">Dernière mise à jour : <?= date('d/m/Y') ?></p>
</div>

<?= $this->endSection() ?>

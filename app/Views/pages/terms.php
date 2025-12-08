<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Conditions Générales d'Utilisation</h1>
    <p class="page-subtitle">Conditions d'utilisation du service Pilom</p>
</div>

<div class="legal-content">
    <section class="legal-section">
        <h2>Article 1 - Objet</h2>
        <p>
            Les présentes Conditions Générales d'Utilisation (CGU) ont pour objet de définir les modalités 
            et conditions d'utilisation du service Pilom, ainsi que les droits et obligations des utilisateurs.
        </p>
        <p>
            En créant un compte sur Pilom, l'utilisateur accepte sans réserve les présentes CGU.
        </p>
    </section>

    <section class="legal-section">
        <h2>Article 2 - Description du service</h2>
        <p>
            Pilom est une plateforme de gestion en ligne permettant aux entrepreneurs et petites entreprises de :
        </p>
        <ul>
            <li>Créer et gérer des factures et devis</li>
            <li>Gérer leur fichier clients</li>
            <li>Suivre leur trésorerie et leurs dépenses</li>
            <li>Générer des rapports et statistiques</li>
        </ul>
    </section>

    <section class="legal-section">
        <h2>Article 3 - Inscription et compte utilisateur</h2>
        <p>
            L'accès au service nécessite la création d'un compte. L'utilisateur s'engage à fournir des 
            informations exactes et à les maintenir à jour. Il est responsable de la confidentialité 
            de ses identifiants de connexion.
        </p>
        <p>
            Pilom se réserve le droit de suspendre ou supprimer tout compte en cas de non-respect des présentes CGU.
        </p>
    </section>

    <section class="legal-section">
        <h2>Article 4 - Obligations de l'utilisateur</h2>
        <p>L'utilisateur s'engage à :</p>
        <ul>
            <li>Utiliser le service conformément à sa destination</li>
            <li>Ne pas porter atteinte à l'intégrité du service</li>
            <li>Respecter la législation en vigueur</li>
            <li>Ne pas diffuser de contenu illicite ou offensant</li>
        </ul>
    </section>

    <section class="legal-section">
        <h2>Article 5 - Propriété intellectuelle</h2>
        <p>
            Le service Pilom, son contenu et ses fonctionnalités sont protégés par le droit de la propriété 
            intellectuelle. L'utilisateur bénéficie d'un droit d'usage personnel et non exclusif du service.
        </p>
    </section>

    <section class="legal-section">
        <h2>Article 6 - Protection des données</h2>
        <p>
            Les données personnelles collectées sont traitées conformément à notre 
            <a href="<?= base_url('confidentialite') ?>">Politique de Confidentialité</a> et au RGPD.
        </p>
    </section>

    <section class="legal-section">
        <h2>Article 7 - Responsabilité</h2>
        <p>
            Pilom s'engage à assurer la disponibilité du service dans la mesure du possible. Toutefois, 
            Pilom ne saurait être tenu responsable des interruptions de service pour maintenance ou cas 
            de force majeure.
        </p>
    </section>

    <section class="legal-section">
        <h2>Article 8 - Modification des CGU</h2>
        <p>
            Pilom se réserve le droit de modifier les présentes CGU. Les utilisateurs seront informés 
            de toute modification substantielle par email ou notification in-app.
        </p>
    </section>

    <section class="legal-section">
        <h2>Article 9 - Droit applicable et juridiction</h2>
        <p>
            Les présentes CGU sont régies par le droit français. Tout litige relatif à leur interprétation 
            ou exécution sera soumis aux tribunaux compétents de Paris.
        </p>
    </section>

    <p class="legal-update">Dernière mise à jour : <?= date('d/m/Y') ?></p>
</div>

<?= $this->endSection() ?>

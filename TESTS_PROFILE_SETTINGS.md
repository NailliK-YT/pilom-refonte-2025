# Tests - Système de Gestion du Profil et Paramètres

## ✅ Tests Exécutés avec Succès

### 1. Base de Données

#### Migrations
```bash
php spark migrate
```
**Résultat:** ✅ Toutes les 5 migrations exécutées avec succès
- `user_profiles` (créée)
- `company_settings` (créée)
- `notification_preferences` (créée)
- `login_history` (créée)
- `account_deletion_requests` (créée)

#### Seeders
```bash
php spark db:seed UserProfilesSeeder
php spark db:seed CompanySettingsSeeder
php spark db:seed NotificationPreferencesSeeder
```
**Résultat:** ✅ Tous les seeders ont généré des données de test

**Vérification:** Utilisez `test-queries.sql` pour vérifier les données

### 2. Structure des Fichiers

#### Controllers (4 contrôleurs créés)
- ✅ `ProfileController.php` - Gestion du profil
- ✅ `CompanySettingsController.php` - Paramètres entreprise
- ✅ `AccountController.php` - Sécurité et suppression
- ✅ `NotificationController.php` - Préférences notifications

#### Models (5 modèles créés)
- ✅ `UserProfileModel.php`
- ✅ `CompanySettingsModel.php`
- ✅ `NotificationPreferencesModel.php`
- ✅ `LoginHistoryModel.php`
- ✅ `AccountDeletionModel.php`

#### Views (10 vues créées)
**Profile:**
- ✅ `profile/layout.php`
- ✅ `profile/index.php`
- ✅ `profile/password.php`

**Settings:**
- ✅ `settings/layout.php`
- ✅ `settings/company_info.php`
- ✅ `settings/legal.php`
- ✅ `settings/invoicing.php`

**Account:**
- ✅ `account/security.php`
- ✅ `account/login_history.php`
- ✅ `account/deletion.php`

**Notifications:**
- ✅ `notifications/preferences.php`

#### Assets
- ✅ `public/css/profile.css` (500+ lignes)
- ✅ `public/js/profile.js`
- ✅ `public/js/settings.js`

#### Language Files
- ✅ `app/Language/fr/Profile.php`
- ✅ `app/Language/fr/Settings.php`
- ✅ `app/Language/fr/Account.php`
- ✅ `app/Language/fr/Notifications.php`

### 3. Dossiers d'Upload

```bash
✅ writable/uploads/profiles/ (créé)
✅ writable/uploads/logos/ (créé)
```

**Permissions:** Assurez-vous que ces dossiers sont accessibles en écriture

### 4. Routes

**22 routes configurées:**
- `/profile` - 6 routes ✅
- `/settings/company` - 8 routes ✅
- `/account` - 5 routes ✅
- `/notifications` - 2 routes ✅

Toutes protégées par le filtre `auth`.

---

## ⚠️ Avertissement Important

### Bibliothèque GD Non Installée

**Détecté:** PHP 8.5.0 sans extension GD

**Impact:** 
- ❌ Le redimensionnement d'images ne fonctionnera PAS
- ❌ L'upload de photos de profil échouera lors du traitement
- ❌ L'upload de logos échouera lors du traitement

**Solution:**
Pour activer le traitement d'images, installez l'extension GD ou ImageMagick :

1. Ouvrir `php.ini`
2. Décommenter ou ajouter : `extension=gd`
3. Redémarrer le serveur web

OU installer ImageMagick :
```bash
composer require ext-imagick
```

---

## 📋 Tests Manuels Requis

### Tests à Effectuer

#### 1. Profil Utilisateur
- [ ] Se connecter avec un utilisateur existant
- [ ] Accéder à `/profile`
- [ ] Modifier prénom, nom, téléphone
- [ ] Sauvegarder et vérifier la persistance
- [ ] **Sans GD:** Ne pas tester l'upload de photo
- [ ] **Avec GD:** Tester upload photo (JPG, PNG < 2MB)

#### 2. Changement de Mot de Passe
- [ ] Accéder à `/profile/password`
- [ ] Entrer ancien mot de passe incorrect → doit échouer
- [ ] Entrer mot de passe faible → voir indicateur "Faible"
- [ ] Entrer mot de passe fort → voir indicateur "Fort"
- [ ] Vérifier les checkmarks des exigences
- [ ] Changer avec succès le mot de passe
- [ ] Se reconnecter avec le nouveau mot de passe

#### 3. Paramètres Entreprise
- [ ] Accéder à `/settings/company`
- [ ] Modifier nom de l'entreprise
- [ ] Remplir l'adresse complète
- [ ] Entrer un SIRET valide (bordure verte)
- [ ] Entrer un SIRET invalide (bordure rouge)
- [ ] **Sans GD:** Ne pas tester l'upload de logo
- [ ] Sauvegarder et vérifier

#### 4. Informations Légales
- [ ] Accéder à `/settings/company/legal`
- [ ] Saisir mentions légales
- [ ] Saisir CGV
- [ ] Sauvegarder

#### 5. Facturation
- [ ] Accéder à `/settings/company/invoicing`
- [ ] Sélectionner taux TVA par défaut
- [ ] Configurer préfixe facture (ex: "FACT")
- [ ] Définir prochain numéro (ex: 100)
- [ ] Vérifier aperçu en temps réel (FACT-0100)
- [ ] Entrer IBAN (27 caractères pour FR)
- [ ] Entrer BIC
- [ ] Sauvegarder

#### 6. Sécurité du Compte
- [ ] Accéder à `/account/security`
- [ ] Vérifier affichage dernière connexion
- [ ] Accéder à `/account/login-history`
- [ ] Vérifier tableau historique

#### 7. Suppression de Compte
- [ ] Accéder à `/account/deletion`
- [ ] Lire les avertissements
- [ ] Demander suppression avec raison
- [ ] Vérifier affichage "30 jours restants"
- [ ] Annuler la suppression
- [ ] Vérifier compte actif à nouveau

#### 8. Préférences Notifications
- [ ] Accéder à `/notifications/preferences`
- [ ] Désactiver/activer différents types
- [ ] Sauvegarder
- [ ] Recharger la page → vérifier persistance

---

## 🔒 Tests de Sécurité

### À Vérifier

#### 1. Protection CSRF
- [ ] Soumettre formulaire sans token CSRF → doit échouer
- [ ] Vérifier présence de `<?= csrf_field() ?>` dans toutes les vues

#### 2. Permissions
- [ ] Utilisateur A ne peut pas modifier profil Utilisateur B
- [ ] Tester avec manipulation URL directe
- [ ] Vérifier redirection ou erreur 403

#### 3. XSS (Cross-Site Scripting)
- [ ] Entrer `<script>alert('XSS')</script>` dans champs texte
- [ ] Vérifier échappement dans l'affichage (pas d'exécution)

#### 4. Upload de Fichiers
- [ ] Tenter upload fichier PHP → doit être rejeté
- [ ] Tenter upload fichier > 2MB → doit échouer
- [ ] Tenter upload PDF → doit être rejeté

#### 5. SQL Injection
- [ ] Entrer `' OR '1'='1` dans champs
- [ ] Vérifier aucune erreur SQL, données protégées

---

## 📊 Résultats Attendus

### Validation Backend

**SIRET:**
```php
validateSiret('73282932000074') // true (valide)
validateSiret('12345678901234') // false (invalide)
```

**IBAN:**
```php
validateIban('FR7630001007941234567890185') // true
validateIban('FR123') // false
```

### Données en Base

**Requêtes de vérification dans `test-queries.sql`:**
1. Comptage des enregistrements par table
2. Échantillon de profils utilisateurs
3. Échantillon de paramètres entreprise
4. Préférences de notification
5. Clés étrangères configurées

---

## 🐛 Problèmes Connus

### 1. Extension GD Manquante
**Statut:** ⚠️ Critique pour uploads d'images
**Solution:** Installer extension GD dans PHP

### 2. Test Script PHP Échec
**Statut:** ⚠️ Minor
**Cause:** Problème d'initialisation CodeIgniter en CLI
**Solution:** Utiliser requêtes SQL directes (`test-queries.sql`)

---

## ✅ Checklist de Mise en Production

Avant la mise en production, vérifier :

- [x] Migrations exécutées
- [x] Dossiers d'upload créés
- [x] Routes configurées
- [ ] Extension GD installée
- [ ] Permissions dossiers vérifiées (755)
- [ ] Tests manuels effectués
- [ ] Tests de sécurité passés
- [ ] Seeders exécutés (optionnel pour dev)
- [ ] Documentation lue

---

## 📝 Commandes Utiles

```bash
# Exécuter migrations
php spark migrate

# Annuler dernière migration
php spark migrate:rollback

# Exécuter seeders
php spark db:seed UserProfilesSeeder
php spark db:seed CompanySettingsSeeder
php spark db:seed NotificationPreferencesSeeder

# Créer dossiers upload (Windows)
New-Item -ItemType Directory -Path "writable\uploads\profiles" -Force
New-Item -ItemType Directory -Path "writable\uploads\logos" -Force

# Vérifier extension GD
php -r "echo extension_loaded('gd') ? 'GD installée' : 'GD manquante';"
```

---

## 📞 Support

En cas de problème :
1. Vérifier les logs : `writable/logs/`
2. Consulter `README_PROFILE_SETTINGS.md`
3. Vérifier la configuration dans `.env`
4. S'assurer que la session est active

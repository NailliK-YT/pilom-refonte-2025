# Configuration de la Base de Données - PILOM

## ✅ État Actuel

La base de données a été réinitialisée et reconfigurée avec succès le 7 décembre 2025.

### Tables Créées (25 tables)

#### 1. Gestion des Utilisateurs et Entreprises
- **users** - Utilisateurs du système
- **user_profiles** - Profils utilisateurs détaillés
- **companies** - Entreprises/organisations
- **business_sectors** - Secteurs d'activité
- **registration_sessions** - Sessions d'inscription
- **company_settings** - Paramètres des entreprises

#### 2. Gestion des Contacts et Relations
- **contact** - Contacts (clients/fournisseurs)

#### 3. Gestion Commerciale
- **devis** - Devis/Propositions commerciales
- **facture** - Factures
- **reglement** - Règlements/Paiements de factures

#### 4. Gestion des Produits
- **products** - Produits
- **categories** - Catégories de produits
- **tva_rates** - Taux de TVA
- **price_tiers** - Paliers de prix

#### 5. Gestion des Dépenses
- **depenses** - Dépenses
- **categories_depenses** - Catégories de dépenses
- **fournisseurs** - Fournisseurs
- **frequences** - Fréquences (pour dépenses récurrentes)
- **depenses_recurrences** - Configuration des dépenses récurrentes
- **historique_depenses** - Historique des modifications de dépenses

#### 6. Système et Sécurité
- **login_history** - Historique des connexions
- **notification_preferences** - Préférences de notification
- **account_deletion_requests** - Demandes de suppression de compte
- **pages** - Pages du CMS
- **migrations** - Suivi des migrations de base de données

## 🔧 Configuration

### Fichier `env`
```
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = pilom
database.default.username = anas
database.default.password = sana
database.default.DBDriver = Postgre
database.default.port = 5432
```

## 📝 Migrations

### État des Migrations
- **Total**: 27 migrations
- **Batch**: 1 (toutes exécutées en une fois)

### Liste des Migrations (dans l'ordre)
1. `2025-12-01-205246` - CreateUsersTable
2. `2025-12-01-205248` - CreateProductsTable
3. `2025-12-02-000001` - CreateBusinessSectorsTable
4. `2025-12-02-000002` - CreateCompaniesTable
5. `2025-12-02-000003` - CreateRegistrationSessionsTable
6. `2025-12-02-000004` - AddUserCompanyForeignKey
7. `2025-12-03-100000` - AddRememberTokenToUsers
8. `2025-12-04-100001` - CreateTvaRatesTable
9. `2025-12-04-100002` - CreateCategoriesTable
10. `2025-12-04-100004` - CreatePriceTiersTable
11. `2025-12-04-114243` - CreateContactTable
12. `2025-12-04-115150` - CreateDevisTable
13. `2025-12-04-144300` - FixCategoriesAndProductsTables
14. `2025-12-04-153100` - CreatePagesTable
15. `2025-12-05-100001` - CreateUserProfilesTable
16. `2025-12-05-100002` - CreateCompanySettingsTable
17. `2025-12-05-100003` - CreateNotificationPreferencesTable
18. `2025-12-05-100004` - CreateLoginHistoryTable
19. `2025-12-05-100005` - CreateAccountDeletionRequestsTable
20. `2025-12-05-100829` - CreateFactureTable
21. `2025-12-05-124448` - CreateReglementTable
22. `2025-12-05-140000` - CreateFrequencesTable
23. `2025-12-05-140001` - CreateCategoriesDepensesTable
24. `2025-12-05-140002` - CreateFournisseursTable
25. `2025-12-05-140003` - CreateDepensesTable
26. `2025-12-05-140004` - CreateDepensesRecurrencesTable
27. `2025-12-05-140005` - CreateHistoriqueDepensesTable

## 🔄 Réinitialisation de la Base de Données

### Méthode Recommandée (SQL Direct)

Pour réinitialiser complètement la base de données :

```bash
# 1. Exécuter le script SQL de reset
PGPASSWORD=sana psql -h localhost -U anas -d pilom -f reset-database.sql

# 2. Relancer les migrations
php spark migrate
```

### Fichier `reset-database.sql`
Ce script supprime toutes les tables (avec CASCADE) et recrée la table `migrations` vide.

## 🔗 Relations Clés

### Contact → Devis → Facture → Règlement
- Un **contact** peut avoir plusieurs **devis**
- Un **devis** peut être converti en **facture**
- Une **facture** peut avoir plusieurs **règlements**

### Dépenses
- Les **dépenses** sont liées à :
  - Une **company** (entreprise)
  - Un **user** (utilisateur créateur)
  - Une **categorie_depenses** (catégorie)
  - Un **fournisseur** (optionnel)
  - Un **tva_rates** (taux de TVA)
  - Une **frequence** (pour les dépenses récurrentes)

### Utilisateurs et Entreprises
- Les **users** peuvent être liés à une **company**
- Chaque **company** a un **business_sector**
- Les **users** ont des **user_profiles**, **company_settings**, etc.

## ✅ Vérifications

### Vérifier les Tables
```bash
PGPASSWORD=sana psql -h localhost -U anas -d pilom -c "\dt"
```

### Vérifier les Migrations
```bash
PGPASSWORD=sana psql -h localhost -U anas -d pilom -c "SELECT COUNT(*) as total_migrations, MAX(batch) as dernier_batch FROM migrations;"
```

### Vérifier une Table Spécifique
```bash
PGPASSWORD=sana psql -h localhost -U anas -d pilom -c "\d nom_table"
```

## 🚨 Problèmes Résolus

### Migrations en Double (RÉSOLU)
**Problème** : Plusieurs migrations vides créaient des doublons et des erreurs
- `CreateContactsTable` (vide) - **SUPPRIMÉE**
- `CreateQuotesTable` (vide) - **SUPPRIMÉE**
- `CreateInvoicesTable` (vide) - **SUPPRIMÉE**
- `CreateInvoiceItemsTable` (vide) - **SUPPRIMÉE**
- `CreatePaymentsTable` (vide) - **SUPPRIMÉE**
- `CreateQuoteItemsTable` (vide) - **SUPPRIMÉE**

**Solution** : Ces migrations ont été supprimées. Les tables nécessaires sont créées par les migrations plus récentes (`CreateContactTable`, `CreateDevisTable`, `CreateFactureTable`, `CreateReglementTable`).

### Erreur "relation contact already exists" (RÉSOLU)
**Problème** : La table `contact` existait déjà lors de l'exécution des migrations

**Solution** : 
1. Script `reset-database.sql` créé pour nettoyer complètement la base
2. Suppression des migrations en double
3. Réexécution des migrations dans le bon ordre

## 📚 Commandes Utiles

### CodeIgniter
```bash
# Exécuter les migrations
php spark migrate

# Rollback des migrations
php spark migrate:rollback

# Vérifier le statut des migrations
php spark migrate:status

# Créer une nouvelle migration
php spark make:migration NomDeLaMigration
```

### PostgreSQL
```bash
# Se connecter à la base
PGPASSWORD=sana psql -h localhost -U anas -d pilom

# Exécuter un fichier SQL
PGPASSWORD=sana psql -h localhost -U anas -d pilom -f fichier.sql

# Exécuter une commande SQL
PGPASSWORD=sana psql -h localhost -U anas -d pilom -c "SELECT * FROM users;"
```

## 🎯 Prochaines Étapes

1. ✅ Base de données configurée et fonctionnelle
2. ⏭️ Tester les fonctionnalités du site
3. ⏭️ Vérifier l'intégrité des données
4. ⏭️ Ajouter des données de test si nécessaire

## 📝 Notes Importantes

- Toutes les tables utilisent des **clés étrangères avec CASCADE** pour maintenir l'intégrité référentielle
- Les tables utilisent soit des **UUID** soit des **SERIAL/INTEGER** comme clés primaires selon leur usage
- Les **contraintes CHECK** sont utilisées pour valider les valeurs (statuts, montants, etc.)
- Les **timestamps** (created_at, updated_at, deleted_at) sont présents sur la plupart des tables
- La table **migrations** suit toutes les modifications de schéma

---
**Dernière mise à jour** : 7 décembre 2025
**Version de la base** : Batch 1, 27 migrations


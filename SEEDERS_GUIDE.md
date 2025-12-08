# 🌱 Guide d'utilisation des Seeders - Pilom

## 📝 Résumé

J'ai créé **18 seeders complets** pour initialiser toute votre base de données avec des données de démonstration réalistes.

## 🚀 Utilisation rapide

### Initialisation complète (RECOMMANDÉ)

```bash
cd /home/fletcher/Documents/Cours_Utils/SaeTC/FusionnerProjet/pilom
php spark migrate:refresh --force
php spark db:seed MasterSeeder
```

✅ **Résultat** : Base de données complète avec toutes les données de test !

---

## 📊 Liste des seeders créés

| # | Seeder | Données créées | Dépendances |
|---|--------|----------------|-------------|
| 1 | `BusinessSectorSeeder` | 10 secteurs d'activité | Aucune |
| 2 | `CompanySeeder` | 1 entreprise + 2 utilisateurs | BusinessSectorSeeder |
| 3 | `TvaRatesSeeder` | 5 taux de TVA (0%, 2.1%, 5.5%, 10%, 20%) | CompanySeeder |
| 4 | `CategoriesSeeder` | 13 catégories de produits | CompanySeeder |
| 5 | `ProductSeeder` | 10 produits variés | CategoriesSeeder, TvaRatesSeeder |
| 6 | `PriceTiersSeeder` | 15 paliers de prix (-5%, -10%, -15%) | ProductSeeder |
| 7 | `ContactSeeder` | 3 contacts (client, prospect, fournisseur) | CompanySeeder |
| 8 | `DevisSeeder` | 3 devis | ContactSeeder |
| 9 | `FactureSeeder` | 2 factures | ContactSeeder, DevisSeeder |
| 10 | `ReglementSeeder` | 2 règlements | FactureSeeder |
| 11 | `FrequenceSeeder` | 8 fréquences (journalier à annuel) | Aucune |
| 12 | `CategoryDepenseSeeder` | 12 catégories de dépenses | CompanySeeder |
| 13 | `FournisseurSeeder` | 10 fournisseurs réels | CompanySeeder |
| 14 | `DepenseSeeder` | 20 dépenses variées | Tous les précédents |
| 15 | `UserProfilesSeeder` | Profils pour chaque utilisateur | CompanySeeder |
| 16 | `CompanySettingsSeeder` | Paramètres d'entreprise | CompanySeeder |
| 17 | `NotificationPreferencesSeeder` | Préférences de notifications | CompanySeeder |
| 18 | `PagesSeeder` | 4 pages (mentions légales, etc.) | Aucune |

---

## 🔐 Identifiants de test

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| `admin@pilom.fr` | `admin123` | Administrateur |
| `test@pilom.fr` | `admin123` | Utilisateur test |

---

## 💡 Détails des données créées

### 🏢 Entreprise
- **Nom** : Pilom Tech
- **Secteur** : Informatique & Technologies
- **Utilisateurs** : 2 (admin + test)

### 💶 Taux de TVA
- Taux normal (20%)
- Taux intermédiaire (10%)
- Taux réduit (5,5%)
- Taux super réduit (2,1%)
- Exonéré (0%)

### 📦 Catégories de produits (13)
- **Électronique** → Ordinateurs, Smartphones, Accessoires
- **Mobilier de bureau** → Bureaux, Sièges
- **Fournitures de bureau** → Papeterie, Consommables
- **Services** → Formation, Conseil

### 🛍️ Produits (10)
- MacBook Pro 14" M3 (2199€)
- Dell XPS 15 (1899€)
- Écran Dell UltraSharp 27" (549€)
- Clavier mécanique Keychron K8 (89€)
- Souris Logitech MX Master 3S (99€)
- Bureau assis-debout électrique (449€)
- Chaise Herman Miller (1299€)
- Casque Sony WH-1000XM5 (349€)
- Webcam Logitech Brio 4K (199€)
- Pack papier A4 (4.99€)

### 💰 Paliers de prix (3 par produit sélectionné)
- 10+ unités = -5%
- 25+ unités = -10%
- 50+ unités = -15%

### 📇 Contacts (3)
- **Client** : Sophie Martin (client actif)
- **Prospect** : Jean Dubois (en négociation)
- **Fournisseur** : Marie Lambert (partenaire)

### 📄 Devis & Factures
- 3 devis (en attente, accepté, refusé)
- 2 factures (payée, en attente)
- 2 règlements

### 💸 Dépenses (20)
- Catégories variées (fournitures, déplacements, marketing, etc.)
- Montants de 10€ à 5000€
- Statuts : brouillon, validé, archivé
- 30% de dépenses récurrentes

### 🏭 Fournisseurs (10)
- Amazon Business
- Boulanger Pro
- Office Depot
- OVH
- Microsoft France
- Total Energies
- AXA Assurances
- Orange Business
- La Poste
- Cabinet Dubois Expertise

---

## ⚙️ Commandes disponibles

### 1. Initialisation complète
```bash
php spark migrate:refresh --force
php spark db:seed MasterSeeder
```

### 2. Seeder basique (CRM uniquement)
```bash
php spark db:seed DatabaseSeeder
```

### 3. Seeder individuel
```bash
php spark db:seed ProductSeeder
```

---

## 🔄 Réinitialisation complète

Si vous voulez repartir de zéro :

```bash
# 1. Supprimer toutes les données
php spark migrate:rollback

# 2. Recréer les tables
php spark migrate

# 3. Charger les données
php spark db:seed MasterSeeder
```

---

## 📝 Notes importantes

1. **Toujours exécuter `migrate:refresh --force` avant** le MasterSeeder
2. **Les IDs sont générés dynamiquement** (UUID) à chaque exécution
3. **Les mots de passe sont hashés** avec `password_hash()`
4. **Multi-tenant** : Toutes les données sont liées à l'entreprise créée
5. **Données réalistes** : SIRET, adresses, téléphones français réels

---

## ✅ Vérification

Après l'exécution, vous devriez voir :

```
═══════════════════════════════════════════════════════════════
  PILOM - Initialisation complète de la base de données
═══════════════════════════════════════════════════════════════

📋 Étape 1/15: Secteurs d'activité
✓ 10 secteurs d'activité créés

🏢 Étape 2/15: Entreprises et utilisateurs
✓ Entreprise de test créée : Pilom Tech
✓ Utilisateur test@pilom.fr lié à l'entreprise
✓ Utilisateur admin@pilom.fr créé et lié à l'entreprise

...

═══════════════════════════════════════════════════════════════
  ✅ Initialisation terminée avec succès !
═══════════════════════════════════════════════════════════════

📌 Identifiants de test :
   Email admin : admin@pilom.fr
   Email test  : test@pilom.fr
   Mot de passe : admin123

🌐 Accédez à l'application : http://localhost:8081
```

---

## 🎯 Prochaines étapes

1. Démarrez le serveur : `php spark serve`
2. Accédez à http://localhost:8081
3. Connectez-vous avec `admin@pilom.fr` / `admin123`
4. Explorez les données créées !

---

**Bonne utilisation ! 🚀**


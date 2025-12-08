# 🌱 Guide des Seeders - Pilom

Ce document explique comment utiliser les seeders pour initialiser votre base de données avec des données de démonstration.

## 📋 Liste des Seeders

### Seeders de base
| Seeder | Description | Dépendances |
|--------|-------------|-------------|
| **BusinessSectorSeeder** | 10 secteurs d'activité variés | Aucune |
| **TvaRatesSeeder** | 5 taux de TVA français (0%, 2.1%, 5.5%, 10%, 20%) | Aucune |
| **CompanySeeder** | Entreprise de test + 2 utilisateurs | BusinessSectorSeeder |

### Seeders produits
| Seeder | Description | Dépendances |
|--------|-------------|-------------|
| **CategoriesSeeder** | Catégories de produits avec hiérarchie | TvaRatesSeeder |
| **ProductSeeder** | 10 produits variés (informatique, mobilier) | CategoriesSeeder, TvaRatesSeeder |
| **PriceTiersSeeder** | Paliers de prix dégressifs | ProductSeeder |

### Seeders commercial (CRM)
| Seeder | Description | Dépendances |
|--------|-------------|-------------|
| **ContactSeeder** | 3 contacts (client, prospect, fournisseur) | CompanySeeder |
| **DevisSeeder** | 3 devis avec montants variés | ContactSeeder |
| **FactureSeeder** | 2 factures | ContactSeeder, DevisSeeder |
| **ReglementSeeder** | 2 règlements | FactureSeeder |

### Seeders dépenses
| Seeder | Description | Dépendances |
|--------|-------------|-------------|
| **FrequenceSeeder** | 8 fréquences (journalier à annuel) | Aucune |
| **CategoryDepenseSeeder** | 12 catégories de dépenses | CompanySeeder |
| **FournisseurSeeder** | 10 fournisseurs réels (Amazon, OVH, etc.) | CompanySeeder |
| **DepenseSeeder** | 20 dépenses variées | Tous les précédents |

### Seeders utilisateurs & paramètres
| Seeder | Description | Dépendances |
|--------|-------------|-------------|
| **UserProfilesSeeder** | Profils pour chaque utilisateur | CompanySeeder |
| **CompanySettingsSeeder** | Paramètres d'entreprise | CompanySeeder |
| **NotificationPreferencesSeeder** | Préférences de notifications | CompanySeeder |

### Autres
| Seeder | Description | Dépendances |
|--------|-------------|-------------|
| **PagesSeeder** | 4 pages (mentions légales, confidentialité, etc.) | Aucune |

---

## 🚀 Utilisation

### 1️⃣ Initialisation complète (RECOMMANDÉ)

```bash
# Réinitialise la base et charge TOUS les seeders
php spark migrate:refresh
php spark db:seed MasterSeeder
```

**Contenu :**
- ✅ 10 secteurs d'activité
- ✅ 5 taux de TVA
- ✅ 1 entreprise + 2 utilisateurs
- ✅ 13 catégories de produits
- ✅ 10 produits
- ✅ 15 paliers de prix
- ✅ 3 contacts
- ✅ 3 devis
- ✅ 2 factures
- ✅ 2 règlements
- ✅ 8 fréquences
- ✅ 12 catégories de dépenses
- ✅ 10 fournisseurs
- ✅ 20 dépenses
- ✅ Profils utilisateurs
- ✅ Paramètres d'entreprise
- ✅ Préférences de notifications
- ✅ 4 pages du site

---

### 2️⃣ Seeders individuels

```bash
# Seeder spécifique
php spark db:seed BusinessSectorSeeder
php spark db:seed ProductSeeder
php spark db:seed FournisseurSeeder
# etc...
```

---

### 3️⃣ Seeder de base (ancien)

```bash
# Charge uniquement les données commerciales basiques
php spark db:seed DatabaseSeeder
```

**Contenu :**
- 1 entreprise + 2 utilisateurs
- 3 contacts
- 3 devis
- 2 factures
- 2 règlements

---

## 🔐 Identifiants de test

Après l'exécution du MasterSeeder :

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| `admin@pilom.fr` | `admin123` | Administrateur |
| `test@pilom.fr` | *(voir CompanySeeder)* | Utilisateur |

---

## 📊 Données créées

### Secteurs d'activité
- Services aux entreprises
- Commerce & Distribution
- Restauration & Hôtellerie
- Bâtiment & Travaux Publics
- Santé & Bien-être
- Industrie & Fabrication
- Informatique & Technologies
- Transport & Logistique
- Agriculture & Agroalimentaire
- Immobilier

### Produits exemples
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

### Fournisseurs
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

## 🛠️ Ordre d'exécution

Le **MasterSeeder** respecte automatiquement l'ordre des dépendances :

```
1. BusinessSectorSeeder
2. CompanySeeder (+ users)
3. TvaRatesSeeder
4. CategoriesSeeder
5. ProductSeeder
6. PriceTiersSeeder
7. ContactSeeder
8. DevisSeeder
9. FactureSeeder
10. ReglementSeeder
11. FrequenceSeeder
12. CategoryDepenseSeeder
13. FournisseurSeeder
14. DepenseSeeder
15. UserProfilesSeeder
16. CompanySettingsSeeder
17. NotificationPreferencesSeeder
18. PagesSeeder
```

---

## ⚠️ Notes importantes

1. **Toujours exécuter `migrate:refresh` avant** pour partir d'une base vierge
2. **Ne PAS exécuter en production** - données de démonstration uniquement
3. **Les IDs sont générés dynamiquement** (UUID) à chaque exécution
4. **Les mots de passe sont hashés** avec `password_hash()`
5. **Les images des produits ne sont pas créées** (champ `image_path` à null)

---

## 🔄 Réinitialisation complète

```bash
# 1. Supprimer toutes les données et migrations
php spark migrate:rollback

# 2. Réappliquer toutes les migrations
php spark migrate

# 3. Charger les données
php spark db:seed MasterSeeder
```

---

## 📝 Personnalisation

Pour modifier les données générées, éditez les fichiers dans :
```
app/Database/Seeds/
```

Chaque seeder contient des tableaux de données facilement modifiables.

---

## 🐛 Dépannage

### Erreur : "Aucune entreprise trouvée"
```bash
# Exécutez d'abord CompanySeeder
php spark db:seed CompanySeeder
```

### Erreur : "Taux de TVA manquants"
```bash
# Exécutez d'abord TvaRatesSeeder
php spark db:seed TvaRatesSeeder
```

### Tout réinitialiser
```bash
php spark migrate:refresh
php spark db:seed MasterSeeder
```

---

## 📚 Documentation

Pour plus d'informations :
- [Documentation CodeIgniter 4 - Seeding](https://codeigniter.com/user_guide/dbmgmt/seeds.html)
- [Migrations CodeIgniter 4](https://codeigniter.com/user_guide/dbmgmt/migration.html)

---

**Bonne utilisation ! 🚀**


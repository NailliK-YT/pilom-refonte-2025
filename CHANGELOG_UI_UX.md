# Changelog - Amélioration UI/UX Pilom

## Résumé des modifications

Ce document détaille les améliorations apportées à l'interface utilisateur et à l'expérience utilisateur du site Pilom.

---

## 1. Navigation Latérale (Sidebar)

### Nouveau layout unifié
- **Fichier principal** : `app/Views/layouts/dashboard_layout.php`
- **CSS** : `public/css/dashboard.css`

### Caractéristiques :
- ✅ Barre de navigation latérale fixe sur toutes les pages connectées
- ✅ Logo et branding en haut de la sidebar
- ✅ Sections organisées par thème :
  - **Principal** : Tableau de bord
  - **Commercial** : Contacts, Devis, Factures, Règlements
  - **Catalogue** : Produits, Catégories, Taux de TVA
  - **Dépenses** : Mes dépenses, Fournisseurs
  - **Mon compte** : Profil, Notifications
  - **Paramètres** : Entreprise, Facturation, Mentions légales
  - **Sécurité** : Vue d'ensemble, Historique, Suppression compte
- ✅ Profil utilisateur en bas de la sidebar avec bouton de déconnexion
- ✅ Version mobile responsive avec menu hamburger

### Icônes SVG
Toutes les icônes sont désormais en SVG inline pour :
- Performances optimisées (pas de requêtes HTTP supplémentaires)
- Personnalisation des couleurs via CSS
- Compatibilité maximale

---

## 2. Pages Modifiées

### Pages connectées (utilisent le nouveau layout) :

| Page | Fichier | Statut |
|------|---------|--------|
| Tableau de bord | `dashboard/index.php` | ✅ |
| Produits | `products/index.php` | ✅ |
| Catégories | `categories/*.php` | ✅ |
| Contacts | `contacts/contacts.php` | ✅ Refonte complète |
| Devis | `devis/devis.php` | ✅ Refonte complète |
| Dépenses | `depenses/index.php` | ✅ |
| Profil | `profile/index.php` | ✅ Refonte avec tabs |
| Mot de passe | `profile/password.php` | ✅ |
| Notifications | `notifications/preferences.php` | ✅ |
| Paramètres entreprise | `settings/company_info.php` | ✅ |
| Facturation | `settings/invoicing.php` | ✅ |
| Mentions légales | `settings/legal.php` | ✅ |
| Sécurité | `account/security.php` | ✅ Refonte complète |
| Historique connexions | `account/login_history.php` | ✅ |
| Suppression compte | `account/deletion.php` | ✅ |

---

## 3. Charte Graphique Conservée

### Couleurs principales :
```css
--primary-color: #4e51c0      /* Violet Pilom */
--secondary-color: #1fc187    /* Vert */
--text-color: #333333         /* Texte principal */
--background-color: #f8fafc   /* Fond clair */
--dark-gray: #1e293b          /* Titres */
```

### Nouvelles variables ajoutées :
```css
--primary-light: rgba(78, 81, 192, 0.1)
--border-color: #e2e8f0
--danger: #dc2626
--warning: #f59e0b
--success: #16a34a
```

---

## 4. Typographie

- **Police principale** : Plus Jakarta Sans (Google Fonts)
- Fallback : système natif
- Hiérarchie claire des titres (h1-h6)

---

## 5. Composants UI Améliorés

### Cards
- Bordures arrondies (12px)
- Ombres subtiles
- Effet hover interactif

### Boutons
- Styles cohérents (primary, secondary, outline, danger)
- Icônes SVG intégrées
- Animations de transition

### Tables
- En-têtes stylisés
- Lignes alternées
- Effets hover

### Formulaires
- Labels clairs
- Champs avec focus visible
- Messages d'aide et validation

### Alertes
- Auto-dismiss après 5 secondes
- Icônes contextuelle (succès, erreur, warning)
- Animation d'entrée/sortie

---

## 6. Responsive Design

### Breakpoints :
- Desktop : > 1024px
- Tablette : 768px - 1024px
- Mobile : < 768px

### Comportement mobile :
- Sidebar masquée par défaut
- Bouton hamburger flottant
- Overlay pour fermer le menu
- Navigation tactile optimisée

---

## 7. Base de Données (PostgreSQL)

### Configuration :
- Fichier : `app/Config/Database.php`
- Driver : Postgre
- Variables d'environnement via `.env`

### Seeders créés/mis à jour :
- `MasterSeeder.php` - Seeder principal amélioré
- `ProductSeeder.php` - Données de produits
- `FournisseurSeeder.php` - Données de fournisseurs
- `DepenseSeeder.php` - Données de dépenses

### Commande d'initialisation :
```bash
php spark migrate
php spark db:seed MasterSeeder
```

### Compte de test :
- **Email** : test@pilom.fr
- **Password** : password

---

## 8. Fichiers Créés

| Fichier | Description |
|---------|-------------|
| `public/css/dashboard.css` | Styles du layout principal |
| `app/Database/Seeds/ProductSeeder.php` | Seeder produits |
| `app/Database/Seeds/FournisseurSeeder.php` | Seeder fournisseurs |
| `app/Database/Seeds/DepenseSeeder.php` | Seeder dépenses |

---

## 9. Instructions d'Utilisation

### Pour démarrer le projet :

1. **Configuration de la base de données** :
   ```bash
   cp env .env
   # Éditer .env avec vos paramètres PostgreSQL
   ```

2. **Installation des dépendances** :
   ```bash
   composer install
   ```

3. **Exécution des migrations** :
   ```bash
   php spark migrate
   ```

4. **Population de la base de données** :
   ```bash
   php spark db:seed MasterSeeder
   ```

5. **Lancement du serveur** :
   ```bash
   php spark serve
   ```

6. **Accès** : http://localhost:8080

---

## 10. Structure des Views

```
app/Views/
├── layouts/
│   ├── dashboard_layout.php  ← Layout principal avec sidebar
│   └── template.php          ← Layout public (landing page)
├── profile/
│   ├── index.php
│   ├── password.php
│   └── layout.php (deprecated, kept for compatibility)
├── settings/
│   ├── layout.php            ← Utilise dashboard_layout
│   ├── company_info.php
│   ├── invoicing.php
│   └── legal.php
├── account/
│   ├── security.php
│   ├── login_history.php
│   └── deletion.php
└── ...
```

---

## 11. Notes de Développement

### Convention de nommage :
- Classes CSS en kebab-case
- Variables CSS avec préfixe `--`
- Fichiers PHP en snake_case

### Bonnes pratiques appliquées :
- Séparation des concerns (HTML, CSS, JS)
- CSS variables pour la maintenance
- Accessibilité (ARIA labels, contraste)
- Performance (SVG inline, CSS minimal)

---

*Documentation générée le 7 décembre 2025*


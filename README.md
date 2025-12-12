# 🚀 PILOM - Plateforme de Gestion d'Entreprise

Application web de gestion d'entreprise développée avec **CodeIgniter 4** et **PostgreSQL**.

---

## 📋 Prérequis

Avant de commencer, assurez-vous d'avoir installé :

| Logiciel | Version minimale | Téléchargement |
|----------|------------------|----------------|
| **PHP** | 8.1+ | [php.net](https://www.php.net/downloads) |
| **Composer** | 2.x | [getcomposer.org](https://getcomposer.org/download/) |
| **PostgreSQL** | 13+ | [postgresql.org](https://www.postgresql.org/download/) |
| **Node.js** *(optionnel)* | 18+ | [nodejs.org](https://nodejs.org/) |

### Extensions PHP requises
- `pgsql` et `pdo_pgsql` (PostgreSQL)
- `intl` (internationalisation)
- `mbstring` (chaînes de caractères)
- `gd` ou `imagick` (images)
- `curl` (requêtes HTTP)

---

## ⚡ Installation rapide

### 1. Cloner le projet
```bash
git clone <url-du-repo>
cd pilom-refonte-2025
```

### 2. Installer les dépendances PHP
```bash
composer install
```

### 3. Configurer l'environnement
```bash
# Copier le fichier de configuration
copy env .env          # Windows
cp env .env            # Linux/Mac

# Modifier le fichier .env avec vos informations de base de données :
# database.default.hostname = localhost
# database.default.database = pilom
# database.default.username = votre_utilisateur
# database.default.password = votre_mot_de_passe
```

### 4. Créer la base de données PostgreSQL
```sql
-- Connectez-vous à PostgreSQL (psql -U postgres)
CREATE DATABASE pilom;
CREATE USER votre_utilisateur WITH PASSWORD 'votre_mot_de_passe';
GRANT ALL PRIVILEGES ON DATABASE pilom TO votre_utilisateur;

-- Ensuite, connectez-vous à la base pilom :
\c pilom

-- Créer l'extension UUID (requise)
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
```

### 5. Exécuter les migrations
```bash
php spark migrate
```

### 6. Peupler la base de données
```bash
php spark db:seed CompleteDatabaseSeeder
```

### 7. Créer le dossier de cache (si nécessaire)
```bash
mkdir writable/cache    # Linux/Mac
mkdir writable\cache    # Windows
```

### 8. Lancer le serveur de développement
```bash
php spark serve --host=localhost --port=8080
```

🎉 **L'application est maintenant accessible sur : http://localhost:8080**

---

## 🔐 Comptes par défaut

Après avoir exécuté le seeder, ces comptes sont disponibles :

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| **Admin** | `admin@pilom.fr` | `admin123` |
| **Utilisateur** | `test@pilom.fr` | `admin123` |

> ⚠️ **Important** : Changez ces mots de passe en production !

---

## 📁 Structure du projet

```
pilom-refonte-2025/
├── app/
│   ├── Config/          # Configuration de l'application
│   ├── Controllers/     # Contrôleurs
│   ├── Database/
│   │   ├── Migrations/  # Migrations de base de données
│   │   └── Seeds/       # Données initiales
│   ├── Helpers/         # Fonctions utilitaires
│   ├── Models/          # Modèles de données
│   └── Views/           # Templates HTML
├── public/              # Fichiers publics (CSS, JS, images)
├── writable/            # Fichiers générés (logs, cache, uploads)
├── vendor/              # Dépendances PHP (géré par Composer)
├── .env                 # Configuration locale (à créer)
├── composer.json        # Dépendances PHP
└── spark                # CLI CodeIgniter
```

---

## 🛠️ Commandes utiles

### Serveur de développement
```bash
php spark serve --host=localhost --port=8080
```

### Base de données
```bash
# Voir l'état des migrations
php spark migrate:status

# Exécuter les migrations
php spark migrate

# Annuler la dernière migration
php spark migrate:rollback

# Peupler la base de données
php spark db:seed CompleteDatabaseSeeder

# Avec des identifiants personnalisés
php spark db:seed CompleteDatabaseSeeder admin@exemple.fr monMotDePasse
```

### Développement
```bash
# Créer un nouveau contrôleur
php spark make:controller NomControleur

# Créer un nouveau modèle
php spark make:model NomModele

# Créer une nouvelle migration
php spark make:migration NomMigration

# Lister toutes les routes
php spark routes

# Vider le cache
php spark cache:clear
```

---

## 🌐 URLs principales

| Page | URL |
|------|-----|
| Accueil | http://localhost:8080/ |
| Connexion | http://localhost:8080/login |
| Inscription | http://localhost:8080/register |
| Tableau de bord | http://localhost:8080/dashboard |
| Contacts | http://localhost:8080/contacts |
| Devis | http://localhost:8080/devis |
| Factures | http://localhost:8080/factures |
| Produits | http://localhost:8080/products |
| Dépenses | http://localhost:8080/depenses |

---

## ⚠️ Dépannage

### Le serveur ne démarre pas
```bash
# Vérifier si le port est déjà utilisé
netstat -ano | findstr :8080    # Windows
lsof -i :8080                   # Linux/Mac

# Utiliser un autre port
php spark serve --port=8081
```

### Erreur de connexion à la base de données
1. Vérifiez que PostgreSQL est lancé
2. Vérifiez les informations dans le fichier `.env`
3. Vérifiez que l'extension `uuid-ossp` est créée dans la base

### Erreur de cache
```bash
# Créer/recréer le dossier cache
mkdir -p writable/cache        # Linux/Mac
mkdir writable\cache           # Windows

# Donner les permissions
chmod -R 777 writable/         # Linux/Mac
```

### Page blanche ou erreur 500
```bash
# Vérifier les logs
cat writable/logs/log-*.log    # Linux/Mac
type writable\logs\log-*.log   # Windows

# Activer le mode debug dans .env
CI_ENVIRONMENT = development
```

---

## 🔧 Configuration pour la production

1. **Modifier `.env`** :
   ```
   CI_ENVIRONMENT = production
   app.baseURL = 'https://votre-domaine.com/'
   ```

2. **Sécuriser les fichiers** :
   - Ne jamais exposer le fichier `.env`
   - Configurer HTTPS obligatoire
   - Changer les mots de passe par défaut

3. **Performance** :
   - Activer le cache OPcache
   - Configurer un reverse proxy (Nginx/Apache)

---

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 👥 Support

Pour toute question ou problème :
- Créez une issue sur le dépôt Git
- Consultez les logs dans `writable/logs/`
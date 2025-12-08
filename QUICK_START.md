# 🚀 Guide de Démarrage Rapide - PILOM

## ⚡ Démarrage en 3 Étapes

### 1. Vérifier la Connexion à la Base de Données
```bash
php test-db-connection.php
```
✅ Devrait afficher "Connexion réussie"

### 2. Lancer le Serveur
```bash
php spark serve --host=localhost --port=8080
```
✅ Serveur accessible sur http://localhost:8080

### 3. Se Connecter
- URL : http://localhost:8080/login
- Email : `test@pilom.fr`
- Mot de passe : `password`

---

## 🔧 Commandes de Maintenance

### Base de Données

#### Réinitialiser Complètement (en cas de problème)
```bash
# Étape 1 : Nettoyer
PGPASSWORD=sana psql -h localhost -U anas -d pilom -f reset-database.sql

# Étape 2 : Recréer les tables
php spark migrate

# Étape 3 : Insérer les données de test
php spark db:seed MasterSeeder
```

#### Vérifications Rapides
```bash
# Lister les tables
PGPASSWORD=sana psql -h localhost -U anas -d pilom -c "\dt"

# Vérifier les migrations
php spark migrate:status

# Compter les utilisateurs
PGPASSWORD=sana psql -h localhost -U anas -d pilom -c "SELECT COUNT(*) FROM users;"
```

### CodeIgniter

```bash
# Créer une nouvelle migration
php spark make:migration NomDeLaMigration

# Créer un nouveau seeder
php spark make:seeder NomDuSeeder

# Créer un nouveau modèle
php spark make:model NomDuModele

# Créer un nouveau contrôleur
php spark make:controller NomDuControleur

# Voir toutes les routes
php spark routes
```

---

## 📁 Structure des Dossiers

```
pilom/
├── app/
│   ├── Config/          # Configuration
│   ├── Controllers/     # Contrôleurs
│   ├── Models/          # Modèles
│   ├── Views/           # Vues
│   └── Database/
│       ├── Migrations/  # Migrations DB (27 fichiers)
│       └── Seeds/       # Seeders (17 fichiers)
├── public/
│   ├── css/            # Styles
│   ├── js/             # JavaScript
│   └── index.php       # Point d'entrée
├── writable/
│   └── logs/           # Logs de l'application
├── env                 # Configuration environnement
├── reset-database.sql  # Script de reset DB
├── DATABASE_SETUP.md   # Documentation complète
└── DATABASE_STATUS.md  # État actuel de la DB
```

---

## 🗄️ Informations Base de Données

### Connexion PostgreSQL
```
Host:     localhost
Port:     5432
Database: pilom
User:     anas
Password: sana
```

### Accès Direct
```bash
# Se connecter à psql
PGPASSWORD=sana psql -h localhost -U anas -d pilom

# Ou avec prompt interactif
psql -h localhost -U anas -d pilom
# Mot de passe: sana
```

---

## 🌐 URLs du Site

### Pages Publiques
- Accueil : http://localhost:8080/
- Connexion : http://localhost:8080/login
- Inscription : http://localhost:8080/register

### Dashboard (après connexion)
- Tableau de bord : http://localhost:8080/dashboard
- Contacts : http://localhost:8080/contacts
- Devis : http://localhost:8080/devis
- Factures : http://localhost:8080/factures
- Produits : http://localhost:8080/products
- Dépenses : http://localhost:8080/depenses

---

## 🔐 Comptes de Test

### Utilisateur Standard
- **Email** : `test@pilom.fr`
- **Mot de passe** : `password`
- **Rôle** : user

---

## ⚠️ Résolution de Problèmes

### Le serveur ne démarre pas
```bash
# Vérifier si le port 8080 est libre
lsof -i :8080

# Utiliser un autre port
php spark serve --host=localhost --port=8081
```

### Erreur de connexion à la base de données
```bash
# Vérifier que PostgreSQL est lancé
sudo systemctl status postgresql

# Tester la connexion
php test-db-connection.php
```

### Erreur de migration
```bash
# Réinitialiser complètement la base
PGPASSWORD=sana psql -h localhost -U anas -d pilom -f reset-database.sql
php spark migrate
php spark db:seed MasterSeeder
```

### Page blanche ou erreur 500
```bash
# Vérifier les logs
tail -f writable/logs/log-*.log

# Vérifier les permissions
chmod -R 777 writable/
```

---

## 📚 Documentation Complète

- **`DATABASE_SETUP.md`** : Configuration détaillée de la base de données
- **`DATABASE_STATUS.md`** : État actuel et problèmes résolus
- **`README.md`** : Documentation générale du projet
- **`README_PROFILE_SETTINGS.md`** : Gestion des profils utilisateurs

---

## 🎯 Prochaines Étapes

1. ✅ Base de données opérationnelle
2. ✅ Données de test insérées
3. ✅ Serveur web fonctionnel
4. ⏭️ Tester les fonctionnalités principales
5. ⏭️ Ajouter des données personnalisées
6. ⏭️ Configurer pour la production

---

## 💡 Conseils

- **Développement** : Utilisez `CI_ENVIRONMENT = development` dans le fichier `env`
- **Logs** : Consultez `writable/logs/` en cas d'erreur
- **Debug** : La barre de débogage s'affiche en mode development
- **Backup** : Sauvegardez régulièrement avec `pg_dump`

### Backup de la Base de Données
```bash
# Créer un backup
PGPASSWORD=sana pg_dump -h localhost -U anas pilom > backup_$(date +%Y%m%d).sql

# Restaurer un backup
PGPASSWORD=sana psql -h localhost -U anas pilom < backup_20251207.sql
```

---

**Date de création** : 7 décembre 2025  
**Version** : 1.0  
**Statut** : ✅ Opérationnel


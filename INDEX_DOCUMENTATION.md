# 📚 Index de la Documentation - PILOM

**Date de mise à jour** : 7 décembre 2025  
**Version** : 1.0

---

## 🎯 Pour Commencer Rapidement

### Vous voulez juste démarrer le projet ?
👉 **`QUICK_START.md`** - Guide de démarrage en 3 étapes

### Vous avez un problème avec la base de données ?
👉 **`RESOLUTION_PROBLEMES.md`** - Solutions aux problèmes courants

---

## 📖 Documentation Complète

### 1. 🚀 **QUICK_START.md**
**Pour : Démarrage rapide**

**Contient :**
- ⚡ Démarrage en 3 étapes
- 🔧 Commandes de maintenance
- 📁 Structure des dossiers
- 🗄️ Informations base de données
- 🌐 URLs du site
- 🔐 Comptes de test
- ⚠️ Résolution de problèmes
- 💡 Conseils et astuces

**Idéal pour :** Commencer à utiliser le projet rapidement

---

### 2. 🔧 **RESOLUTION_PROBLEMES.md**
**Pour : Comprendre ce qui a été résolu**

**Contient :**
- 📋 Résumé de l'intervention
- ✅ Solutions appliquées
- 📊 État final de la base
- 🔄 Procédure de réinitialisation
- 💡 Conseils pour éviter les problèmes futurs

**Idéal pour :** Comprendre l'historique et les corrections appliquées

---

### 3. 🗄️ **DATABASE_SETUP.md**
**Pour : Documentation technique de la base de données**

**Contient :**
- ✅ État actuel de la base
- 📊 Liste complète des 25 tables
- 🔗 Relations entre tables
- 🔄 Détail des 27 migrations
- 🔧 Configuration PostgreSQL
- ✅ Vérifications disponibles
- 🚨 Problèmes résolus (détaillés)

**Idéal pour :** Comprendre la structure complète de la base

---

### 4. 📈 **DATABASE_STATUS.md**
**Pour : État actuel et statistiques**

**Contient :**
- 🎉 Résumé de l'état
- 📊 Statistiques détaillées
- 🗄️ Architecture de la base
- 🔐 Comptes de test
- 🚀 Commandes utiles
- ✅ Fonctionnalités opérationnelles
- 🧪 Tests effectués
- 📝 Notes techniques

**Idéal pour :** Avoir une vue d'ensemble de l'état actuel

---

### 5. 📋 **INDEX_DOCUMENTATION.md** (ce fichier)
**Pour : Navigation dans la documentation**

**Contient :**
- 📚 Index de tous les documents
- 🎯 Guide selon vos besoins
- 🔍 Où trouver quelle information

---

## 🔍 Trouver une Information Spécifique

### Je veux...

#### ...démarrer le serveur web
👉 `QUICK_START.md` → Section "Démarrage en 3 étapes"
```bash
php spark serve --host=localhost --port=8080
```

#### ...me connecter au site
👉 `QUICK_START.md` → Section "Comptes de test"
- Email : `test@pilom.fr`
- Mot de passe : `password`

#### ...réinitialiser la base de données
👉 `QUICK_START.md` → Section "Réinitialiser Complètement"
```bash
PGPASSWORD=sana psql -h localhost -U anas -d pilom -f reset-database.sql
php spark migrate
php spark db:seed MasterSeeder
```

#### ...comprendre la structure des tables
👉 `DATABASE_SETUP.md` → Section "Tables Créées"

#### ...voir les relations entre tables
👉 `DATABASE_STATUS.md` → Section "Architecture de la Base de Données"

#### ...connaître les URLs disponibles
👉 `QUICK_START.md` → Section "URLs du Site"

#### ...comprendre ce qui a été corrigé
👉 `RESOLUTION_PROBLEMES.md` → Tout le document

#### ...créer une nouvelle migration
👉 `QUICK_START.md` → Section "CodeIgniter"
```bash
php spark make:migration NomDeLaMigration
```

#### ...faire un backup de la base
👉 `QUICK_START.md` ou `RESOLUTION_PROBLEMES.md` → Section "Backup"
```bash
PGPASSWORD=sana pg_dump -h localhost -U anas pilom > backup_$(date +%Y%m%d).sql
```

#### ...accéder directement à PostgreSQL
👉 `QUICK_START.md` → Section "Accès Direct"
```bash
PGPASSWORD=sana psql -h localhost -U anas -d pilom
```

---

## 📂 Fichiers Additionnels

### Scripts
- **`reset-database.sql`** - Script SQL pour réinitialiser la base
- **`test-db-connection.php`** - Tester la connexion à PostgreSQL
- **`cleanup_tables.php`** - Ancien script de nettoyage (obsolète, utiliser `reset-database.sql`)

### Configuration
- **`env`** - Configuration de l'environnement et base de données
- **`composer.json`** - Dépendances PHP
- **`phpunit.xml.dist`** - Configuration des tests

### Documentation Existante
- **`README.md`** - Documentation générale du projet
- **`README_PROFILE_SETTINGS.md`** - Gestion des profils utilisateurs
- **`CHANGELOG_UI_UX.md`** - Historique des modifications UI/UX
- **`TESTS_PROFILE_SETTINGS.md`** - Tests des paramètres de profil
- **`LICENSE`** - Licence du projet

---

## 🎓 Parcours d'Apprentissage Recommandé

### Pour un Nouveau Développeur

1. **Commencer ici** 📍
   - Lire `INDEX_DOCUMENTATION.md` (ce fichier)

2. **Démarrer le projet** 🚀
   - Suivre `QUICK_START.md`
   - Lancer le serveur
   - Se connecter au site

3. **Explorer la base de données** 🗄️
   - Lire `DATABASE_STATUS.md` pour comprendre la structure
   - Consulter `DATABASE_SETUP.md` pour les détails techniques

4. **Comprendre l'historique** 📖
   - Lire `RESOLUTION_PROBLEMES.md` pour connaître les corrections

5. **Développer** 💻
   - Utiliser `QUICK_START.md` comme référence
   - Consulter `README.md` pour le contexte général

### Pour un Administrateur Système

1. **Configuration** ⚙️
   - Lire `DATABASE_SETUP.md` → Section "Configuration"
   - Vérifier le fichier `env`

2. **Maintenance** 🔧
   - Utiliser `reset-database.sql` pour réinitialisation
   - Consulter `QUICK_START.md` → Section "Commandes de maintenance"

3. **Monitoring** 📊
   - `DATABASE_STATUS.md` pour l'état actuel
   - Logs dans `writable/logs/`

### Pour un Auditeur/Testeur

1. **Fonctionnalités** ✅
   - `DATABASE_STATUS.md` → Section "Fonctionnalités Opérationnelles"
   - `DATABASE_STATUS.md` → Section "Tests Effectués"

2. **Structure** 🏗️
   - `DATABASE_SETUP.md` → Architecture complète
   - `DATABASE_STATUS.md` → Relations et contraintes

3. **Historique** 📝
   - `RESOLUTION_PROBLEMES.md` → Problèmes et solutions

---

## 🆘 Aide Rapide

### Le serveur ne démarre pas
```bash
# Vérifier le port
lsof -i :8080

# Utiliser un autre port
php spark serve --port=8081
```

### Erreur de base de données
```bash
# Tester la connexion
php test-db-connection.php

# Réinitialiser si nécessaire
PGPASSWORD=sana psql -h localhost -U anas -d pilom -f reset-database.sql
php spark migrate
```

### Page blanche
```bash
# Vérifier les logs
tail -f writable/logs/log-*.log

# Vérifier les permissions
chmod -R 777 writable/
```

---

## 📞 Contacts et Support

### Documentation Officielle
- **CodeIgniter 4** : https://codeigniter.com/user_guide/
- **PostgreSQL** : https://www.postgresql.org/docs/

### Fichiers de Log
- **Application** : `writable/logs/log-*.log`
- **Erreurs PHP** : Vérifier la configuration PHP

### Structure du Projet
```
pilom/
├── app/                    # Code de l'application
│   ├── Controllers/        # Contrôleurs
│   ├── Models/             # Modèles
│   ├── Views/              # Vues
│   └── Database/
│       ├── Migrations/     # 27 migrations
│       └── Seeds/          # 17 seeders
├── public/                 # Fichiers publics (CSS, JS, images)
├── writable/               # Fichiers générés (logs, cache)
├── env                     # Configuration
└── Documentation (4 fichiers + ce fichier)
```

---

## 🎯 Checklist de Vérification

Avant de commencer à travailler, assurez-vous que :

- [ ] PostgreSQL est démarré
- [ ] Le fichier `env` est configuré correctement
- [ ] Les 25 tables sont créées (vérifier avec `\dt`)
- [ ] Les 27 migrations sont appliquées
- [ ] Le compte de test existe (`test@pilom.fr`)
- [ ] Le serveur web démarre sans erreur
- [ ] La page d'accueil se charge (HTTP 200)

### Commande de Vérification Rapide
```bash
cd /home/fletcher/Documents/Cours_Utils/SaeTC/FusionnerProjet/pilom
php test-db-connection.php && \
PGPASSWORD=sana psql -h localhost -U anas -d pilom -c "SELECT COUNT(*) as tables FROM information_schema.tables WHERE table_schema='public';" && \
php spark serve --host=localhost --port=8080
```

---

## 📌 Marque-pages Recommandés

Gardez ces pages sous la main :

1. **Pour le développement quotidien** : `QUICK_START.md`
2. **Pour les problèmes** : `RESOLUTION_PROBLEMES.md`
3. **Pour comprendre la DB** : `DATABASE_STATUS.md`
4. **Pour la navigation** : `INDEX_DOCUMENTATION.md` (ce fichier)

---

**Bonne utilisation de PILOM ! 🎉**

*Documentation maintenue par l'équipe de développement*  
*Dernière mise à jour : 7 décembre 2025*


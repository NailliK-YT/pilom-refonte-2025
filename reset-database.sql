-- Script SQL pour réinitialiser complètement la base de données
-- Ce script supprime toutes les tables et la table migrations

\echo '===================================='
\echo 'RÉINITIALISATION DE LA BASE DE DONNÉES'
\echo '===================================='
\echo ''

\echo 'Étape 1: Suppression de toutes les tables...'
\echo '-------------------------------------------'

-- Supprimer toutes les tables avec CASCADE
DROP TABLE IF EXISTS reglement CASCADE;
DROP TABLE IF EXISTS facture CASCADE;
DROP TABLE IF EXISTS devis CASCADE;
DROP TABLE IF EXISTS historique_depenses CASCADE;
DROP TABLE IF EXISTS depenses_recurrences CASCADE;
DROP TABLE IF EXISTS depenses CASCADE;
DROP TABLE IF EXISTS invoice_items CASCADE;
DROP TABLE IF EXISTS quote_items CASCADE;
DROP TABLE IF EXISTS payments CASCADE;
DROP TABLE IF EXISTS invoices CASCADE;
DROP TABLE IF EXISTS quotes CASCADE;
DROP TABLE IF EXISTS products CASCADE;
DROP TABLE IF EXISTS price_tiers CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS fournisseurs CASCADE;
DROP TABLE IF EXISTS categories_depenses CASCADE;
DROP TABLE IF EXISTS frequences CASCADE;
DROP TABLE IF EXISTS tva_rates CASCADE;
DROP TABLE IF EXISTS contact CASCADE;
DROP TABLE IF EXISTS pages CASCADE;
DROP TABLE IF EXISTS account_deletion_requests CASCADE;
DROP TABLE IF EXISTS login_history CASCADE;
DROP TABLE IF EXISTS notification_preferences CASCADE;
DROP TABLE IF EXISTS company_settings CASCADE;
DROP TABLE IF EXISTS user_profiles CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS registration_sessions CASCADE;
DROP TABLE IF EXISTS companies CASCADE;
DROP TABLE IF EXISTS business_sectors CASCADE;

DROP TABLE IF EXISTS treasury_entries CASCADE;
DROP TABLE IF EXISTS treasury_alerts CASCADE;
DROP TABLE IF EXISTS notifications CASCADE;
DROP TABLE IF EXISTS recurring_invoices CASCADE;
DROP TABLE IF EXISTS roles CASCADE;
DROP TABLE IF EXISTS permissions CASCADE;
DROP TABLE IF EXISTS role_permissions CASCADE;
DROP TABLE IF EXISTS contact_notes CASCADE;
DROP TABLE IF EXISTS contact_attachments CASCADE;
DROP TABLE IF EXISTS folders CASCADE;
DROP TABLE IF EXISTS documents CASCADE;

DROP TABLE IF EXISTS migrations CASCADE;

\echo ''
\echo 'Étape 2: Recréation de la table migrations...'
\echo '-------------------------------------------'

-- Recréer la table migrations
CREATE TABLE migrations (
    id BIGSERIAL PRIMARY KEY,
    version VARCHAR(255) NOT NULL,
    class VARCHAR(255) NOT NULL,
    "group" VARCHAR(255) NOT NULL,
    namespace VARCHAR(255) NOT NULL,
    time INTEGER NOT NULL,
    batch INTEGER NOT NULL
);

\echo ''
\echo '===================================='
\echo 'Base de données nettoyée avec succès!'
\echo '===================================='
\echo ''
\echo 'Prochaine étape: Exécutez "php spark migrate" pour recréer toutes les tables'
\echo ''


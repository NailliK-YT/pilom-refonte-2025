<?php

/**
 * Script automatique de configuration PostgreSQL pour développement
 */

echo "=== Configuration automatique PostgreSQL ===\n\n";

// Configuration pour environnement de développement
$configs = [
    [
        'host' => 'localhost',
        'port' => '5432',
        'dbname' => 'postgres', // Base par défaut
        'user' => 'postgres',
        'password' => '',
        'label' => 'Sans mot de passe (trust)'
    ],
    [
        'host' => 'localhost',
        'port' => '5432',
        'dbname' => 'postgres',
        'user' => 'postgres',
        'password' => 'postgres',
        'label' => 'Mot de passe: postgres'
    ],
    [
        'host' => 'localhost',
        'port' => '5432',
        'dbname' => 'postgres',
        'user' => 'postgres',
        'password' => 'admin',
        'label' => 'Mot de passe: admin'
    ],
    [
        'host' => 'localhost',
        'port' => '5432',
        'dbname' => 'postgres',
        'user' => 'postgres',
        'password' => 'root',
        'label' => 'Mot de passe: root'
    ],
];

$successConfig = null;

echo "🔍 Recherche de la configuration PostgreSQL...\n\n";

foreach ($configs as $config) {
    echo "Test: " . $config['label'] . "... ";

    $connString = "host={$config['host']} port={$config['port']} dbname={$config['dbname']} user={$config['user']}";
    if (!empty($config['password'])) {
        $connString .= " password={$config['password']}";
    }

    $conn = @pg_connect($connString);

    if ($conn) {
        echo "✅ SUCCÈS!\n";
        $successConfig = $config;

        // Vérifier la version
        $result = pg_query($conn, "SELECT version()");
        if ($result) {
            $version = pg_fetch_result($result, 0, 0);
            echo "   Version: " . substr($version, 0, 50) . "...\n";
        }

        pg_close($conn);
        break;
    } else {
        echo "❌\n";
    }
}

if (!$successConfig) {
    echo "\n❌ Aucune configuration n'a fonctionné.\n\n";
    echo "Solutions:\n";
    echo "1. Vérifiez que PostgreSQL est démarré\n";
    echo "2. Essayez de vous connecter avec pgAdmin pour trouver le bon mot de passe\n";
    echo "3. Réinitialisez le mot de passe PostgreSQL:\n";
    echo "   - Ouvrez pgAdmin\n";
    echo "   - Faites un clic droit sur 'PostgreSQL 18'\n";
    echo "   - Propriétés → Définir un nouveau mot de passe\n\n";
    exit(1);
}

echo "\n✅ Configuration PostgreSQL trouvée!\n\n";

// Maintenant, créer la base de données pilom si elle n'existe pas
echo "📊 Création de la base de données 'pilom'...\n";

$connString = "host={$successConfig['host']} port={$successConfig['port']} dbname={$successConfig['dbname']} user={$successConfig['user']}";
if (!empty($successConfig['password'])) {
    $connString .= " password={$successConfig['password']}";
}

$conn = pg_connect($connString);

if (!$conn) {
    echo "❌ Impossible de se reconnecter\n";
    exit(1);
}

// Vérifier si la base pilom existe
$result = pg_query($conn, "SELECT 1 FROM pg_database WHERE datname = 'pilom'");
$exists = pg_num_rows($result) > 0;

if ($exists) {
    echo "   ℹ️  La base de données 'pilom' existe déjà\n";
} else {
    echo "   Création de la base 'pilom'... ";
    $result = @pg_query($conn, "CREATE DATABASE pilom ENCODING 'UTF8'");
    if ($result) {
        echo "✅ Créée!\n";
    } else {
        echo "❌ Erreur: " . pg_last_error($conn) . "\n";
        pg_close($conn);
        exit(1);
    }
}

pg_close($conn);

// Maintenant se connecter à la base pilom et installer l'extension UUID
echo "\n🔧 Installation de l'extension UUID...\n";

$connString = "host={$successConfig['host']} port={$successConfig['port']} dbname=pilom user={$successConfig['user']}";
if (!empty($successConfig['password'])) {
    $connString .= " password={$successConfig['password']}";
}

$conn = pg_connect($connString);

if ($conn) {
    $result = @pg_query($conn, 'CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');
    if ($result) {
        echo "   ✅ Extension UUID-OSSP installée\n";
    } else {
        echo "   ⚠️  Impossible d'installer l'extension (peut-être déjà installée)\n";
    }
    pg_close($conn);
}

// Écrire la configuration dans le fichier .env
echo "\n📝 Mise à jour du fichier .env...\n";

$envFile = __DIR__ . '/.env';
$envContent = file_get_contents($envFile);

// Ajouter ou mettre à jour les lignes de configuration
$dbConfig = [
    'database.default.hostname' => $successConfig['host'],
    'database.default.database' => 'pilom',
    'database.default.username' => $successConfig['user'],
    'database.default.password' => $successConfig['password'],
    'database.default.DBDriver' => 'Postgre',
    'database.default.port' => $successConfig['port'],
];

foreach ($dbConfig as $key => $value) {
    $pattern = "/^" . preg_quote($key, '/') . "\s*=.*/m";
    $replacement = "$key = $value";

    if (preg_match($pattern, $envContent)) {
        // Remplacer la ligne existante
        $envContent = preg_replace($pattern, $replacement, $envContent);
    } else {
        // Ajouter la ligne
        $envContent .= "\n$replacement";
    }
}

file_put_contents($envFile, $envContent);

echo "   ✅ Fichier .env mis à jour\n";

// Afficher un résumé
echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ CONFIGURATION COMPLÈTE!\n";
echo str_repeat("=", 60) . "\n\n";

echo "Configuration PostgreSQL:\n";
echo "  Host:     {$successConfig['host']}\n";
echo "  Port:     {$successConfig['port']}\n";
echo "  Database: pilom\n";
echo "  Username: {$successConfig['user']}\n";
echo "  Password: " . (empty($successConfig['password']) ? '[AUCUN]' : $successConfig['password']) . "\n\n";

echo "Prochaines étapes:\n";
echo "  1. php spark migrate\n";
echo "  2. php spark db:seed BusinessSectorSeeder\n";
echo "  3. php spark serve\n";
echo "  4. Accédez à http://localhost:8080/register\n\n";

echo "🚀 Tout est prêt pour lancer les migrations!\n";

<?php

/**
 * Simple database connection test without dependencies
 */

echo "=== Test de connexion PostgreSQL ===\n\n";

// Read .env file manually
$envFile = __DIR__ . '/.env';
$env = [];

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0)
            continue;

        // Parse key = value
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $env[$key] = $value;
        }
    }
}

$host = $env['database.default.hostname'] ?? 'localhost';
$port = $env['database.default.port'] ?? '5432';
$dbname = $env['database.default.database'] ?? '';
$user = $env['database.default.username'] ?? '';
$password = $env['database.default.password'] ?? '';
$driver = $env['database.default.DBDriver'] ?? '';

echo "Configuration détectée dans .env:\n";
echo "- Host: " . ($host ?: '[NON DÉFINI]') . "\n";
echo "- Port: " . ($port ?: '[NON DÉFINI]') . "\n";
echo "- Database: " . ($dbname ?: '[NON DÉFINI]') . "\n";
echo "- Username: " . ($user ?: '[NON DÉFINI]') . "\n";
echo "- Password: " . (empty($password) ? '[NON DÉFINI]' : '[DÉFINI - ' . strlen($password) . ' caractères]') . "\n";
echo "- Driver: " . ($driver ?: '[NON DÉFINI]') . "\n\n";

if (empty($dbname) || empty($user)) {
    echo "❌ ERREUR: Les variables de base de données ne sont pas correctement définies dans .env\n\n";
    echo "Veuillez ajouter ces lignes dans votre fichier .env:\n\n";
    echo "database.default.hostname = localhost\n";
    echo "database.default.database = pilom\n";
    echo "database.default.username = postgres\n";
    echo "database.default.password = votre_mot_de_passe\n";
    echo "database.default.DBDriver = Postgre\n";
    echo "database.default.port = 5432\n";
    exit(1);
}

if (empty($password)) {
    echo "❌ ERREUR: Le mot de passe n'est pas défini dans .env!\n\n";
    echo "Ajoutez cette ligne dans votre fichier .env:\n";
    echo "database.default.password = votre_mot_de_passe_postgres\n\n";
    exit(1);
}

if ($user === 'postgre') {
    echo "⚠️  ATTENTION: Le nom d'utilisateur est 'postgre' mais devrait probablement être 'postgres'\n";
    echo "   Si la connexion échoue, changez dans .env:\n";
    echo "   database.default.username = postgres\n\n";
}

if ($driver !== 'Postgre') {
    echo "⚠️  ATTENTION: Le driver n'est pas défini sur 'Postgre' dans .env\n";
    echo "   Driver actuel: $driver\n\n";
}

echo "Tentative de connexion à PostgreSQL...\n";

$connectionString = "host=$host port=$port dbname=$dbname user=$user password=$password";

$conn = @pg_connect($connectionString);

if ($conn) {
    echo "✅ SUCCÈS: Connexion à PostgreSQL réussie!\n\n";

    // Test query
    $result = pg_query($conn, "SELECT version()");
    if ($result) {
        $version = pg_fetch_result($result, 0, 0);
        echo "📊 Version PostgreSQL:\n   " . $version . "\n\n";
    }

    // Check if uuid extension exists
    $result = pg_query($conn, "SELECT EXISTS(SELECT 1 FROM pg_extension WHERE extname = 'uuid-ossp') as has_uuid");
    if ($result) {
        $row = pg_fetch_assoc($result);
        $hasUuid = $row['has_uuid'];

        if ($hasUuid === 't' || $hasUuid === true) {
            echo "✅ Extension UUID-OSSP: Installée\n";
        } else {
            echo "⚠️  Extension UUID-OSSP: Non installée\n";
            echo "   Tentative d'installation automatique...\n";
            $installResult = @pg_query($conn, 'CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');
            if ($installResult) {
                echo "   ✅ Extension UUID-OSSP installée avec succès!\n";
            } else {
                echo "   ❌ Impossible d'installer l'extension (permissions insuffisantes)\n";
                echo "   Connectez-vous à PostgreSQL et exécutez manuellement:\n";
                echo "   CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\";\n";
            }
        }
    }

    pg_close($conn);
    echo "\n✅ Configuration correcte! Vous pouvez maintenant exécuter:\n";
    echo "   php spark migrate\n";
    echo "   php spark db:seed BusinessSectorSeeder\n";
} else {
    echo "❌ ERREUR: Impossible de se connecter à PostgreSQL\n\n";
    echo "Vérifiez que:\n";
    echo "1. PostgreSQL est démarré\n";
    echo "2. La base de données '$dbname' existe (créez-la si nécessaire)\n";
    echo "   Commande: psql -U postgres -c \"CREATE DATABASE $dbname;\"\n";
    echo "3. L'utilisateur '$user' existe et a les droits d'accès\n";
    echo "   Nom d'utilisateur standard PostgreSQL: 'postgres'\n";
    echo "4. Le mot de passe est correct\n";
    echo "5. Le port 5432 est le bon port\n\n";
    echo "Pour créer la base de données et l'utilisateur:\n";
    echo "psql -U postgres\n";
    echo "CREATE DATABASE $dbname;\n";
    if ($user !== 'postgres') {
        echo "CREATE USER $user WITH PASSWORD '$password';\n";
        echo "GRANT ALL PRIVILEGES ON DATABASE $dbname TO $user;\n";
    }
    exit(1);
}

<?php
/**
 * MySQL connection configuration.
 * Values come from environment variables passed by docker-compose.
 * No sensitive value is stored in this file — it can be committed safely.
 */

$db_host = getenv('DB_HOST') ?: 'mysql';
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');

// Safety: refuse to continue if the variables are not loaded
if (empty($db_name) || empty($db_user) || empty($db_pass)) {
    http_response_code(500);
    die("Erreur de configuration : variables d'environnement manquantes. Verifie ton fichier .env a la racine du projet.");
}

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // The detailed error ALWAYS goes to the logs (view with `docker compose logs php`)
    error_log('PDO connection failed: ' . $e->getMessage());

    http_response_code(500);
    // In prod, NEVER show the SQL message to the visitor (info leak: db/table names).
    if ((getenv('APP_ENV') ?: 'production') === 'development') {
        die("Erreur de connexion MySQL : " . htmlspecialchars($e->getMessage()));
    }
    die("Erreur interne, reessaie plus tard.");
}

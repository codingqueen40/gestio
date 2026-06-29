<?php
/**
 * Front controller : unique point d'entrée de l'application.
 * Toutes les requêtes (hors fichiers réels) sont réécrites ici par .htaccess.
 */

require __DIR__ . '/../src/config.php';            // PDO ($pdo) + session
require_once __DIR__ . '/../src/libs/auth.php';
require_once __DIR__ . '/../src/libs/user.php';
require_once __DIR__ . '/../src/libs/category.php';
require_once __DIR__ . '/../src/libs/expense.php';
require_once __DIR__ . '/../src/libs/budget.php';

$routes = require __DIR__ . '/../src/routes.php';

// Méthode + chemin demandés (sans query string, sans slash final superflu).
$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path   = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

// Exposé au header pour surligner l'onglet actif.
$currentPath = $path;

$key = $method . ' ' . $path;

if (isset($routes[$key])) {
    require __DIR__ . '/../src/controllers/' . $routes[$key];
} else {
    http_response_code(404);
    require __DIR__ . '/../src/controllers/not_found.php';
}

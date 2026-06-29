<?php
/** Suppression d'une catégorie (POST only, CSRF, scope utilisateur). */
requireLogin();
csrfCheck();

$userId     = currentUserId();
$categoryId = (int) ($_POST['id_category'] ?? 0);

// La catégorie doit appartenir au user connecté.
$category = getCategoryById($pdo, $userId, $categoryId);
if ($category === null) {
    http_response_code(404);
    require __DIR__ . '/not_found.php';
    return;
}

// Bloquer la suppression si des dépenses utilisent cette catégorie.
if (countExpensesInCategory($pdo, $userId, $categoryId) > 0) {
    header("Location: /categories?error=in_use");
    exit;
}

deleteCategory($pdo, $userId, $categoryId);
header("Location: /categories?deleted=1");
exit;

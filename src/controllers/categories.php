<?php
/** Liste et gestion des catégories personnalisées. */
requireLogin();

$userId     = currentUserId();
$categories = getCategories($pdo, $userId);

// Enrichit chaque catégorie avec le nombre de dépenses liées.
foreach ($categories as &$cat) {
    $cat['expense_count'] = countExpensesInCategory($pdo, $userId, (int) $cat['id_category']);
}
unset($cat);

require __DIR__ . '/../views/categories.php';

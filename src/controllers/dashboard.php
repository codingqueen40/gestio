<?php
/** Tableau de bord personnel — réservé aux utilisateurs connectés. */
requireLogin();

$categories   = getCategories($pdo);
$expenses     = getExpensesByUser($pdo, currentUserId());

// Cartes KPI : toujours calculées sur l'ensemble des dépenses (indépendant des filtres).
$total        = sumExpenses($expenses);
$currentMonth = date('Y-m');
$monthlyTotal = sumExpensesForMonth($expenses, $currentMonth);

// --- Filtres (mois + catégorie) appliqués à la liste affichée ---
$months    = getExpenseMonths($expenses);
$validCats = array_map('intval', array_column($categories, 'id_category'));

// On normalise les valeurs reçues : une valeur invalide retombe sur « tous ».
$filterMonth    = $_GET['month'] ?? '';
$filterCategory = $_GET['category'] ?? '';
if ($filterMonth !== '' && !in_array($filterMonth, $months, true)) {
    $filterMonth = '';
}
if ($filterCategory !== '' && !in_array((int) $filterCategory, $validCats, true)) {
    $filterCategory = '';
}

$filtered      = filterExpenses(
    $expenses,
    $filterMonth !== '' ? $filterMonth : null,
    $filterCategory !== '' ? (int) $filterCategory : null
);
$filteredTotal = sumExpenses($filtered);
$hasFilter     = $filterMonth !== '' || $filterCategory !== '';

// Répartition par catégorie pour le graphique (suit la liste filtrée affichée).
$byCategory    = sumByCategory($filtered);

require __DIR__ . '/../views/dashboard.php';

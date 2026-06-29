<?php
/** Tableau de bord personnel — réservé aux utilisateurs connectés. */
requireLogin();

// Génère les dépenses récurrentes actives arrivées à échéance ce mois.
$generatedCount = generateRecurringExpenses($pdo, currentUserId());

$categories   = getCategories($pdo, currentUserId());
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
$filterSearch   = trim($_GET['search'] ?? '');
if ($filterMonth !== '' && !in_array($filterMonth, $months, true)) {
    $filterMonth = '';
}
if ($filterCategory !== '' && !in_array((int) $filterCategory, $validCats, true)) {
    $filterCategory = '';
}

$filtered      = filterExpenses(
    $expenses,
    $filterMonth !== '' ? $filterMonth : null,
    $filterCategory !== '' ? (int) $filterCategory : null,
    $filterSearch !== '' ? $filterSearch : null
);
$filteredTotal = sumExpenses($filtered);
$hasFilter     = $filterMonth !== '' || $filterCategory !== '' || $filterSearch !== '';

// Répartition par catégorie pour le graphique (suit la liste filtrée affichée).
$byCategory    = sumByCategory($filtered);

// Évolution mensuelle (toutes dépenses, indépendante des filtres d'affichage).
$byMonth       = sumByMonth($expenses);

// --- Budgets du mois courant (indépendants des filtres d'affichage) ---
$categoryBudgets = getCategoryBudgets($pdo, currentUserId());
$globalBudget    = getGlobalBudget($pdo, currentUserId());
$currentExpenses = filterExpenses($expenses, $currentMonth, null);
$budgetRows      = budgetProgress($categories, $categoryBudgets, $currentExpenses);
// Le « dépensé » global du mois = $monthlyTotal (déjà calculé plus haut).
$globalPct       = ($globalBudget !== null && $globalBudget > 0)
    ? ($monthlyTotal / $globalBudget) * 100
    : 0.0;
$globalOver      = $globalBudget !== null && $monthlyTotal > $globalBudget;
$hasBudgets      = $globalBudget !== null || count($budgetRows) > 0;

require __DIR__ . '/../views/dashboard.php';

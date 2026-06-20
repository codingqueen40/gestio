<?php
/** Tableau de bord personnel — réservé aux utilisateurs connectés. */
requireLogin();

$categories   = getCategories($pdo);
$expenses     = getExpensesByUser($pdo, currentUserId());

$total        = sumExpenses($expenses);
$currentMonth = date('Y-m');
$monthlyTotal = sumExpensesForMonth($expenses, $currentMonth);

require __DIR__ . '/../views/dashboard.php';

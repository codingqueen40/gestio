<?php
/** Liste des dépenses récurrentes de l'utilisateur connecté. */
requireLogin();

$recurringExpenses = getRecurringExpenses($pdo, currentUserId());

require __DIR__ . '/../views/recurrences.php';

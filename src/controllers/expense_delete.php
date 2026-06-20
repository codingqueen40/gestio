<?php
/** Suppression d'une dépense (POST only, protégé par CSRF et scope utilisateur). */
requireLogin();
csrfCheck();

$expenseId = (int) ($_POST['id_expense'] ?? 0);

if ($expenseId > 0) {
    // deleteExpense filtre par id_user : impossible de supprimer la dépense d'autrui.
    deleteExpense($pdo, currentUserId(), $expenseId);
}

header('Location: /?deleted=1');
exit;

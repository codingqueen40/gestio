<?php
/** Suppression d'une dépense récurrente (POST + CSRF). */
requireLogin();
csrfCheck();

$id = (int) ($_POST['id_recurring'] ?? 0);

$recurring = getRecurringExpenseById($pdo, currentUserId(), $id);
if ($recurring === null) {
    http_response_code(404);
    require __DIR__ . '/not_found.php';
    return;
}

deleteRecurringExpense($pdo, currentUserId(), $id);
header("Location: /recurrences?deleted=1");
exit;

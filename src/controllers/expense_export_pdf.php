<?php
/** Export PDF — génère une page HTML imprimable (Ctrl+P → Enregistrer en PDF). */
requireLogin();

$expenses = getExpensesByUser($pdo, currentUserId());
$total    = sumExpenses($expenses);
$username = currentUsername();

// Regrouper par mois, du plus récent au plus ancien.
$byMonth = [];
foreach ($expenses as $d) {
    $byMonth[substr($d['expense_date'], 0, 7)][] = $d;
}
krsort($byMonth);

require __DIR__ . '/../views/expense_export_pdf.php';

<?php
/** Export CSV de toutes les dépenses de l'utilisateur connecté (stream direct). */
requireLogin();

$expenses = getExpensesByUser($pdo, currentUserId());

$filename = 'depenses_' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 (compatibilité Excel)
fputcsv($out, ['Date', 'Libellé', 'Catégorie', 'Montant', 'Note'], ',', '"', '\\');

foreach ($expenses as $d) {
    fputcsv($out, [
        $d['expense_date'],
        $d['title'],
        $d['category_name'] ?? '',
        number_format((float) $d['amount'], 2, '.', ''),
        $d['note'] ?? '',
    ], ',', '"', '\\');
}

fclose($out);
exit;

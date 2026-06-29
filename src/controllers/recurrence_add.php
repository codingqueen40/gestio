<?php
/** Ajout d'une dépense récurrente : formulaire (GET) + traitement (POST). */
requireLogin();

$errors = [];
$old = [
    'title'        => '',
    'amount'       => '',
    'category'     => '',
    'day_of_month' => '1',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();

    $old['title']        = trim($_POST['title'] ?? '');
    $old['amount']       = trim($_POST['amount'] ?? '');
    $old['category']     = trim($_POST['category'] ?? '');
    $old['day_of_month'] = trim($_POST['day_of_month'] ?? '');

    if ($old['title'] === '') {
        $errors[] = "Le libellé est obligatoire.";
    }

    if ($old['amount'] === '' || !is_numeric($old['amount'])) {
        $errors[] = "Le montant doit être un nombre.";
    } elseif ((float) $old['amount'] <= 0) {
        $errors[] = "Le montant doit être supérieur à 0.";
    }

    if (!in_array((int) $old['category'], array_map('intval', getCategoryIds($pdo, currentUserId())), true)) {
        $errors[] = "La catégorie sélectionnée n'existe pas.";
    }

    $day = (int) $old['day_of_month'];
    if ($day < 1 || $day > 28) {
        $errors[] = "Le jour doit être compris entre 1 et 28.";
    }

    if (count($errors) === 0) {
        addRecurringExpense(
            $pdo,
            currentUserId(),
            $old['title'],
            (float) $old['amount'],
            (int) $old['category'],
            $day
        );
        header("Location: /recurrences?added=1");
        exit;
    }
}

$categories = getCategories($pdo, currentUserId());

require __DIR__ . '/../views/recurrence_add.php';

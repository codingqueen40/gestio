<?php
/** Édition d'une dépense récurrente : formulaire (GET) + traitement (POST). */
requireLogin();

$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$id     = (int) ($isPost ? ($_POST['id_recurring'] ?? 0) : ($_GET['id'] ?? 0));

$recurring = getRecurringExpenseById($pdo, currentUserId(), $id);
if ($recurring === null) {
    http_response_code(404);
    require __DIR__ . '/not_found.php';
    return;
}

$errors = [];
$old = [
    'title'        => $recurring['title'],
    'amount'       => $recurring['amount'],
    'category'     => (string) $recurring['id_category'],
    'day_of_month' => (string) $recurring['day_of_month'],
    'active'       => (bool) $recurring['active'],
];

if ($isPost) {
    csrfCheck();

    $old['title']        = trim($_POST['title'] ?? '');
    $old['amount']       = trim($_POST['amount'] ?? '');
    $old['category']     = trim($_POST['category'] ?? '');
    $old['day_of_month'] = trim($_POST['day_of_month'] ?? '');
    $old['active']       = isset($_POST['active']);

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
        updateRecurringExpense(
            $pdo,
            currentUserId(),
            $id,
            $old['title'],
            (float) $old['amount'],
            (int) $old['category'],
            $day,
            $old['active']
        );
        header("Location: /recurrences?updated=1");
        exit;
    }
}

$categories = getCategories($pdo, currentUserId());

require __DIR__ . '/../views/recurrence_edit.php';

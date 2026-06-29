<?php
/** Ajout d'une dépense : formulaire (GET) + traitement (POST). */
requireLogin();

$errors = [];
$old = [
    'title'    => '',
    'amount'   => '',
    'date'     => date('Y-m-d'),
    'category' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();

    $old['title']    = trim($_POST['title'] ?? '');
    $old['amount']   = trim($_POST['amount'] ?? '');
    $old['date']     = trim($_POST['date'] ?? '');
    $old['category'] = trim($_POST['category'] ?? '');

    // Validation
    if ($old['title'] === '') {
        $errors[] = "Le libellé est obligatoire";
    }

    if ($old['amount'] === '' || !is_numeric($old['amount'])) {
        $errors[] = "Le montant doit être un nombre";
    } elseif ((float) $old['amount'] <= 0) {
        $errors[] = "Le montant doit être supérieur à 0";
    }

    $d = DateTime::createFromFormat('Y-m-d', $old['date']);
    if (!$d || $d->format('Y-m-d') !== $old['date']) {
        $errors[] = "La date n'est pas valide";
    }

    if (!in_array((int) $old['category'], array_map('intval', getCategoryIds($pdo, currentUserId())), true)) {
        $errors[] = "La catégorie sélectionnée n'existe pas";
    }

    if (count($errors) === 0) {
        $ok = addExpense(
            $pdo,
            currentUserId(),
            $old['title'],
            (float) $old['amount'],
            $old['date'],
            (int) $old['category']
        );

        if ($ok) {
            header("Location: /?added=1");
            exit;
        }
        $errors[] = "Une erreur s'est produite lors de l'ajout";
    }
}

$categories = getCategories($pdo, currentUserId());

require __DIR__ . '/../views/expense_add.php';

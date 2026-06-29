<?php
/** Édition d'une dépense : formulaire pré-rempli (GET ?id=) + traitement (POST). */
requireLogin();

$isPost    = $_SERVER['REQUEST_METHOD'] === 'POST';
$expenseId = (int) ($isPost ? ($_POST['id_expense'] ?? 0) : ($_GET['id'] ?? 0));

// La dépense doit exister ET appartenir au user connecté (scope id_user).
// Même réponse (404) qu'elle n'existe pas ou qu'elle soit à autrui → pas d'énumération.
$expense = getExpenseById($pdo, currentUserId(), $expenseId);
if ($expense === null) {
    http_response_code(404);
    require __DIR__ . '/not_found.php';
    return;
}

$errors = [];
// Valeurs initiales = la dépense en base ; écrasées par le POST en cas d'erreur de saisie.
$old = [
    'title'    => $expense['title'],
    'amount'   => $expense['amount'],
    'date'     => $expense['expense_date'],
    'category' => (string) $expense['id_category'],
    'note'     => $expense['note'] ?? '',
];

if ($isPost) {
    csrfCheck();

    $old['title']    = trim($_POST['title'] ?? '');
    $old['amount']   = trim($_POST['amount'] ?? '');
    $old['date']     = trim($_POST['date'] ?? '');
    $old['category'] = trim($_POST['category'] ?? '');
    $old['note']     = trim($_POST['note'] ?? '');

    // Validation (identique à l'ajout).
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
        // L'appartenance est déjà vérifiée plus haut : on redirige vers le succès
        // même si la modification est un no-op (aucune valeur changée → 0 ligne affectée).
        updateExpense(
            $pdo,
            currentUserId(),
            $expenseId,
            $old['title'],
            (float) $old['amount'],
            $old['date'],
            (int) $old['category'],
            $old['note']
        );

        header("Location: /?updated=1");
        exit;
    }
}

$categories = getCategories($pdo, currentUserId());

require __DIR__ . '/../views/expense_edit.php';

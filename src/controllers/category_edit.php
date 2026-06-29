<?php
/** Édition d'une catégorie : formulaire pré-rempli (GET ?id=) + traitement (POST). */
requireLogin();

$isPost     = $_SERVER['REQUEST_METHOD'] === 'POST';
$categoryId = (int) ($isPost ? ($_POST['id_category'] ?? 0) : ($_GET['id'] ?? 0));

// La catégorie doit exister ET appartenir au user connecté.
$category = getCategoryById($pdo, currentUserId(), $categoryId);
if ($category === null) {
    http_response_code(404);
    require __DIR__ . '/not_found.php';
    return;
}

$errors = [];
$old = ['name' => $category['name'], 'color' => $category['color']];

if ($isPost) {
    csrfCheck();

    $old['name']  = trim($_POST['name'] ?? '');
    $old['color'] = trim($_POST['color'] ?? '');

    if ($old['name'] === '') {
        $errors[] = "Le nom est obligatoire";
    } elseif (mb_strlen($old['name']) > 50) {
        $errors[] = "Le nom ne peut pas dépasser 50 caractères";
    }

    if (!isValidHexColor($old['color'])) {
        $errors[] = "La couleur doit être au format hexadécimal (#RRGGBB)";
    }

    if (count($errors) === 0) {
        updateCategory($pdo, currentUserId(), $categoryId, $old['name'], $old['color']);
        header("Location: /categories?updated=1");
        exit;
    }
}

require __DIR__ . '/../views/category_edit.php';

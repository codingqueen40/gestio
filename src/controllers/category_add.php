<?php
/** Ajout d'une catégorie : formulaire (GET) + traitement (POST). */
requireLogin();

$errors = [];
$old = ['name' => '', 'color' => '#6c757d'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        addCategory($pdo, currentUserId(), $old['name'], $old['color']);
        header("Location: /categories?added=1");
        exit;
    }
}

require __DIR__ . '/../views/category_add.php';

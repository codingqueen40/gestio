<?php
/** Réinitialisation du mot de passe via token reçu par email. */

$token  = trim($_GET['token'] ?? $_POST['token'] ?? '');
$errors = [];

$reset = validatePasswordReset($pdo, $token);
if ($reset === null) {
    header("Location: /mot-de-passe-oublie?expired=1");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();

    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';

    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit faire au moins 8 caractères.";
    } elseif ($password !== $confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (count($errors) === 0) {
        updateUserPassword($pdo, (int) $reset['id_user'], $password);
        consumePasswordReset($pdo, (int) $reset['id_reset']);
        header("Location: /login?password_reset=1");
        exit;
    }
}

require __DIR__ . '/../views/reset_password.php';

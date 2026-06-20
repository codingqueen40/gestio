<?php
/** Inscription : affiche le formulaire (GET) et crée le compte (POST). */
requireGuest();

$errors = [];
$old = ['username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();

    // On mémorise les valeurs saisies pour les ré-afficher en cas d'erreur
    $old['username'] = trim($_POST['username'] ?? '');
    $old['email'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($old['username'] === '') {
        $errors[] = "Le nom d'utilisateur est obligatoire";
    }
    if ($old['email'] === '') {
        $errors[] = "L'email est obligatoire";
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }
    if ($password === '') {
        $errors[] = "Le mot de passe est obligatoire";
    } elseif (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
    }

    // Unicité de l'email (en plus de la contrainte UNIQUE en base)
    if (count($errors) === 0 && emailExists($pdo, $old['email'])) {
        $errors[] = "Un compte existe déjà avec cet email";
    }

    if (count($errors) === 0) {
        $res = addUser($pdo, $old['username'], $old['email'], $password);

        if ($res) {
            header("Location: /login?registered=1");
            exit;
        } else {
            $errors[] = "Une erreur s'est produite lors de votre inscription";
        }
    }
}

require __DIR__ . '/../views/signup.php';

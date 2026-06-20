<?php
/** Connexion : affiche le formulaire (GET) et traite la soumission (POST). */
requireGuest();

$errors = [];
$old = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();

    $old['email'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($old['email'] === '' || $password === '') {
        $errors[] = "Email et mot de passe sont obligatoires";
    } else {
        $user = verifyUserLogin($pdo, $old['email'], $password);
        if ($user) {
            loginUser($user);
            header('Location: /');
            exit;
        }
        // Message générique : ne pas révéler si c'est l'email ou le mot de passe.
        $errors[] = "Email ou mot de passe incorrect";
    }
}

require __DIR__ . '/../views/login.php';

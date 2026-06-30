<?php
/** Connexion : affiche le formulaire (GET) et traite la soumission (POST). */
requireGuest();

$errors = [];
$old = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();

    $old['email'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $ip   = clientIp();
    $lock = loginLockRemaining($pdo, $ip, $old['email']);

    if ($lock > 0) {
        // Trop d'échecs récents : on refuse sans même tester le mot de passe.
        $minutes  = (int) ceil($lock / 60);
        $errors[] = "Trop de tentatives de connexion. Réessaie dans $minutes minute" . ($minutes > 1 ? 's' : '') . ".";
    } elseif ($old['email'] === '' || $password === '') {
        $errors[] = "Email et mot de passe sont obligatoires";
    } else {
        $user = verifyUserLogin($pdo, $old['email'], $password);
        if ($user) {
            clearLoginAttempts($pdo, $ip, $old['email']); // succès → on remet le compteur à zéro
            loginUser($user);
            if (!empty($_POST['remember'])) {
                setRememberToken($pdo, (int) $user['id_user']);
            }
            header('Location: /');
            exit;
        }
        // Échec : on enregistre la tentative (anti-brute-force) et on purge le vieux.
        recordFailedLogin($pdo, $ip, $old['email']);
        purgeOldLoginAttempts($pdo);
        // Message générique : ne pas révéler si c'est l'email ou le mot de passe.
        $errors[] = "Email ou mot de passe incorrect";
    }
}

require __DIR__ . '/../views/login.php';

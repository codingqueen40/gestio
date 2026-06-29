<?php
/** Demande de réinitialisation de mot de passe : formulaire (GET) + envoi (POST). */

$sent       = false;
$errors     = [];
$devResetUrl = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();

    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    } else {
        $user = getUserByEmail($pdo, $email);

        if ($user !== null) {
            $token    = createPasswordReset($pdo, (int) $user['id_user']);
            $proto    = (getenv('APP_ENV') === 'production') ? 'https' : 'http';
            $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $resetUrl = "$proto://$host/reinitialiser-mdp?token=$token";

            $subject = 'Réinitialisation de ton mot de passe — Gestio';
            $body    = "Bonjour {$user['username']},\n\n"
                . "Clique sur ce lien pour réinitialiser ton mot de passe :\n$resetUrl\n\n"
                . "Ce lien expire dans 1 heure. Si tu n'es pas à l'origine de cette demande, ignore cet email.";
            @mail($email, $subject, $body, "From: noreply@gestio.local");

            // En développement : affiche le lien directement (pas de serveur mail requis).
            if (getenv('APP_ENV') !== 'production') {
                $devResetUrl = $resetUrl;
            }
        }

        // Toujours afficher "email envoyé" : ne pas révéler si l'email existe.
        $sent = true;
    }
}

require __DIR__ . '/../views/forgot_password.php';

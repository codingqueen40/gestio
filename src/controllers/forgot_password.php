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
            $token = createPasswordReset($pdo, (int) $user['id_user']);

            // En prod, on bâtit le lien depuis APP_URL (URL canonique injectée par
            // docker-compose) et JAMAIS depuis l'en-tête Host fourni par le client,
            // qui est falsifiable (empoisonnement du lien de reset, #42).
            // Hors prod (dev local), on retombe sur le Host courant pour rester pratique.
            $appUrl = getenv('APP_URL');
            if ($appUrl) {
                $base = rtrim($appUrl, '/');
            } else {
                $proto = (getenv('APP_ENV') === 'production') ? 'https' : 'http';
                $host  = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $base  = "$proto://$host";
            }
            $resetUrl = "$base/reinitialiser-mdp?token=$token";

            $subject = 'Réinitialisation de ton mot de passe — Gestio';
            $body    = "Bonjour {$user['username']},\n\n"
                . "Clique sur ce lien pour réinitialiser ton mot de passe :\n$resetUrl\n\n"
                . "Ce lien expire dans 1 heure. Si tu n'es pas à l'origine de cette demande, ignore cet email.";

            // L'expéditeur DOIT être l'adresse vérifiée chez le relais (SMTP_FROM) :
            // le relais refuse un From non vérifié, et DMARC s'aligne sur le domaine
            // de cet en-tête. Un domaine inventé (.local) ferait échouer les deux.
            $from = getenv('SMTP_FROM') ?: 'noreply@gestio.local';

            // Le fallback n'a de sens qu'en dev. En prod, un SMTP_FROM vide rejouerait
            // silencieusement le bug d'origine (domaine .local => rejet Brevo + DMARC KO) :
            // on le trace, sinon la panne est invisible.
            if (!getenv('SMTP_FROM') && getenv('APP_ENV') === 'production') {
                error_log('Gestio: SMTP_FROM absent en production — expéditeur invalide, le mail sera rejeté.');
            }

            $headers = "From: Gestio <$from>\r\n"
                . "Reply-To: $from\r\n"
                . "Content-Type: text/plain; charset=UTF-8";

            // Le sujet contient des accents : sans encodage MIME (RFC 2047),
            // les clients mail affichent du charabia.
            $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

            if (!@mail($email, $subject, $body, $headers) && getenv('APP_ENV') === 'production') {
                error_log("Gestio: échec d'envoi du mail de reset à $email");
            }

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

<?php
/**
 * Helpers d'authentification : session + protection CSRF.
 * La session est démarrée dans src/config.php (toujours inclus avant ce fichier).
 */

/** Ouvre la session applicative pour un utilisateur authentifié. */
function loginUser(array $user): void
{
    session_regenerate_id(true); // anti session-fixation
    $_SESSION['id_user']  = (int) $user['id_user'];
    $_SESSION['username'] = $user['username'] ?? '';
}

/** Ferme la session : vide les données, détruit le cookie et la session. */
function logoutUser(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

/** L'utilisateur est-il connecté ? */
function isLoggedIn(): bool
{
    return !empty($_SESSION['id_user']);
}

/** Identifiant de l'utilisateur connecté, ou null. */
function currentUserId(): ?int
{
    return isset($_SESSION['id_user']) ? (int) $_SESSION['id_user'] : null;
}

/** Nom de l'utilisateur connecté (chaîne vide si absent). */
function currentUsername(): string
{
    return $_SESSION['username'] ?? '';
}

/** Réserve une page aux utilisateurs connectés. */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }
}

/** Réserve une page aux visiteurs (login/signup) : redirige les connectés. */
function requireGuest(): void
{
    if (isLoggedIn()) {
        header('Location: /');
        exit;
    }
}

/** Génère (au besoin) et retourne le jeton CSRF de la session. */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Champ caché à insérer dans les formulaires. */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken()) . '">';
}

/** Vérifie le jeton CSRF d'une requête POST, sinon coupe en HTTP 403. */
function csrfCheck(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $sent)) {
        http_response_code(403);
        die('Requête invalide (jeton CSRF manquant ou incorrect).');
    }
}

// ---------------------------------------------------------------------------
// Se souvenir de moi (persistent login via cookie sécurisé + DB)
// ---------------------------------------------------------------------------

/** Génère un token remember-me, le stocke (haché) en DB et pose le cookie 30 jours. */
function setRememberToken(PDO $pdo, int $userId): void
{
    $token   = bin2hex(random_bytes(32));
    $hash    = hash('sha256', $token);
    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));

    $pdo->prepare("
        INSERT INTO remember_token (id_user, token_hash, expires_at)
        VALUES (:uid, :hash, :exp)
    ")->execute([':uid' => $userId, ':hash' => $hash, ':exp' => $expires]);

    setcookie('remember_token', $token, [
        'expires'  => time() + 30 * 24 * 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => (getenv('APP_ENV') === 'production'),
    ]);
}

/** Supprime le token DB correspondant au cookie et efface ce dernier. */
function clearRememberToken(PDO $pdo): void
{
    $token = $_COOKIE['remember_token'] ?? null;
    if ($token !== null) {
        $hash = hash('sha256', $token);
        $pdo->prepare("DELETE FROM remember_token WHERE token_hash = :hash")
            ->execute([':hash' => $hash]);
    }
    setcookie('remember_token', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => (getenv('APP_ENV') === 'production'),
    ]);
}

/**
 * Si aucune session active mais cookie remember_token valide :
 * auto-connecte l'utilisateur et effectue une rotation du token (anti-replay).
 * À appeler au tout début du front controller, avant le dispatch des routes.
 */
function checkRememberToken(PDO $pdo): void
{
    if (isLoggedIn()) {
        return;
    }
    $token = $_COOKIE['remember_token'] ?? null;
    if ($token === null) {
        return;
    }
    $hash = hash('sha256', $token);
    // expires_at est écrit par PHP (date.timezone = Europe/Berlin) : on le
    // compare donc à l'horloge PHP, et non à NOW() qui suit le fuseau de
    // MySQL (UTC dans le conteneur). Sinon le décalage fausse la durée de
    // vie du token — et s'inverserait en tokens morts-nés si un jour PHP
    // passait derrière MySQL.
    $stmt = $pdo->prepare("
        SELECT r.id_token, r.id_user, u.username
        FROM remember_token r
        JOIN `user` u ON r.id_user = u.id_user
        WHERE r.token_hash = :hash AND r.expires_at > :now
    ");
    $stmt->execute([':hash' => $hash, ':now' => date('Y-m-d H:i:s')]);
    $row = $stmt->fetch();

    if ($row === false) {
        // Token invalide ou expiré : nettoyer le cookie orphelin.
        setcookie('remember_token', '', ['expires' => time() - 3600, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
        return;
    }

    // Rotation : supprimer l'ancien token avant de créer le nouveau (empêche le replay).
    $pdo->prepare("DELETE FROM remember_token WHERE id_token = :id")
        ->execute([':id' => $row['id_token']]);

    loginUser(['id_user' => $row['id_user'], 'username' => $row['username']]);
    setRememberToken($pdo, (int) $row['id_user']);
}

// ---------------------------------------------------------------------------
// Réinitialisation du mot de passe
// ---------------------------------------------------------------------------

/** Crée un token de reset (valide 1h), supprime les précédents du même user. Retourne le token brut. */
function createPasswordReset(PDO $pdo, int $userId): string
{
    $pdo->prepare("DELETE FROM password_reset WHERE id_user = :uid")
        ->execute([':uid' => $userId]);

    $token   = bin2hex(random_bytes(32));
    $hash    = hash('sha256', $token);
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $pdo->prepare("
        INSERT INTO password_reset (id_user, token_hash, expires_at)
        VALUES (:uid, :hash, :exp)
    ")->execute([':uid' => $userId, ':hash' => $hash, ':exp' => $expires]);

    return $token;
}

/** Valide un token de reset. Retourne ['id_reset', 'id_user'] ou null si invalide/expiré. */
function validatePasswordReset(PDO $pdo, string $token): ?array
{
    if (strlen($token) !== 64) {
        return null;
    }
    $hash = hash('sha256', $token);
    // expires_at est écrit par PHP (date.timezone = Europe/Berlin) : on le
    // compare donc à l'horloge PHP, et non à NOW() qui suit le fuseau de
    // MySQL (UTC dans le conteneur). Sinon le décalage fausse la durée de
    // vie du token — et s'inverserait en tokens morts-nés si un jour PHP
    // passait derrière MySQL.
    $stmt = $pdo->prepare("
        SELECT id_reset, id_user FROM password_reset
        WHERE token_hash = :hash AND expires_at > :now
    ");
    $stmt->execute([':hash' => $hash, ':now' => date('Y-m-d H:i:s')]);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

/** Consomme (supprime) un token de reset après usage. */
function consumePasswordReset(PDO $pdo, int $resetId): void
{
    $pdo->prepare("DELETE FROM password_reset WHERE id_reset = :id")
        ->execute([':id' => $resetId]);
}

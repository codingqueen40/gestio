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

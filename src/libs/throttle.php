<?php
/**
 * Anti-brute-force sur la connexion (#24).
 *
 * Stratégie : on enregistre chaque échec de login (IP + email) dans la table
 * `login_attempt`. Au-delà d'un seuil d'échecs récents pour une même IP OU un
 * même email, on verrouille temporairement les tentatives.
 *
 * - Le verrou par IP arrête le spray (un attaquant qui teste plein de comptes).
 * - Le verrou par email protège un compte ciblé (même si l'attaquant tourne ses IP).
 *   ⚠️ Tradeoff connu : un tiers peut provoquer le verrou d'un email (DoS léger
 *   ciblé). Acceptable ici : la fenêtre est courte et le compte se débloque seul.
 */

const LOGIN_MAX_ATTEMPTS = 5;     // échecs tolérés dans la fenêtre
const LOGIN_WINDOW_MIN   = 15;    // taille de la fenêtre glissante (minutes)
const LOGIN_LOCK_MIN     = 15;    // durée du verrou après dépassement (minutes)

/**
 * IP réelle du client.
 * En prod, l'app est derrière Caddy (reverse proxy) : REMOTE_ADDR est l'IP du
 * proxy, pas celle du client. Caddy APPENDE l'IP cliente à X-Forwarded-For, donc
 * la vraie IP est la DERNIÈRE entrée de la liste (les précédentes sont
 * potentiellement falsifiées par le client). Hors proxy (dev) : pas de XFF.
 */
function clientIp(): string
{
    $xff = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if ($xff !== '') {
        $parts = array_map('trim', explode(',', $xff));
        $last  = end($parts);
        if ($last !== false && filter_var($last, FILTER_VALIDATE_IP)) {
            return $last;
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/** Enregistre un échec de connexion pour cette IP + cet email. */
function recordFailedLogin(PDO $pdo, string $ip, string $email): void
{
    $pdo->prepare(
        "INSERT INTO login_attempt (ip, email, attempted_at) VALUES (:ip, :email, NOW())"
    )->execute([':ip' => $ip, ':email' => $email]);
}

/** Efface les échecs de cette IP + cet email (appelé après une connexion réussie). */
function clearLoginAttempts(PDO $pdo, string $ip, string $email): void
{
    $pdo->prepare(
        "DELETE FROM login_attempt WHERE ip = :ip OR email = :email"
    )->execute([':ip' => $ip, ':email' => $email]);
}

/**
 * Secondes de verrou restantes pour cette IP/email, 0 si non verrouillé.
 *
 * Verrou si, dans les LOGIN_WINDOW_MIN dernières minutes, le nombre d'échecs
 * (pour l'IP OU l'email) atteint LOGIN_MAX_ATTEMPTS. Le verrou court jusqu'à
 * LOGIN_LOCK_MIN minutes après la DERNIÈRE tentative échouée.
 */
function loginLockRemaining(PDO $pdo, string $ip, string $email): int
{
    // Tout le calcul temporel reste CÔTÉ SQL : on évite de comparer un timestamp
    // renvoyé par MySQL (son fuseau) avec time() de PHP (autre fuseau possible) —
    // un mélange qui fausse le reste. `remaining` = secondes jusqu'au déverrouillage.
    // Les fenêtres sont des constantes entières (pas des entrées utilisateur) →
    // inline sûr ; INTERVAL n'accepte de toute façon pas de placeholder lié.
    $stmt = $pdo->prepare("
        SELECT
            COUNT(*) AS nb,
            TIMESTAMPDIFF(
                SECOND,
                NOW(),
                MAX(attempted_at) + INTERVAL " . LOGIN_LOCK_MIN . " MINUTE
            ) AS remaining
        FROM login_attempt
        WHERE (ip = :ip OR email = :email)
          AND attempted_at > (NOW() - INTERVAL " . LOGIN_WINDOW_MIN . " MINUTE)
    ");
    $stmt->execute([':ip' => $ip, ':email' => $email]);
    $row = $stmt->fetch();

    if (!$row || (int) $row['nb'] < LOGIN_MAX_ATTEMPTS) {
        return 0;
    }

    $remaining = (int) $row['remaining'];
    return $remaining > 0 ? $remaining : 0;
}

/** Purge opportuniste des tentatives hors fenêtre (garde la table petite). */
function purgeOldLoginAttempts(PDO $pdo): void
{
    $pdo->exec(
        "DELETE FROM login_attempt WHERE attempted_at < (NOW() - INTERVAL " . LOGIN_LOCK_MIN . " MINUTE)"
    );
}

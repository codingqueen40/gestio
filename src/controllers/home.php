<?php
/**
 * Racine « / » : aiguillage selon l'état de connexion.
 * - Connecté  → tableau de bord personnel (délègue au contrôleur dashboard).
 * - Invité    → landing page publique (hero marketing + appels à l'action).
 */
if (isLoggedIn()) {
    require __DIR__ . '/dashboard.php';
    return;
}

require __DIR__ . '/../views/home.php';

<?php
require_once __DIR__ . '/../libs/auth.php';

$theme = in_array($_COOKIE['theme'] ?? '', ['light', 'dark']) ? $_COOKIE['theme'] : 'light';

$mainMenu = [
    '/'        => 'Accueil',
    '/about'   => 'À propos',
    '/contact' => 'Contact',
];

// Entrées réservées aux utilisateurs connectés (pages protégées).
if (isLoggedIn()) {
    $mainMenu['/budgets']      = 'Budgets';
    $mainMenu['/categories']   = 'Catégories';
    $mainMenu['/recurrences']  = 'Récurrences';
}

// Chemin courant fourni par le front controller (fallback sur '/').
$currentPage = $currentPath ?? '/';


?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="<?= $theme ?>">
<?php // valeur sanitisée : 'light' | 'dark' ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestio — Gestionnaire de dépenses</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/override-bootstrap.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>

    <div class="container">
        <header
            class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
            <div class="col-md-3 mb-2 mb-md-0"> <a href="/"
                    class="d-inline-flex link-body-emphasis text-decoration-none">
                    <img src="/assets/images/logo_gestio_cropped.png"
                        alt="logo Gestio" width="200">
                </a>
            </div>
            <ul
                class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
                <?php foreach ($mainMenu as $page => $title) : ?>
                <?php $isActive = $page === $currentPage; ?>

                <li class="nav-item"><a href="<?= $page ?>"
                        class="nav-link px-2 <?= $isActive ? 'active' : 'link-secondary' ?>"
                        <?= $isActive ? 'aria-current="page"' : '' ?>><?= $title ?></a>
                </li>

                <?php endforeach; ?>

            </ul>
            <div class="col-12 col-md-3 text-center text-md-end mt-1 mt-md-0">
                <button id="theme-toggle" type="button"
                    class="btn btn-outline-secondary btn-sm me-2"
                    aria-label="<?= $theme === 'dark' ? 'Passer en mode clair' : 'Passer en mode sombre' ?>">
                    <i
                        class="bi <?= $theme === 'dark' ? 'bi-sun-fill' : 'bi-moon-fill' ?>"></i>
                </button>
                <?php if (isLoggedIn()): ?>
                <a href="/profil"
                    class="me-2 link-secondary text-decoration-none">Bonjour
                    <?= htmlspecialchars(currentUsername()) ?></a>
                <form action="/logout" method="post" class="d-inline">
                    <?= csrfField() ?>
                    <button type="submit"
                        class="btn btn-outline-danger">Déconnexion</button>
                </form>
                <?php else: ?>
                <a href="/login" class="btn btn-outline-primary me-2">Connexion</a>
                <a href="/signup" class="btn btn-primary">S'inscrire</a>
                <?php endif; ?>
            </div>
        </header>

    </div>

    <main class="container">
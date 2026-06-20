<?php
require_once __DIR__ . '/../libs/auth.php';

$mainMenu = [
    '/'      => 'Home',
    '/about' => 'About',
];

// Chemin courant fourni par le front controller (fallback sur '/').
$currentPage = $currentPath ?? '/';


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionnaire de dépenses</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/override-bootstrap.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

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
            <div class="col-md-3 text-end">
                <?php if (isLoggedIn()): ?>
                <span class="me-2">Bonjour <?= htmlspecialchars(currentUsername()) ?></span>
                <a href="/logout" class="btn btn-outline-danger">Logout</a>
                <?php else: ?>
                <a href="/login" class="btn btn-outline-primary me-2">Login</a>
                <a href="/signup" class="btn btn-primary">Sign-up</a>
                <?php endif; ?>
            </div>
        </header>


        <nav class="navbar navbar-dark bg-dark mb-4">
            <div class="container">
                <span class="navbar-brand mb-0 h1">Gestionnaire de
                    dépenses</span>
                <span class="text-light small">Codingqueen40</span>
            </div>
        </nav>
    </div>

    <main class="container">
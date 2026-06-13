<?php
require __DIR__ . '/../src/config.php';

$categories = $pdo->query("SELECT * FROM category ORDER BY name")->fetchAll();

$expenses = $pdo->query("
    SELECT d.*, c.name AS category_name, c.color AS category_color
    FROM expense d
    LEFT JOIN category c ON d.id_category = c.id_category
    ORDER BY d.expense_date DESC, d.id_expense DESC
")->fetchAll();

$total = array_sum(array_column($expenses, 'amount'));
$total_mois = 0;
$mois_actuel = date('Y-m');
foreach ($expenses as $d) {
    if (substr($d['expense_date'], 0, 7) === $mois_actuel) {
        $total_mois += $d['amount'];
    }
}
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
                <li><a href="#" class="nav-link px-2 link-secondary">Home</a>
                </li>
                <li><a href="#" class="nav-link px-2">Features</a></li>
                <li><a href="#" class="nav-link px-2">Pricing</a></li>
                <li><a href="#" class="nav-link px-2">FAQs</a></li>
                <li><a href="#" class="nav-link px-2">About</a></li>
            </ul>
            <div class="col-md-3 text-end"> <button type="button"
                    class="btn btn-outline-primary me-2">Login</button> <button
                    type="button" class="btn btn-primary">Sign-up</button>
            </div>
        </header>
    </div>

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand mb-0 h1">Gestionnaire de dépenses</span>
            <span class="text-light small">Codingqueen40</span>
        </div>
    </nav>

    <div class="container">
        <main class="container">
            <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                <div class="col-10 col-sm-8 col-lg-6"> <img
                        src="assets/images/logo_gestio_cropped.png"
                        class="d-block mx-lg-auto img-fluid" alt="logo Gestio"
                        width="700" loading="lazy"> </div>
                <div class="col-lg-6">
                    <h1 class="display-5 fw-bold text-body-emphasis lh-1 mb-3">
                        Avec Gestio, gère tes dépenses facilement !</h1>
                    <p class="lead">Très simple d'utilisation, administre tes
                        dépenses en quelques
                        clics.</p>
                    <div
                        class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <button type="button"
                            class="btn btn-primary btn-lg px-4 me-md-2">Primary</button>
                        <button type="button"
                            class="btn btn-outline-secondary btn-lg px-4">Default</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div class="container">

        <div class="alert alert-success">
            <strong>Stack OK</strong> — PHP <?= phpversion() ?> + MySQL via
            Docker. Tout est connecte.
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card text-bg-primary h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 opacity-75">Total general
                        </h6>
                        <h3 class="card-title mb-0">
                            <?= number_format($total, 2, ',', ' ') ?> EUR</h3>
                        <small><?= count($expenses) ?> dépense(s)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-bg-warning h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 opacity-75">Ce mois
                            (<?= date('m/Y') ?>)</h6>
                        <h3 class="card-title mb-0">
                            <?= number_format($total_mois, 2, ',', ' ') ?> EUR
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div
                class="card-header d-flex justify-content-between align-items-center">
                <strong>Dernieres depenses</strong>
                <button class="btn btn-sm btn-primary" disabled>+ Ajouter (a
                    coder)</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Libelle</th>
                            <th>Categorie</th>
                            <th class="text-end">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $d): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($d['expense_date'])) ?>
                            </td>
                            <td><?= htmlspecialchars($d['title']) ?></td>
                            <td>
                                <?php if ($d['category_name']): ?>
                                <span class="badge"
                                    style="background-color: <?= htmlspecialchars($d['category_color']) ?>">
                                    <?= htmlspecialchars($d['category_name']) ?>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold">
                                <?= number_format($d['amount'], 2, ',', ' ') ?>
                                EUR</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><strong>Categories disponibles</strong>
            </div>
            <div class="card-body">
                <?php foreach ($categories as $cat): ?>
                <span class="badge me-2 mb-2 fs-6"
                    style="background-color: <?= htmlspecialchars($cat['color']) ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="alert alert-info">
            <strong>Prochaines etapes :</strong>
            <ol class="mb-0">
                <li>Creer <code>ajouter.php</code> avec un formulaire (libelle,
                    montant, categorie, date)</li>
                <li>Creer <code>supprimer.php</code> avec prepared statements
                    (securite !)</li>
                <li>Ajouter des graphiques par categorie (Chart.js via CDN)</li>
                <li>Filtres par mois / categorie</li>
            </ol>
        </div>

        <footer class="text-center text-muted py-4">
            <small>Projet local — Apache <?= apache_get_version() ?? '' ?> |
                Conteneurisé avec OrbStack</small>
        </footer>
    </div>

    <div class="container">
        <footer
            class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
            <div class="col-md-4 d-flex align-items-center"> <a href="/"
                    class="mb-3 me-2 mb-md-0 text-body-secondary text-decoration-none lh-1">
                    <img src="/assets/images/logo-gestio-2.png"
                        alt="logo Gestio" width="100"> </a> <span
                    class="mb-3 mb-md-0 text-body-secondary">©<?php echo date('Y'); ?>Codingqueen40</span>
            </div>
            <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
                <li class="ms-3"><a class="text-body-secondary" href="#"
                        aria-label="youtube"><i class="bi bi-youtube"></i></a>
                </li>
                <li class="ms-3"><a class="text-body-secondary" href="#"
                        aria-label="linkedin"><i class="bi bi-linkedin"></i></a>
                </li>
            </ul>
        </footer>
    </div>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
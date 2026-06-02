<?php
require 'config.php';

$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll();

$depenses = $pdo->query("
    SELECT d.*, c.nom AS categorie_nom, c.couleur AS categorie_couleur
    FROM depenses d
    LEFT JOIN categories c ON d.categorie_id = c.id
    ORDER BY d.date_depense DESC, d.id DESC
")->fetchAll();

$total = array_sum(array_column($depenses, 'montant'));
$total_mois = 0;
$mois_actuel = date('Y-m');
foreach ($depenses as $d) {
    if (substr($d['date_depense'], 0, 7) === $mois_actuel) {
        $total_mois += $d['montant'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionnaire de depenses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand mb-0 h1">Gestionnaire de depenses</span>
            <span class="text-light small">Codingqueen40</span>
        </div>
    </nav>

    <div class="container">

        <div class="alert alert-success">
            <strong>Stack OK</strong> — PHP <?= phpversion() ?> + MySQL via Docker. Tout est connecte.
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card text-bg-primary h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 opacity-75">Total general</h6>
                        <h3 class="card-title mb-0"><?= number_format($total, 2, ',', ' ') ?> EUR</h3>
                        <small><?= count($depenses) ?> depense(s)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-bg-warning h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 opacity-75">Ce mois (<?= date('m/Y') ?>)</h6>
                        <h3 class="card-title mb-0"><?= number_format($total_mois, 2, ',', ' ') ?> EUR</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Dernieres depenses</strong>
                <button class="btn btn-sm btn-primary" disabled>+ Ajouter (a coder)</button>
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
                        <?php foreach ($depenses as $d): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($d['date_depense'])) ?></td>
                            <td><?= htmlspecialchars($d['libelle']) ?></td>
                            <td>
                                <?php if ($d['categorie_nom']): ?>
                                    <span class="badge" style="background-color: <?= htmlspecialchars($d['categorie_couleur']) ?>">
                                        <?= htmlspecialchars($d['categorie_nom']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold"><?= number_format($d['montant'], 2, ',', ' ') ?> EUR</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><strong>Categories disponibles</strong></div>
            <div class="card-body">
                <?php foreach ($categories as $cat): ?>
                    <span class="badge me-2 mb-2 fs-6" style="background-color: <?= htmlspecialchars($cat['couleur']) ?>">
                        <?= htmlspecialchars($cat['nom']) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="alert alert-info">
            <strong>Prochaines etapes :</strong>
            <ol class="mb-0">
                <li>Creer <code>ajouter.php</code> avec un formulaire (libelle, montant, categorie, date)</li>
                <li>Creer <code>supprimer.php</code> avec prepared statements (securite !)</li>
                <li>Ajouter des graphiques par categorie (Chart.js via CDN)</li>
                <li>Filtres par mois / categorie</li>
            </ol>
        </div>

        <footer class="text-center text-muted py-4">
            <small>Projet local — Apache <?= apache_get_version() ?? '' ?> | Conteneurise avec OrbStack</small>
        </footer>
    </div>
</body>
</html>

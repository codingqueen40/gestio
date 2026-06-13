<?php
require __DIR__ . '/../src/config.php';

require_once __DIR__ . '/../src/templates/header.php';

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
<div class="container">
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
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <button type="button"
                    class="btn btn-primary btn-lg px-4 me-md-2">Primary</button>
                <button type="button"
                    class="btn btn-outline-secondary btn-lg px-4">Default</button>
            </div>
        </div>
    </div>
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
</div>

<?= require_once __DIR__ . '/../src/templates/footer.php' ?>
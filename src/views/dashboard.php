<?php require __DIR__ . '/../templates/header.php'; ?>

<div class="container pt-4">
    <h1 class="h2 fw-bold text-body-emphasis mb-1">
        Bonjour <?= htmlspecialchars(currentUsername()) ?> 👋</h1>
    <p class="lead mb-0">Voici un aperçu de tes dépenses.</p>
</div>

<div class="container">

    <?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success">Dépense ajoutée avec succès.</div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Dépense modifiée.</div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">Dépense supprimée.</div>
    <?php endif; ?>

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
                        <?= number_format($monthlyTotal, 2, ',', ' ') ?> EUR
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Couleur de barre selon l'état : dépassé (rouge), proche (jaune ≥ 80%), sinon vert.
    $barClass = static function (float $pct, bool $over): string {
        if ($over) {
            return 'bg-danger';
        }
        return $pct >= 80 ? 'bg-warning' : 'bg-success';
    };
    ?>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Budgets du mois (<?= date('m/Y') ?>)</strong>
            <a href="/budgets" class="btn btn-sm btn-outline-primary">Gérer les budgets</a>
        </div>
        <div class="card-body">
            <?php if (!$hasBudgets): ?>
            <p class="text-muted mb-0">
                Aucun budget défini. <a href="/budgets">Fixe tes plafonds mensuels</a> pour suivre ta progression.
            </p>
            <?php else: ?>

            <?php if ($globalBudget !== null): ?>
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-bold">Budget global</span>
                    <span class="<?= $globalOver ? 'text-danger fw-bold' : '' ?>">
                        <?= number_format($monthlyTotal, 2, ',', ' ') ?> /
                        <?= number_format($globalBudget, 2, ',', ' ') ?> EUR
                        <?php if ($globalOver): ?>⚠️ dépassé<?php endif; ?>
                    </span>
                </div>
                <div class="progress" role="progressbar"
                    aria-valuenow="<?= (int) round($globalPct) ?>" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar <?= $barClass($globalPct, $globalOver) ?>"
                        style="width: <?= min(100, round($globalPct, 1)) ?>%">
                        <?= (int) round($globalPct) ?>%
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php foreach ($budgetRows as $b): ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span>
                        <span class="badge" style="background-color: <?= htmlspecialchars($b['color']) ?>">
                            <?= htmlspecialchars($b['name']) ?>
                        </span>
                    </span>
                    <span class="<?= $b['over'] ? 'text-danger fw-bold' : '' ?>">
                        <?= number_format($b['spent'], 2, ',', ' ') ?> /
                        <?= number_format($b['budget'], 2, ',', ' ') ?> EUR
                        <?php if ($b['over']): ?>⚠️<?php endif; ?>
                    </span>
                </div>
                <div class="progress" role="progressbar"
                    aria-valuenow="<?= (int) round($b['pct']) ?>" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar <?= $barClass($b['pct'], $b['over']) ?>"
                        style="width: <?= min(100, round($b['pct'], 1)) ?>%">
                        <?= (int) round($b['pct']) ?>%
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <strong>Répartition par catégorie</strong>
            <?php if ($hasFilter): ?>
            <span class="text-muted small">(vue filtrée)</span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (count($byCategory) === 0): ?>
            <p class="text-muted mb-0">Aucune donnée à afficher.</p>
            <?php else: ?>
            <div style="max-width: 420px; margin: 0 auto;">
                <canvas id="categoryChart" height="300"></canvas>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>Évolution mensuelle</strong></div>
        <div class="card-body">
            <?php if (count($byMonth) === 0): ?>
            <p class="text-muted mb-0">Aucune donnée à afficher.</p>
            <?php else: ?>
            <canvas id="monthlyChart" height="120"></canvas>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Dernières dépenses</strong>
                <div class="d-flex gap-2">
                    <a href="/depenses/exporter" class="btn btn-sm btn-outline-secondary">Exporter CSV</a>
                    <a href="/depenses/importer" class="btn btn-sm btn-outline-secondary">Importer CSV</a>
                    <a href="/depenses/ajouter" class="btn btn-sm btn-primary">+ Ajouter</a>
                </div>
            </div>
            <form method="get" action="/" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label for="month" class="form-label small mb-1">Mois</label>
                    <select id="month" name="month"
                        class="form-select form-select-sm">
                        <option value="">Tous les mois</option>
                        <?php foreach ($months as $m): ?>
                        <option value="<?= htmlspecialchars($m) ?>"
                            <?= $m === $filterMonth ? 'selected' : '' ?>>
                            <?= htmlspecialchars(substr($m, 5, 2) . '/' . substr($m, 0, 4)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <label for="category"
                        class="form-label small mb-1">Catégorie</label>
                    <select id="category" name="category"
                        class="form-select form-select-sm">
                        <option value="">Toutes</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int) $cat['id_category'] ?>"
                            <?= (string) $cat['id_category'] === $filterCategory ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <label for="search" class="form-label small mb-1">Recherche</label>
                    <input type="text" id="search" name="search"
                        class="form-control form-control-sm"
                        placeholder="Libellé…"
                        value="<?= htmlspecialchars($filterSearch) ?>">
                </div>
                <div class="col-auto">
                    <button type="submit"
                        class="btn btn-sm btn-outline-primary">Filtrer</button>
                    <?php if ($hasFilter): ?>
                    <a href="/" class="btn btn-sm btn-link">Réinitialiser</a>
                    <?php endif; ?>
                </div>
                <?php if ($hasFilter): ?>
                <div class="col-12">
                    <small class="text-muted"><?= count($filtered) ?>
                        dépense(s) · Total filtré :
                        <?= number_format($filteredTotal, 2, ',', ' ') ?>
                        EUR</small>
                </div>
                <?php endif; ?>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Libelle</th>
                        <th>Categorie</th>
                        <th class="text-end">Montant</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($filtered) === 0): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <?php if ($hasFilter): ?>
                            Aucune dépense ne correspond à ce filtre.
                            <a href="/">Réinitialiser</a>.
                            <?php else: ?>
                            Aucune dépense pour l'instant.
                            <a href="/depenses/ajouter">Ajoute ta première
                                dépense</a>.
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($filtered as $d): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($d['expense_date'])) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($d['title']) ?>
                            <?php if (!empty($d['note'])): ?>
                            <br><small class="text-muted"><?= htmlspecialchars($d['note']) ?></small>
                            <?php endif; ?>
                        </td>
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
                        <td class="text-end">
                            <a href="/depenses/modifier?id=<?= (int) $d['id_expense'] ?>"
                                class="btn btn-sm btn-outline-secondary">Modifier</a>
                            <form action="/depenses/supprimer" method="post"
                                onsubmit="return confirm('Supprimer cette dépense ?');"
                                class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="id_expense"
                                    value="<?= (int) $d['id_expense'] ?>">
                                <button type="submit"
                                    class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </td>
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

</div>

<?php if (count($byCategory) > 0 || count($byMonth) > 0): ?>
<?php
$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
$catChartData = [
    'labels' => array_column($byCategory, 'name'),
    'data'   => array_map(static fn ($c) => round($c['total'], 2), $byCategory),
    'colors' => array_column($byCategory, 'color'),
];
$monthChartData = [
    'labels' => array_column($byMonth, 'label'),
    'data'   => array_column($byMonth, 'total'),
];
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    <?php if (count($byCategory) > 0): ?>
    const catChart = <?= json_encode($catChartData, $jsonFlags) ?>;
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: catChart.labels,
            datasets: [{ data: catChart.data, backgroundColor: catChart.colors }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: (c) => `${c.label}: ` +
                            c.parsed.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' EUR'
                    }
                }
            }
        }
    });
    <?php endif; ?>

    <?php if (count($byMonth) > 0): ?>
    const monthChart = <?= json_encode($monthChartData, $jsonFlags) ?>;
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthChart.labels,
            datasets: [{
                label: 'Dépenses (EUR)',
                data: monthChart.data,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.08)',
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (c) => c.parsed.y.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' EUR'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (v) => v.toLocaleString('fr-FR') + ' €'
                    }
                }
            }
        }
    });
    <?php endif; ?>
})();
</script>
<?php endif; ?>

<?php require __DIR__ . '/../templates/footer.php'; ?>

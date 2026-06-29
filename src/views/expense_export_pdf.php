<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de dépenses — Gestio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous">
    <style>
        body { background: #f0f2f5; }
        .report-wrap { max-width: 860px; margin: 0 auto; background: #fff; }

        @media print {
            .no-print { display: none !important; }
            body { background: #fff; font-size: 10pt; }
            .report-wrap { box-shadow: none !important; max-width: 100%; }
            @page { margin: 1.5cm; }
            table { font-size: 9pt; }
        }
    </style>
</head>
<body>

<div class="no-print py-2 bg-light border-bottom sticky-top">
    <div class="report-wrap px-4 d-flex justify-content-between align-items-center">
        <span class="text-muted small">Aperçu — dans votre navigateur : Imprimer → <em>Enregistrer en PDF</em></span>
        <div class="d-flex gap-2 py-1">
            <button onclick="window.print()" class="btn btn-sm btn-primary">
                🖨️ Imprimer / Enregistrer en PDF
            </button>
            <a href="/" class="btn btn-sm btn-outline-secondary">← Retour</a>
        </div>
    </div>
</div>

<div class="report-wrap shadow-sm p-4 p-md-5 my-4 mx-auto">

    <!-- En-tête rapport -->
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 pb-3 mb-4 border-bottom">
        <div>
            <h1 class="h3 fw-bold mb-1">Rapport de dépenses</h1>
            <p class="text-muted mb-0"><?= htmlspecialchars($username) ?></p>
        </div>
        <div class="text-end text-muted small">
            <div class="fw-bold">Gestio</div>
            <div>Exporté le <?= date('d/m/Y à H\hi') ?></div>
        </div>
    </div>

    <!-- Résumé -->
    <div class="row g-3 mb-5">
        <div class="col-4">
            <div class="border rounded p-3 text-center">
                <div class="h5 fw-bold mb-0"><?= number_format($total, 2, ',', ' ') ?> €</div>
                <div class="text-muted small">Total général</div>
            </div>
        </div>
        <div class="col-4">
            <div class="border rounded p-3 text-center">
                <div class="h5 fw-bold mb-0"><?= count($expenses) ?></div>
                <div class="text-muted small">Dépense<?= count($expenses) > 1 ? 's' : '' ?></div>
            </div>
        </div>
        <div class="col-4">
            <div class="border rounded p-3 text-center">
                <div class="h5 fw-bold mb-0"><?= count($byMonth) ?></div>
                <div class="text-muted small">Mois</div>
            </div>
        </div>
    </div>

    <!-- Tableau par mois -->
    <?php if (empty($expenses)): ?>
    <p class="text-center text-muted py-5">Aucune dépense à exporter.</p>
    <?php endif; ?>

    <?php foreach ($byMonth as $month => $rows): ?>
    <?php
        $monthLabel = strftime('%B %Y', mktime(0, 0, 0, (int) substr($month, 5, 2), 1, (int) substr($month, 0, 4)));
        $monthTotal = array_sum(array_column($rows, 'amount'));
    ?>
    <div class="mb-4">
        <h2 class="h6 fw-bold text-uppercase text-muted mb-2 d-flex justify-content-between">
            <span><?= htmlspecialchars(ucfirst($monthLabel)) ?></span>
            <span><?= number_format($monthTotal, 2, ',', ' ') ?> €</span>
        </h2>
        <table class="table table-sm table-bordered mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 90px;">Date</th>
                    <th>Libellé</th>
                    <th style="width: 130px;">Catégorie</th>
                    <th class="text-end" style="width: 100px;">Montant</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $d): ?>
            <tr>
                <td class="text-nowrap"><?= date('d/m/Y', strtotime($d['expense_date'])) ?></td>
                <td>
                    <?= htmlspecialchars($d['title']) ?>
                    <?php if (!empty($d['note'])): ?>
                    <br><small class="text-muted"><?= htmlspecialchars($d['note']) ?></small>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($d['category_name'] ?? '—') ?></td>
                <td class="text-end fw-bold text-nowrap">
                    <?= number_format((float) $d['amount'], 2, ',', ' ') ?> €
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>

    <!-- Pied de rapport -->
    <div class="mt-5 pt-3 border-top d-flex flex-wrap justify-content-between gap-2 text-muted small">
        <span>Gestio — Gestionnaire de dépenses personnelles</span>
        <span class="fw-bold text-body">
            Total général : <?= number_format($total, 2, ',', ' ') ?> €
        </span>
    </div>

    <!--
        Bloc de données structurées pour ré-import Gestio.
        Caché à l'écran (display:none), affiché lors de l'impression (d-print-block)
        afin que pdftotext puisse l'extraire du PDF généré par le navigateur.
    -->
    <div class="d-print-block mt-4 pt-3 border-top" style="display:none;">
        <p class="text-muted small mb-1">Données machine — ré-import Gestio</p>
        <pre class="text-muted" style="font-size:7pt; line-height:1.2; white-space:pre;">GESTIO-EXPORT:v1
<?php foreach ($expenses as $d): ?><?= $d['expense_date'] ?>|<?= str_replace(['|',"\n","\r"], [' ',' ',' '], $d['title']) ?>|<?= str_replace('|', ' ', $d['category_name'] ?? '') ?>|<?= number_format((float) $d['amount'], 2, '.', '') ?>|<?= str_replace(['|',"\n","\r"], [' ',' ',' '], $d['note'] ?? '') ?>
<?php endforeach; ?>END-GESTIO-EXPORT</pre>
    </div>

</div>
</body>
</html>

<?php require __DIR__ . '/../templates/header.php'; ?>

<div class="container">
    <h1 class="h3 mb-4">Importer des dépenses (CSV)</h1>

    <?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <?php if ($processed): ?>
    <div class="alert alert-<?= $imported > 0 ? 'success' : 'warning' ?>">
        <?= $imported ?> dépense(s) importée(s).
        <?php if (count($skipped) > 0): ?>
        <?= count($skipped) ?> ligne(s) ignorée(s).
        <?php endif; ?>
    </div>

    <?php if (count($skipped) > 0): ?>
    <div class="card mb-4">
        <div class="card-header text-bg-warning"><strong>Lignes ignorées</strong></div>
        <ul class="list-group list-group-flush">
            <?php foreach ($skipped as $msg): ?>
            <li class="list-group-item small"><?= htmlspecialchars($msg) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <a href="/" class="btn btn-primary">Retour au tableau de bord</a>
    <a href="/depenses/importer" class="btn btn-outline-secondary ms-2">Importer un autre fichier</a>

    <?php else: ?>

    <div class="card card-body mb-4" style="max-width: 560px;">
        <p class="mb-3">
            Le fichier CSV doit avoir les colonnes suivantes (avec en-tête) :<br>
            <code>Date,Libellé,Catégorie,Montant</code><br>
            <small class="text-muted">Date au format YYYY-MM-DD · Catégorie = nom exact de l'une de tes catégories · Montant avec point décimal.</small>
        </p>
        <a href="/depenses/exporter" class="btn btn-sm btn-outline-secondary mb-3">
            Télécharger mes dépenses actuelles (modèle)
        </a>

        <form action="/depenses/importer" method="post" enctype="multipart/form-data">
            <?= csrfField() ?>
            <div class="mb-3">
                <label for="csv_file" class="form-label">Fichier CSV <small class="text-muted">(max 1 Mo)</small></label>
                <input type="file" class="form-control" id="csv_file" name="csv_file"
                    accept=".csv,text/csv" required>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Importer</button>
                <a href="/" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>

    <?php endif; ?>
</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>

<?php require __DIR__ . '/../templates/header.php'; ?>

<div class="container">
    <h1 class="h3 mb-4">Importer des dépenses (PDF)</h1>

    <?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <?php if ($processed): ?>
    <div class="alert alert-<?= $imported > 0 ? 'success' : 'warning' ?>">
        <?= $imported ?> dépense<?= $imported > 1 ? 's' : '' ?> importée<?= $imported > 1 ? 's' : '' ?>.
        <?php if (count($skipped) > 0): ?>
        <?= count($skipped) ?> ligne<?= count($skipped) > 1 ? 's' : '' ?> ignorée<?= count($skipped) > 1 ? 's' : '' ?>.
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
    <a href="/depenses/importer-pdf" class="btn btn-outline-secondary ms-2">Importer un autre PDF</a>

    <?php else: ?>

    <div class="card card-body mb-4" style="max-width: 560px;">
        <p class="mb-2">
            Importez un PDF exporté depuis Gestio (<strong>Exporter PDF</strong> sur le tableau de bord).<br>
            <span class="text-muted small">Le PDF doit contenir le bloc de données Gestio généré à l'export.</span>
        </p>

        <div class="alert alert-info py-2 small mb-3">
            <strong>Comment faire :</strong><br>
            1. Cliquez sur <strong>Exporter PDF</strong> dans le tableau de bord.<br>
            2. Dans la page de rapport, utilisez <em>Imprimer → Enregistrer en PDF</em>.<br>
            3. Importez le fichier PDF obtenu ici.
        </div>

        <a href="/depenses/exporter-pdf" class="btn btn-sm btn-outline-secondary mb-3">
            Exporter mes dépenses en PDF (modèle)
        </a>

        <form action="/depenses/importer-pdf" method="post" enctype="multipart/form-data">
            <?= csrfField() ?>
            <div class="mb-3">
                <label for="pdf_file" class="form-label">Fichier PDF <small class="text-muted">(max 10 Mo)</small></label>
                <input type="file" class="form-control" id="pdf_file" name="pdf_file"
                    accept=".pdf,application/pdf" required>
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

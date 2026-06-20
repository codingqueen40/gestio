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

    <div class="card mb-4">
        <div
            class="card-header d-flex justify-content-between align-items-center">
            <strong>Dernieres depenses</strong>
            <a href="/depenses/ajouter" class="btn btn-sm btn-primary">+ Ajouter</a>
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
                    <?php if (count($expenses) === 0): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Aucune dépense pour l'instant.
                            <a href="/depenses/ajouter">Ajoute ta première
                                dépense</a>.
                        </td>
                    </tr>
                    <?php endif; ?>
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
                        <td class="text-end">
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

<?php require __DIR__ . '/../templates/footer.php'; ?>

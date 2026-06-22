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

    <div class="card mb-4">
        <div class="card-header">
            <div
                class="d-flex justify-content-between align-items-center mb-2">
                <strong>Dernieres depenses</strong>
                <a href="/depenses/ajouter" class="btn btn-sm btn-primary">+ Ajouter</a>
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

<?php require __DIR__ . '/../templates/footer.php'; ?>

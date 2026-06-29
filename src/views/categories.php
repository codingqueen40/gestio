<?php require __DIR__ . '/../templates/header.php'; ?>

<div class="container pt-4">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h1 class="h2 fw-bold text-body-emphasis mb-0">Mes catégories</h1>
        <a href="/categories/ajouter" class="btn btn-primary">+ Ajouter</a>
    </div>
    <p class="lead">Personnalise tes catégories de dépenses.</p>
</div>

<div class="container">

    <?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success">Catégorie ajoutée.</div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Catégorie modifiée.</div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">Catégorie supprimée.</div>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] === 'in_use'): ?>
    <div class="alert alert-danger">
        Impossible de supprimer cette catégorie : des dépenses lui sont rattachées.
        Supprime ou recatégorise ces dépenses d'abord.
    </div>
    <?php endif; ?>

    <?php if (empty($categories)): ?>
    <p class="text-muted">Aucune catégorie. <a href="/categories/ajouter">Ajoute ta première catégorie</a>.</p>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Couleur</th>
                    <th>Nom</th>
                    <th class="text-end">Dépenses</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td>
                        <span class="d-inline-block rounded"
                            style="width: 24px; height: 24px; background-color: <?= htmlspecialchars($cat['color']) ?>;">
                        </span>
                    </td>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td class="text-end"><?= (int) $cat['expense_count'] ?></td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/categories/modifier?id=<?= (int) $cat['id_category'] ?>"
                                class="btn btn-sm btn-outline-secondary">Modifier</a>

                            <?php if ($cat['expense_count'] > 0): ?>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                title="Supprime ou recatégorise les <?= (int) $cat['expense_count'] ?> dépense(s) d'abord"
                                disabled>Supprimer</button>
                            <?php else: ?>
                            <form action="/categories/supprimer" method="post" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="id_category" value="<?= (int) $cat['id_category'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>

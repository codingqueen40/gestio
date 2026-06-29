<?php require __DIR__ . '/../templates/header.php'; ?>

<div class="container">
    <h1 class="h3 mb-4">Dépenses récurrentes</h1>

    <?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success">Dépense récurrente ajoutée.</div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Dépense récurrente modifiée.</div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">Dépense récurrente supprimée.</div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Mes récurrences</strong>
            <a href="/recurrences/ajouter" class="btn btn-sm btn-primary">+ Ajouter</a>
        </div>

        <?php if (count($recurringExpenses) === 0): ?>
        <div class="card-body">
            <p class="text-muted mb-0">Aucune dépense récurrente définie.
                <a href="/recurrences/ajouter">Ajoute ta première récurrence</a>.
            </p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Libellé</th>
                        <th>Catégorie</th>
                        <th class="text-end">Montant</th>
                        <th class="text-center">Jour</th>
                        <th class="text-center">Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recurringExpenses as $r): ?>
                    <tr class="<?= $r['active'] ? '' : 'text-muted' ?>">
                        <td><?= htmlspecialchars($r['title']) ?></td>
                        <td>
                            <?php if ($r['category_name']): ?>
                            <span class="badge"
                                style="background-color: <?= htmlspecialchars($r['category_color']) ?>">
                                <?= htmlspecialchars($r['category_name']) ?>
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end fw-bold">
                            <?= number_format($r['amount'], 2, ',', ' ') ?> EUR
                        </td>
                        <td class="text-center">le <?= (int) $r['day_of_month'] ?></td>
                        <td class="text-center">
                            <?php if ($r['active']): ?>
                            <span class="badge bg-success">Active</span>
                            <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="/recurrences/modifier?id=<?= (int) $r['id_recurring'] ?>"
                                class="btn btn-sm btn-outline-secondary">Modifier</a>
                            <form action="/recurrences/supprimer" method="post"
                                onsubmit="return confirm('Supprimer cette récurrence ?');"
                                class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="id_recurring"
                                    value="<?= (int) $r['id_recurring'] ?>">
                                <button type="submit"
                                    class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <div class="alert alert-info">
        <strong>Comment ça fonctionne ?</strong>
        Les récurrences actives sont générées automatiquement chaque mois au jour indiqué,
        lors de ta prochaine visite sur le tableau de bord.
        Le jour est limité à 28 pour garantir la génération tous les mois (y compris février).
    </div>
</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>

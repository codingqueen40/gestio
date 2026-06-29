<?php require __DIR__ . '/../templates/header.php'; ?>

<div class="container">
    <h1 class="h3 mb-4">Ajouter une dépense récurrente</h1>

    <?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form action="/recurrences/ajouter" method="post" class="card card-body" style="max-width: 540px;">
        <?= csrfField() ?>

        <div class="mb-3">
            <label for="title" class="form-label">Libellé</label>
            <input type="text" class="form-control" id="title" name="title"
                value="<?= htmlspecialchars($old['title']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Montant (€)</label>
            <input type="number" step="0.01" min="0.01" class="form-control"
                id="amount" name="amount"
                value="<?= htmlspecialchars($old['amount']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Catégorie</label>
            <select class="form-select" id="category" name="category" required>
                <option value="">— Choisir —</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= (int) $cat['id_category'] ?>"
                    <?= (string) $cat['id_category'] === $old['category'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="day_of_month" class="form-label">
                Jour du mois <span class="text-muted small">(1–28)</span>
            </label>
            <input type="number" min="1" max="28" class="form-control"
                id="day_of_month" name="day_of_month"
                value="<?= htmlspecialchars($old['day_of_month']) ?>" required>
            <div class="form-text">La dépense sera générée chaque mois à ce jour.</div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="/recurrences" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>

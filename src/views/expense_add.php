<?php require __DIR__ . '/../templates/header.php'; ?>

<div class="container">
    <h1 class="h3 mb-4">Ajouter une dépense</h1>

    <?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form action="/depenses/ajouter" method="post" class="card card-body" style="max-width: 540px;">
        <?= csrfField() ?>

        <div class="mb-3">
            <label for="title" class="form-label">Libellé</label>
            <input type="text" class="form-control" id="title" name="title"
                value="<?= htmlspecialchars($old['title']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Montant (EUR)</label>
            <input type="number" step="0.01" min="0.01" class="form-control"
                id="amount" name="amount"
                value="<?= htmlspecialchars($old['amount']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" id="date" name="date"
                value="<?= htmlspecialchars($old['date']) ?>" required>
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

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="/" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>

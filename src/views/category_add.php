<?php require __DIR__ . '/../templates/header.php'; ?>

<div class="container">
    <h1 class="h3 mb-4">Ajouter une catégorie</h1>

    <?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form action="/categories/ajouter" method="post" class="card card-body" style="max-width: 480px;">
        <?= csrfField() ?>

        <div class="mb-3">
            <label for="name" class="form-label">Nom</label>
            <input type="text" class="form-control" id="name" name="name" maxlength="50"
                value="<?= htmlspecialchars($old['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="color" class="form-label">Couleur</label>
            <div class="d-flex gap-2 align-items-center">
                <input type="color" class="form-control form-control-color"
                    id="color" name="color"
                    value="<?= htmlspecialchars($old['color']) ?>" required>
                <span class="text-muted small">Choisir une couleur d'identification</span>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="/categories" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>

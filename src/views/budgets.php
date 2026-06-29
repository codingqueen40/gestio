<?php require __DIR__ . '/../templates/header.php'; ?>

<div class="container pt-4">
    <h1 class="h2 fw-bold text-body-emphasis mb-1">Mes budgets</h1>
    <p class="lead mb-0">Définis un plafond mensuel global et par catégorie.</p>
</div>

<div class="container">

    <?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success">Budgets enregistrés.</div>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form action="/budgets" method="post" class="card card-body" style="max-width: 540px;">
        <?= csrfField() ?>

        <div class="mb-4">
            <label for="global" class="form-label fw-bold">Budget global mensuel (€)</label>
            <input type="number" step="0.01" min="0" class="form-control"
                id="global" name="global" placeholder="Aucun plafond"
                value="<?= htmlspecialchars($old['global']) ?>">
            <div class="form-text">Laisse vide pour ne pas fixer de plafond global.</div>
        </div>

        <h2 class="h6 fw-bold">Par catégorie (€ / mois)</h2>
        <?php foreach ($categories as $cat): ?>
        <?php $id = (int) $cat['id_category']; ?>
        <div class="mb-3">
            <label for="cat_<?= $id ?>" class="form-label">
                <span class="badge me-1" style="background-color: <?= htmlspecialchars($cat['color']) ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </span>
            </label>
            <input type="number" step="0.01" min="0" class="form-control"
                id="cat_<?= $id ?>" name="cats[<?= $id ?>]" placeholder="Aucun plafond"
                value="<?= htmlspecialchars($old['cats'][$id] ?? '') ?>">
        </div>
        <?php endforeach; ?>

        <div class="d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/" class="btn btn-outline-secondary">Retour au tableau de bord</a>
        </div>
    </form>

</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>

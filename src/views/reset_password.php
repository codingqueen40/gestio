<?php require __DIR__ . '/../templates/header.php'; ?>

<div class="container" style="max-width: 480px; margin-top: 2rem;">
    <h1 class="h3 mb-4">Nouveau mot de passe</h1>

    <?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form action="/reinitialiser-mdp" method="post" class="card card-body">
        <?= csrfField() ?>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <div class="mb-3">
            <label for="password" class="form-label">Nouveau mot de passe</label>
            <input type="password" class="form-control" id="password" name="password"
                minlength="8" required autofocus>
            <div class="form-text">Au moins 8 caractères.</div>
        </div>

        <div class="mb-3">
            <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                minlength="8" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Réinitialiser</button>
    </form>
</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>

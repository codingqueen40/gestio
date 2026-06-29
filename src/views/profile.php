<?php require __DIR__ . '/../templates/header.php'; ?>

<div class="container pt-4">
    <h1 class="h2 fw-bold text-body-emphasis mb-1">Mon compte</h1>
    <p class="lead mb-0">Gère tes informations de connexion.</p>
</div>

<div class="container">

    <?php if (isset($_GET['profile_updated'])): ?>
    <div class="alert alert-success">Informations mises à jour.</div>
    <?php endif; ?>
    <?php if (isset($_GET['password_updated'])): ?>
    <div class="alert alert-success">Mot de passe mis à jour.</div>
    <?php endif; ?>

    <div class="card mb-4" style="max-width: 540px;">
        <div class="card-header"><strong>Informations du compte</strong></div>
        <div class="card-body">
            <?php foreach ($errors['profile'] as $error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>

            <form action="/profil" method="post">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="profile">

                <div class="mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <input type="text" class="form-control" id="username" name="username"
                        value="<?= htmlspecialchars($old['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?= htmlspecialchars($old['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="profile_current_password" class="form-label">Mot de passe actuel</label>
                    <input type="password" class="form-control" id="profile_current_password"
                        name="current_password" required>
                </div>

                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </form>
        </div>
    </div>

    <div class="card mb-4" style="max-width: 540px;">
        <div class="card-header"><strong>Changer mon mot de passe</strong></div>
        <div class="card-body">
            <?php foreach ($errors['password'] as $error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>

            <form action="/profil" method="post">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="password">

                <div class="mb-3">
                    <label for="password_current_password" class="form-label">Mot de passe actuel</label>
                    <input type="password" class="form-control" id="password_current_password"
                        name="current_password" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="new_password"
                        name="new_password" minlength="8" required>
                    <div class="form-text">Au moins 8 caractères.</div>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password" class="form-control" id="confirm_password"
                        name="confirm_password" minlength="8" required>
                </div>

                <button type="submit" class="btn btn-primary">Mettre à jour le mot de passe</button>
            </form>
        </div>
    </div>

    <div class="card mb-4 border-danger" style="max-width: 540px;">
        <div class="card-header text-bg-danger"><strong>Supprimer mon compte</strong></div>
        <div class="card-body">
            <p class="text-muted">
                Cette action est définitive : ton compte et toutes tes dépenses seront supprimés.
            </p>

            <?php foreach ($errors['delete'] as $error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>

            <form action="/profil" method="post"
                onsubmit="return confirm('Supprimer définitivement ton compte et toutes tes dépenses ?');">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="delete">

                <div class="mb-3">
                    <label for="delete_current_password" class="form-label">Mot de passe actuel</label>
                    <input type="password" class="form-control" id="delete_current_password"
                        name="current_password" required>
                </div>

                <button type="submit" class="btn btn-danger">Supprimer mon compte</button>
            </form>
        </div>
    </div>

</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>

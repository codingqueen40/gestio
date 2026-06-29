<?php require __DIR__ . '/../templates/header.php'; ?>

<div class="container" style="max-width: 480px; margin-top: 2rem;">
    <h1 class="h3 mb-4">Mot de passe oublié</h1>

    <?php if (isset($_GET['expired'])): ?>
    <div class="alert alert-warning">
        Ce lien de réinitialisation est invalide ou a expiré. Fais une nouvelle demande.
    </div>
    <?php endif; ?>

    <?php if ($sent): ?>
    <div class="alert alert-success">
        Si un compte est associé à cette adresse, un email de réinitialisation vient d'être envoyé.
        Vérifie ta boîte de réception (et les spams).
    </div>

    <?php if ($devResetUrl !== null): ?>
    <div class="alert alert-warning">
        <strong>Mode développement</strong> — lien de réinitialisation (aucun email envoyé) :
        <br><a href="<?= htmlspecialchars($devResetUrl) ?>"><?= htmlspecialchars($devResetUrl) ?></a>
    </div>
    <?php endif; ?>

    <p><a href="/login">← Retour à la connexion</a></p>

    <?php else: ?>

    <?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form action="/mot-de-passe-oublie" method="post" class="card card-body">
        <?= csrfField() ?>
        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" class="form-control" id="email" name="email"
                placeholder="ton@email.com" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary w-100">Envoyer le lien</button>
        <p class="mt-3 mb-0 text-center">
            <a href="/login">← Retour à la connexion</a>
        </p>
    </form>

    <?php endif; ?>
</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>

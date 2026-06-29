<?php require __DIR__ . '/../templates/header.php'; ?>

<?php if (isset($_GET['registered'])): ?>
<div class="alert alert-success">
    Compte créé avec succès. Tu peux maintenant te connecter.
</div>
<?php endif; ?>

<?php if (isset($_GET['account_deleted'])): ?>
<div class="alert alert-info">
    Ton compte a bien été supprimé.
</div>
<?php endif; ?>

<?php if (isset($_GET['password_reset'])): ?>
<div class="alert alert-success">
    Mot de passe réinitialisé. Tu peux maintenant te connecter.
</div>
<?php endif; ?>

<?php foreach ($errors as $error): ?>
<div class="alert alert-danger">
    <?= htmlspecialchars($error) ?>
</div>
<?php endforeach; ?>

<main class="form-signin w-100 m-auto">
    <form action="/login" method="post">
        <h1 class="h3 mb-3 fw-normal">Please log in</h1>
        <?= csrfField() ?>
        <div class="form-floating"> <input type="email" class="form-control"
                id="email" name="email" placeholder="name@example.com"
                value="<?= htmlspecialchars($old['email']) ?>"> <label
                for="email">Email address</label> </div>
        <div class="form-floating"> <input type="password" class="form-control"
                id="password" name="password" placeholder="Password"> <label
                for="password">Password</label> </div>
        <div class="form-check text-start my-3">
            <input class="form-check-input" type="checkbox" name="remember" value="1" id="checkRemember">
            <label class="form-check-label" for="checkRemember">Se souvenir de moi</label>
        </div>
        <button class="btn btn-primary w-100 py-2" type="submit">Log in</button>
        <p class="mt-3 mb-1 text-center">
            <a href="/mot-de-passe-oublie" class="text-muted small">Mot de passe oublié ?</a>
        </p>
        <p class="mb-0 text-center">
            Pas encore de compte ? <a href="/signup">Inscris-toi</a>
        </p>
    </form>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>

<?php require __DIR__ . '/../templates/header.php'; ?>

<?php foreach ($errors as $error): ?>
<div class="alert alert-danger">
    <?= htmlspecialchars($error) ?>
</div>
<?php endforeach; ?>

<main class="form-signin w-100 m-auto">
    <form action="/signup" method="post">
        <h1 class="h3 mb-3 fw-normal">Please sign up</h1>
        <?= csrfField() ?>
        <div class="form-floating"> <input type="text" class="form-control"
                id="username" name="username" placeholder="Username"
                value="<?= htmlspecialchars($old['username']) ?>"> <label
                for="username">Username</label> </div>
        <div class="form-floating"> <input type="email" class="form-control"
                id="email" name="email" placeholder="name@example.com"
                value="<?= htmlspecialchars($old['email']) ?>"> <label
                for="email">Email address</label> </div>
        <div class="form-floating"> <input type="password" class="form-control"
                id="password" name="password" placeholder="Password"> <label
                for="password">Password</label> </div>
        <div class="form-check text-start my-3"> <input class="form-check-input"
                type="checkbox" value="remember-me" id="checkDefault"> <label
                class="form-check-label" for="checkDefault">
                Remember me
            </label> </div> <button class="btn btn-primary w-100 py-2"
            type="submit">Sign up</button>
        <p class="mt-3 mb-0 text-center">
            Déjà inscrit ? <a href="/login">Connecte-toi</a>
        </p>
    </form>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>

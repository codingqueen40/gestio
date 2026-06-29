<?php require __DIR__ . '/../templates/header.php'; ?>

<div class="bg-dark text-secondary px-4 py-5 text-center">
    <div class="py-5">
        <h1 class="fw-bold text-white">Codingqueen40</h1>
        <div class="col-lg-6 mx-auto">
            <p class="fs-5 mb-4">Développeuse web passionnée, j'ai créé Gestio
                pour apprendre et
                mettre en pratique PHP, MySQL et Docker dans un projet concret.
            </p>
            <a href="#contact" class="btn btn-outline-info btn-lg px-4 fw-bold">
                Me contacter</a>
        </div>
    </div>
</div>

<div class="container px-4 py-5">
    <div class="row g-4 justify-content-center">
        <div class="col-md-4 text-center">
            <div class="fs-1 mb-2">🛠️</div>
            <h3 class="h5 fw-bold">Stack technique</h3>
            <p class="text-body-secondary">PHP 8.4 · MySQL 8.4 · Bootstrap 5 ·
                Docker</p>
        </div>
        <div class="col-md-4 text-center">
            <div class="fs-1 mb-2">🔐</div>
            <h3 class="h5 fw-bold">Sécurité</h3>
            <p class="text-body-secondary">Sessions durcies · CSRF · requêtes
                préparées PDO · scope
                utilisateur strict</p>
        </div>
        <div class="col-md-4 text-center">
            <div class="fs-1 mb-2">🚀</div>
            <h3 class="h5 fw-bold">Déploiement</h3>
            <p class="text-body-secondary">Oracle Cloud · Caddy (HTTPS auto) ·
                Docker
                Compose</p>
        </div>
    </div>
</div>

<div class="container px-4 pb-5" id="contact">
    <hr class="mb-5">
    <h2 class="text-center fw-bold mb-4">Contact</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body py-5">
                    <div class="fs-1 mb-3">✉️</div>
                    <h3 class="h5 fw-bold mb-3">Par e-mail</h3>
                    <p class="text-body-secondary mb-4">Une question, une
                        suggestion ou un bug à
                        signaler ? Écris-moi, je réponds avec plaisir.</p>
                    <a href="mailto:contact@codingqueen40.com?subject=Contact%20depuis%20Gestio"
                        class="btn btn-primary btn-lg w-100"
                        style="word-break: break-all;">
                        contact@codingqueen40.com</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>
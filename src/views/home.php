<?php require __DIR__ . '/../templates/header.php'; ?>

<div class="bg-dark text-secondary px-4 py-5 text-center">
    <div class="py-5">
        <img src="/assets/images/logo-gestio-2.png" alt="Gestio" width="120"
            class="mb-4">
        <h1 class="display-5 fw-bold text-white">
            Reprends le contrôle de tes dépenses</h1>
        <div class="col-lg-6 mx-auto">
            <p class="fs-5 mb-4">Avec Gestio, suis tes dépenses au quotidien,
                classe-les par catégorie et visualise où part ton argent —
                simplement, et en toute confidentialité.</p>
            <div
                class="d-grid gap-2 d-sm-flex justify-content-sm-center mb-3">
                <a href="/signup" class="btn btn-primary btn-lg px-4 me-sm-3 fw-bold">
                    Créer un compte</a>
                <a href="/login" class="btn btn-outline-light btn-lg px-4">
                    Se connecter</a>
            </div>
        </div>
    </div>
</div>

<div class="container px-4 py-5">
    <div class="row g-4">
        <div class="col-md-4 text-center">
            <div class="fs-1 mb-2">📝</div>
            <h3 class="h5 fw-bold">Enregistre en un clic</h3>
            <p class="text-body-secondary">Ajoute, modifie ou supprime une
                dépense en quelques secondes.</p>
        </div>
        <div class="col-md-4 text-center">
            <div class="fs-1 mb-2">🏷️</div>
            <h3 class="h5 fw-bold">Classe par catégorie</h3>
            <p class="text-body-secondary">Alimentation, transport, logement…
                filtre par mois et par catégorie.</p>
        </div>
        <div class="col-md-4 text-center">
            <div class="fs-1 mb-2">📊</div>
            <h3 class="h5 fw-bold">Visualise tes totaux</h3>
            <p class="text-body-secondary">Un tableau de bord clair avec
                graphique de répartition par catégorie.</p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>

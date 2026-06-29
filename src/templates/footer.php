</main>


<footer class="text-center text-muted py-4">
    <small>Projet local — Apache <?= apache_get_version() ?? '' ?> |
        Conteneurisé avec OrbStack</small>
</footer>



<div class="container">
    <footer
        class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
        <div class="col-md-4 d-flex align-items-center"> <a href="/"
                class="mb-3 me-2 mb-md-0 text-body-secondary text-decoration-none lh-1">
                <img src="/assets/images/logo-gestio-2.png" alt="logo Gestio"
                    width="100"> </a> <span
                class="mb-3 mb-md-0 text-body-secondary">©<?php echo date('Y'); ?>Codingqueen40</span>
        </div>
        <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
            <li class="ms-3"><a class="text-body-secondary" href="#"
                    aria-label="youtube"><i class="bi bi-youtube"></i></a>
            </li>
            <li class="ms-3"><a class="text-body-secondary" href="#"
                    aria-label="linkedin"><i class="bi bi-linkedin"></i></a>
            </li>
        </ul>
    </footer>
</div>
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>
<script>
(function () {
    var btn = document.getElementById('theme-toggle');
    if (!btn) return;
    btn.addEventListener('click', function () {
        var html = document.documentElement;
        var next = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-bs-theme', next);
        var icon = btn.querySelector('i');
        icon.className = next === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        btn.setAttribute('aria-label', next === 'dark' ? 'Passer en mode clair' : 'Passer en mode sombre');
        document.cookie = 'theme=' + next + '; path=/; max-age=' + (365 * 24 * 3600) + '; SameSite=Lax';
    });
})();
</script>
</body>

</html>
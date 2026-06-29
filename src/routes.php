<?php
/**
 * Table de routage : "MÉTHODE /chemin" => fichier contrôleur (dans src/controllers/).
 * Un contrôleur qui gère GET et POST est listé pour les deux méthodes.
 */
return [
    'GET /'                    => 'home.php',

    'GET /login'               => 'login.php',
    'POST /login'              => 'login.php',

    'GET /signup'              => 'signup.php',
    'POST /signup'             => 'signup.php',

    'POST /logout'             => 'logout.php',

    'GET /profil'              => 'profile.php',
    'POST /profil'             => 'profile.php',

    'GET /budgets'             => 'budgets.php',
    'POST /budgets'            => 'budgets.php',

    'GET /categories'            => 'categories.php',
    'GET /categories/ajouter'    => 'category_add.php',
    'POST /categories/ajouter'   => 'category_add.php',
    'GET /categories/modifier'   => 'category_edit.php',
    'POST /categories/modifier'  => 'category_edit.php',
    'POST /categories/supprimer' => 'category_delete.php',

    'GET /about'               => 'about.php',

    'GET /contact'             => 'contact.php',

    'GET /depenses/exporter'   => 'expense_export.php',
    'GET /depenses/importer'   => 'expense_import.php',
    'POST /depenses/importer'  => 'expense_import.php',

    'GET /depenses/ajouter'    => 'expense_add.php',
    'POST /depenses/ajouter'   => 'expense_add.php',

    'GET /depenses/modifier'   => 'expense_edit.php',
    'POST /depenses/modifier'  => 'expense_edit.php',

    'POST /depenses/supprimer' => 'expense_delete.php',
];

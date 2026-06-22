<?php
/**
 * Table de routage : "MÉTHODE /chemin" => fichier contrôleur (dans src/controllers/).
 * Un contrôleur qui gère GET et POST est listé pour les deux méthodes.
 */
return [
    'GET /'                    => 'dashboard.php',

    'GET /login'               => 'login.php',
    'POST /login'              => 'login.php',

    'GET /signup'              => 'signup.php',
    'POST /signup'             => 'signup.php',

    'POST /logout'             => 'logout.php',

    'GET /about'               => 'about.php',

    'GET /depenses/ajouter'    => 'expense_add.php',
    'POST /depenses/ajouter'   => 'expense_add.php',

    'POST /depenses/supprimer' => 'expense_delete.php',
];

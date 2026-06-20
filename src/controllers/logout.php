<?php
/** Déconnexion : détruit la session et renvoie vers le login. */
logoutUser();

header('Location: /login');
exit;

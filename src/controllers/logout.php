<?php
/** Déconnexion (POST only, protégée par CSRF) : détruit la session et renvoie vers le login. */
requireLogin();
csrfCheck();

clearRememberToken($pdo);
logoutUser();

header('Location: /login');
exit;

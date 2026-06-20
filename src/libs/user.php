<?php

function addUser(PDO $pdo, string $username, string $email, string $password):bool
{
    // On prépare la requête
    $query = $pdo->prepare("INSERT INTO user (username, email, password) VALUES (:username, :email, :password)");

    // On hash le mdp
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // On passe les données
    $query->bindValue(":username", $username, $pdo::PARAM_STR);
    $query->bindValue(":email", $email, $pdo::PARAM_STR);
    $query->bindValue(":password", $passwordHash, $pdo::PARAM_STR);

    // On exécute la requête.
    // Garde-fou : si la contrainte UNIQUE sur l'email saute malgré la
    // vérification préalable (course entre deux inscriptions), on renvoie false
    // plutôt que de laisser remonter une PDOException.
    try {
        return $query->execute();
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') { // violation de contrainte d'intégrité
            return false;
        }
        throw $e;
    }
}

/** Indique si un email est déjà utilisé par un compte. */
function emailExists(PDO $pdo, string $email):bool
{
    $query = $pdo->prepare("SELECT 1 FROM user WHERE email = :email LIMIT 1");
    $query->bindValue(":email", $email, $pdo::PARAM_STR);
    $query->execute();

    return (bool) $query->fetchColumn();
}

function verifyUserLogin(PDO $pdo, string $email, string $password):bool|array
{
    $query = $pdo->prepare("SELECT id_user, username, email, password FROM user WHERE email = :email");
    $query->bindValue(":email", $email, $pdo::PARAM_STR);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        return $user;
    } else {
        return false;
    }

}
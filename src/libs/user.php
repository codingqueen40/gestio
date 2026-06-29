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

/** Récupère un utilisateur complet par son id (ou null s'il n'existe pas). */
function getUserById(PDO $pdo, int $id):?array
{
    $query = $pdo->prepare("SELECT id_user, username, email, password FROM user WHERE id_user = :id");
    $query->bindValue(":id", $id, $pdo::PARAM_INT);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

/** Met à jour le nom d'utilisateur d'un compte. */
function updateUsername(PDO $pdo, int $id, string $username):bool
{
    $query = $pdo->prepare("UPDATE user SET username = :username WHERE id_user = :id");
    $query->bindValue(":username", $username, $pdo::PARAM_STR);
    $query->bindValue(":id", $id, $pdo::PARAM_INT);

    return $query->execute();
}

/**
 * Met à jour l'email d'un compte.
 * Renvoie false si l'email est déjà pris (violation de la contrainte UNIQUE).
 */
function updateUserEmail(PDO $pdo, int $id, string $email):bool
{
    $query = $pdo->prepare("UPDATE user SET email = :email WHERE id_user = :id");
    $query->bindValue(":email", $email, $pdo::PARAM_STR);
    $query->bindValue(":id", $id, $pdo::PARAM_INT);

    try {
        return $query->execute();
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') { // email déjà utilisé
            return false;
        }
        throw $e;
    }
}

/** Met à jour (et re-hash) le mot de passe d'un compte. */
function updateUserPassword(PDO $pdo, int $id, string $password):bool
{
    $query = $pdo->prepare("UPDATE user SET password = :password WHERE id_user = :id");
    $query->bindValue(":password", password_hash($password, PASSWORD_DEFAULT), $pdo::PARAM_STR);
    $query->bindValue(":id", $id, $pdo::PARAM_INT);

    return $query->execute();
}

/** Supprime un compte. Les dépenses liées partent en cascade (FK id_user ON DELETE CASCADE). */
function deleteUser(PDO $pdo, int $id):bool
{
    $query = $pdo->prepare("DELETE FROM user WHERE id_user = :id");
    $query->bindValue(":id", $id, $pdo::PARAM_INT);

    return $query->execute();
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
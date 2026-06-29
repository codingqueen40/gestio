<?php
/**
 * Logique métier des catégories. Chaque catégorie appartient à un utilisateur
 * (colonne category.id_user) : toutes les opérations sont filtrées par utilisateur.
 */

/** Catégories par défaut copiées pour chaque nouveau compte (cf. seedDefaultCategories). */
const DEFAULT_CATEGORIES = [
    ['Food', '#28a745'],
    ['Travel', '#007bff'],
    ['Housing', '#dc3545'],
    ['Hobbies', '#ffc107'],
    ['Health', '#17a2b8'],
    ['Education', '#6610f2'],
    ['Other', '#6c757d'],
];

/** Retourne les catégories d'un utilisateur, triées par nom. */
function getCategories(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare("SELECT * FROM category WHERE id_user = :uid ORDER BY name");
    $stmt->execute([':uid' => $userId]);
    return $stmt->fetchAll();
}

/** Liste des id_category d'un utilisateur (pour valider un formulaire). */
function getCategoryIds(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare("SELECT id_category FROM category WHERE id_user = :uid");
    $stmt->execute([':uid' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Retourne une catégorie précise appartenant à l'utilisateur, ou null.
 * Le filtre id_user empêche de lire la catégorie d'un autre compte.
 */
function getCategoryById(PDO $pdo, int $userId, int $categoryId): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM category WHERE id_category = :id AND id_user = :uid");
    $stmt->execute([':id' => $categoryId, ':uid' => $userId]);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

/** Ajoute une catégorie pour un utilisateur. */
function addCategory(PDO $pdo, int $userId, string $name, string $color): bool
{
    $stmt = $pdo->prepare("
        INSERT INTO category (name, color, id_user)
        VALUES (:name, :color, :uid)
    ");
    return $stmt->execute([':name' => $name, ':color' => $color, ':uid' => $userId]);
}

/**
 * Met à jour une catégorie appartenant à l'utilisateur.
 * Le filtre id_user empêche de modifier la catégorie d'un autre compte.
 */
function updateCategory(PDO $pdo, int $userId, int $categoryId, string $name, string $color): bool
{
    $stmt = $pdo->prepare("
        UPDATE category SET name = :name, color = :color
        WHERE id_category = :id AND id_user = :uid
    ");
    return $stmt->execute([
        ':name'  => $name,
        ':color' => $color,
        ':id'    => $categoryId,
        ':uid'   => $userId,
    ]);
}

/**
 * Supprime une catégorie appartenant à l'utilisateur.
 * Le filtre id_user empêche de supprimer la catégorie d'un autre compte.
 * Retourne true si une ligne a réellement été supprimée.
 */
function deleteCategory(PDO $pdo, int $userId, int $categoryId): bool
{
    $stmt = $pdo->prepare("DELETE FROM category WHERE id_category = :id AND id_user = :uid");
    $stmt->execute([':id' => $categoryId, ':uid' => $userId]);
    return $stmt->rowCount() > 0;
}

/** Nombre de dépenses de l'utilisateur rattachées à une catégorie (pour bloquer la suppression). */
function countExpensesInCategory(PDO $pdo, int $userId, int $categoryId): int
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM expense WHERE id_category = :id AND id_user = :uid");
    $stmt->execute([':id' => $categoryId, ':uid' => $userId]);
    return (int) $stmt->fetchColumn();
}

/** Crée le jeu de catégories par défaut pour un nouvel utilisateur. */
function seedDefaultCategories(PDO $pdo, int $userId): void
{
    foreach (DEFAULT_CATEGORIES as [$name, $color]) {
        addCategory($pdo, $userId, $name, $color);
    }
}

/** Valide une couleur au format hexadécimal #RRGGBB. */
function isValidHexColor(string $color): bool
{
    return (bool) preg_match('/^#[0-9a-fA-F]{6}$/', $color);
}

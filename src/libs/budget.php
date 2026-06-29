<?php
/**
 * Logique métier des budgets : un plafond mensuel récurrent par catégorie
 * (table `budget`) et un plafond mensuel global (colonne `user.monthly_budget`).
 * Toutes les opérations sont filtrées par utilisateur.
 */

/** Plafonds par catégorie d'un utilisateur, sous forme [id_category => (float) amount]. */
function getCategoryBudgets(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare("SELECT id_category, amount FROM budget WHERE id_user = :uid");
    $stmt->execute([':uid' => $userId]);

    $budgets = [];
    foreach ($stmt->fetchAll() as $row) {
        $budgets[(int) $row['id_category']] = (float) $row['amount'];
    }
    return $budgets;
}

/** Définit (ou met à jour) le plafond d'une catégorie pour un utilisateur. */
function setCategoryBudget(PDO $pdo, int $userId, int $categoryId, float $amount): void
{
    $stmt = $pdo->prepare("
        INSERT INTO budget (id_user, id_category, amount)
        VALUES (:uid, :cat, :amount)
        ON DUPLICATE KEY UPDATE amount = VALUES(amount)
    ");
    $stmt->execute([':uid' => $userId, ':cat' => $categoryId, ':amount' => $amount]);
}

/** Retire le plafond d'une catégorie (budget « non défini »). */
function deleteCategoryBudget(PDO $pdo, int $userId, int $categoryId): void
{
    $stmt = $pdo->prepare("DELETE FROM budget WHERE id_user = :uid AND id_category = :cat");
    $stmt->execute([':uid' => $userId, ':cat' => $categoryId]);
}

/** Plafond global mensuel d'un utilisateur, ou null s'il n'est pas défini. */
function getGlobalBudget(PDO $pdo, int $userId): ?float
{
    $stmt = $pdo->prepare("SELECT monthly_budget FROM user WHERE id_user = :uid");
    $stmt->execute([':uid' => $userId]);
    $value = $stmt->fetchColumn();

    return $value === null || $value === false ? null : (float) $value;
}

/** Définit le plafond global mensuel (null pour le retirer). */
function setGlobalBudget(PDO $pdo, int $userId, ?float $amount): void
{
    $stmt = $pdo->prepare("UPDATE user SET monthly_budget = :amount WHERE id_user = :uid");
    $stmt->bindValue(':amount', $amount, $amount === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
    $stmt->execute();
}

/**
 * État de progression budgétaire par catégorie, pour une liste de dépenses
 * déjà restreinte à la période voulue (ex. le mois courant).
 *
 * $categories : lignes `category` (id_category, name, color).
 * $budgets    : [id_category => amount] (cf. getCategoryBudgets).
 * Retourne, pour chaque catégorie ayant un budget, une ligne
 * [id_category, name, color, spent, budget, pct, over], triée par % consommé décroissant.
 */
function budgetProgress(array $categories, array $budgets, array $expenses): array
{
    // Dépensé par catégorie sur la liste fournie.
    $spent = [];
    foreach ($expenses as $d) {
        $id = (int) $d['id_category'];
        $spent[$id] = ($spent[$id] ?? 0.0) + (float) $d['amount'];
    }

    $rows = [];
    foreach ($categories as $cat) {
        $id = (int) $cat['id_category'];
        if (!isset($budgets[$id])) {
            continue; // pas de plafond défini pour cette catégorie
        }
        $budget = (float) $budgets[$id];
        $used   = $spent[$id] ?? 0.0;
        $rows[] = [
            'id_category' => $id,
            'name'        => $cat['name'],
            'color'       => $cat['color'],
            'spent'       => $used,
            'budget'      => $budget,
            'pct'         => $budget > 0 ? ($used / $budget) * 100 : 0.0,
            'over'        => $used > $budget,
        ];
    }

    usort($rows, static fn ($a, $b) => $b['pct'] <=> $a['pct']);
    return $rows;
}

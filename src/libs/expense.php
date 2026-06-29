<?php
/**
 * Logique métier des dépenses : lecture, ajout, suppression.
 * Toutes les opérations sont systématiquement filtrées par utilisateur.
 */

/** Retourne les dépenses d'un utilisateur (avec nom/couleur de catégorie). */
function getExpensesByUser(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare("
        SELECT d.*, c.name AS category_name, c.color AS category_color
        FROM expense d
        LEFT JOIN category c ON d.id_category = c.id_category
        WHERE d.id_user = :uid
        ORDER BY d.expense_date DESC, d.id_expense DESC
    ");
    $stmt->execute([':uid' => $userId]);
    return $stmt->fetchAll();
}

/**
 * Retourne une dépense précise appartenant à l'utilisateur, ou null.
 * Le filtre id_user empêche de lire la dépense d'un autre compte.
 */
function getExpenseById(PDO $pdo, int $userId, int $expenseId): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM expense WHERE id_expense = :id AND id_user = :uid");
    $stmt->execute([':id' => $expenseId, ':uid' => $userId]);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

/** Ajoute une dépense pour un utilisateur. */
function addExpense(PDO $pdo, int $userId, string $title, float $amount, string $date, int $categoryId): bool
{
    $stmt = $pdo->prepare("
        INSERT INTO expense (amount, title, expense_date, id_category, id_user)
        VALUES (:amount, :title, :date, :category, :uid)
    ");
    return $stmt->execute([
        ':amount'   => $amount,
        ':title'    => $title,
        ':date'     => $date,
        ':category' => $categoryId,
        ':uid'      => $userId,
    ]);
}

/**
 * Met à jour une dépense appartenant à l'utilisateur.
 * Le filtre id_user empêche de modifier la dépense d'un autre compte.
 * Retourne true si une ligne a réellement été modifiée.
 */
function updateExpense(PDO $pdo, int $userId, int $expenseId, string $title, float $amount, string $date, int $categoryId): bool
{
    $stmt = $pdo->prepare("
        UPDATE expense
        SET amount = :amount, title = :title, expense_date = :date, id_category = :category
        WHERE id_expense = :id AND id_user = :uid
    ");
    $stmt->execute([
        ':amount'   => $amount,
        ':title'    => $title,
        ':date'     => $date,
        ':category' => $categoryId,
        ':id'       => $expenseId,
        ':uid'      => $userId,
    ]);
    return $stmt->rowCount() > 0;
}

/**
 * Supprime une dépense appartenant à l'utilisateur.
 * Le filtre id_user empêche de supprimer la dépense d'un autre compte.
 * Retourne true si une ligne a réellement été supprimée.
 */
function deleteExpense(PDO $pdo, int $userId, int $expenseId): bool
{
    $stmt = $pdo->prepare("DELETE FROM expense WHERE id_expense = :id AND id_user = :uid");
    $stmt->execute([':id' => $expenseId, ':uid' => $userId]);
    return $stmt->rowCount() > 0;
}

/**
 * Liste des mois (format 'Y-m') présents dans les dépenses, du plus récent au plus ancien.
 * Sert à peupler le menu déroulant du filtre par mois.
 */
function getExpenseMonths(array $expenses): array
{
    $months = [];
    foreach ($expenses as $d) {
        $months[substr($d['expense_date'], 0, 7)] = true;
    }
    $months = array_keys($months);
    rsort($months);
    return $months;
}

/**
 * Filtre une liste de dépenses par mois ('Y-m') et/ou catégorie (id_category).
 * Un argument à null = pas de filtre sur ce critère.
 */
function filterExpenses(array $expenses, ?string $month = null, ?int $categoryId = null): array
{
    return array_values(array_filter($expenses, static function ($d) use ($month, $categoryId) {
        if ($month !== null && substr($d['expense_date'], 0, 7) !== $month) {
            return false;
        }
        if ($categoryId !== null && (int) $d['id_category'] !== $categoryId) {
            return false;
        }
        return true;
    }));
}

/**
 * Totaux par catégorie pour une liste de dépenses.
 * Retourne [['name' => , 'color' => , 'total' => ], ...] trié par total décroissant,
 * uniquement pour les catégories réellement présentes. Pratique pour un graphique.
 */
function sumByCategory(array $expenses): array
{
    $byCat = [];
    foreach ($expenses as $d) {
        $id = (int) $d['id_category'];
        if (!isset($byCat[$id])) {
            $byCat[$id] = [
                'name'  => $d['category_name'] ?? 'Sans catégorie',
                'color' => $d['category_color'] ?? '#6c757d',
                'total' => 0.0,
            ];
        }
        $byCat[$id]['total'] += (float) $d['amount'];
    }
    usort($byCat, static fn ($a, $b) => $b['total'] <=> $a['total']);
    return $byCat;
}

/**
 * Totaux mensuels pour un line chart d'évolution.
 * Retourne [['month'=>'Y-m','label'=>'MM/YYYY','total'=>float], ...] trié chronologiquement.
 * Opère sur toutes les dépenses (pas de filtre) pour montrer la tendance complète.
 */
function sumByMonth(array $expenses): array
{
    $months = [];
    foreach ($expenses as $d) {
        $ym = substr($d['expense_date'], 0, 7);
        $months[$ym] = ($months[$ym] ?? 0.0) + (float) $d['amount'];
    }
    ksort($months);
    $result = [];
    foreach ($months as $ym => $total) {
        $result[] = [
            'month' => $ym,
            'label' => substr($ym, 5, 2) . '/' . substr($ym, 0, 4),
            'total' => round($total, 2),
        ];
    }
    return $result;
}

/** Somme totale d'une liste de dépenses. */
function sumExpenses(array $expenses): float
{
    return (float) array_sum(array_column($expenses, 'amount'));
}

/** Somme des dépenses d'un mois donné (format 'Y-m'). */
function sumExpensesForMonth(array $expenses, string $yearMonth): float
{
    $total = 0.0;
    foreach ($expenses as $d) {
        if (substr($d['expense_date'], 0, 7) === $yearMonth) {
            $total += (float) $d['amount'];
        }
    }
    return $total;
}

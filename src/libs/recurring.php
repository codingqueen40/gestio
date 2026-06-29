<?php
/**
 * Logique métier des dépenses récurrentes.
 * Toutes les opérations sont filtrées par id_user.
 */

function getRecurringExpenses(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare("
        SELECT r.*, c.name AS category_name, c.color AS category_color
        FROM recurring_expense r
        LEFT JOIN category c ON r.id_category = c.id_category
        WHERE r.id_user = :uid
        ORDER BY r.title ASC
    ");
    $stmt->execute([':uid' => $userId]);
    return $stmt->fetchAll();
}

function getRecurringExpenseById(PDO $pdo, int $userId, int $id): ?array
{
    $stmt = $pdo->prepare("
        SELECT * FROM recurring_expense WHERE id_recurring = :id AND id_user = :uid
    ");
    $stmt->execute([':id' => $id, ':uid' => $userId]);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

function addRecurringExpense(PDO $pdo, int $userId, string $title, float $amount, int $categoryId, int $dayOfMonth): bool
{
    $stmt = $pdo->prepare("
        INSERT INTO recurring_expense (id_user, title, amount, id_category, day_of_month)
        VALUES (:uid, :title, :amount, :category, :day)
    ");
    return $stmt->execute([
        ':uid'      => $userId,
        ':title'    => $title,
        ':amount'   => $amount,
        ':category' => $categoryId,
        ':day'      => $dayOfMonth,
    ]);
}

function updateRecurringExpense(PDO $pdo, int $userId, int $id, string $title, float $amount, int $categoryId, int $dayOfMonth, bool $active): bool
{
    $stmt = $pdo->prepare("
        UPDATE recurring_expense
        SET title = :title, amount = :amount, id_category = :category,
            day_of_month = :day, active = :active
        WHERE id_recurring = :id AND id_user = :uid
    ");
    $stmt->execute([
        ':title'    => $title,
        ':amount'   => $amount,
        ':category' => $categoryId,
        ':day'      => $dayOfMonth,
        ':active'   => $active ? 1 : 0,
        ':id'       => $id,
        ':uid'      => $userId,
    ]);
    return $stmt->rowCount() > 0;
}

function deleteRecurringExpense(PDO $pdo, int $userId, int $id): bool
{
    $stmt = $pdo->prepare("
        DELETE FROM recurring_expense WHERE id_recurring = :id AND id_user = :uid
    ");
    $stmt->execute([':id' => $id, ':uid' => $userId]);
    return $stmt->rowCount() > 0;
}

/**
 * Génère automatiquement les dépenses récurrentes actives pour le mois courant.
 * Appelé au chargement du dashboard. Idempotent : la table recurring_expense_log
 * empêche toute double-génération pour un même mois.
 * Retourne le nombre de dépenses nouvellement créées.
 */
function generateRecurringExpenses(PDO $pdo, int $userId): int
{
    $today       = (int) date('j');
    $daysInMonth = (int) date('t');
    $yearMonth   = date('Y-m');
    $created     = 0;

    // Récupère les récurrences actives dont le jour est arrivé et pas encore loggées ce mois.
    $stmt = $pdo->prepare("
        SELECT r.*
        FROM recurring_expense r
        WHERE r.id_user = :uid
          AND r.active = 1
          AND LEAST(r.day_of_month, :dim) <= :today
          AND NOT EXISTS (
              SELECT 1 FROM recurring_expense_log l
              WHERE l.id_recurring = r.id_recurring AND l.`year_month` = :ym
          )
    ");
    $stmt->execute([
        ':uid'   => $userId,
        ':dim'   => $daysInMonth,
        ':today' => $today,
        ':ym'    => $yearMonth,
    ]);
    $due = $stmt->fetchAll();

    foreach ($due as $r) {
        // Ajuste le jour si le mois est trop court (ex. jour 31 en février → 28).
        $day         = min((int) $r['day_of_month'], $daysInMonth);
        $expenseDate = date('Y-m-') . str_pad((string) $day, 2, '0', STR_PAD_LEFT);

        $ins = $pdo->prepare("
            INSERT INTO expense (amount, title, expense_date, id_category, id_user)
            VALUES (:amount, :title, :date, :category, :uid)
        ");
        $ins->execute([
            ':amount'   => $r['amount'],
            ':title'    => $r['title'],
            ':date'     => $expenseDate,
            ':category' => $r['id_category'],
            ':uid'      => $userId,
        ]);

        $log = $pdo->prepare("
            INSERT INTO recurring_expense_log (id_recurring, `year_month`) VALUES (:id, :ym)
        ");
        $log->execute([':id' => $r['id_recurring'], ':ym' => $yearMonth]);
        $created++;
    }

    return $created;
}

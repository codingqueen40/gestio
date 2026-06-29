<?php
/**
 * Gestion des budgets : plafond global mensuel + un plafond par catégorie.
 * Un seul formulaire enregistre l'ensemble (champ vide = pas de plafond).
 */
requireLogin();

$userId     = currentUserId();
$categories = getCategories($pdo);

$errors = [];
// Valeurs du formulaire : on part de l'existant, écrasé par le POST en cas de ré-affichage.
$old = [
    'global' => ($g = getGlobalBudget($pdo, $userId)) !== null ? (string) $g : '',
    'cats'   => [],
];
$existing = getCategoryBudgets($pdo, $userId);
foreach ($categories as $cat) {
    $id = (int) $cat['id_category'];
    $old['cats'][$id] = isset($existing[$id]) ? (string) $existing[$id] : '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();

    // Plafond global.
    $old['global'] = trim($_POST['global'] ?? '');
    $globalAmount  = null;
    if ($old['global'] !== '') {
        if (!is_numeric($old['global']) || (float) $old['global'] <= 0) {
            $errors[] = "Le budget global doit être un nombre supérieur à 0";
        } else {
            $globalAmount = (float) $old['global'];
        }
    }

    // Plafonds par catégorie : [id_category => montant à appliquer (ou null = retirer)].
    $catAmounts = [];
    $postedCats = $_POST['cats'] ?? [];
    foreach ($categories as $cat) {
        $id    = (int) $cat['id_category'];
        $value = trim((string) ($postedCats[$id] ?? ''));
        $old['cats'][$id] = $value;

        if ($value === '') {
            $catAmounts[$id] = null; // pas de plafond
        } elseif (!is_numeric($value) || (float) $value <= 0) {
            $errors[] = "Le budget de « " . $cat['name'] . " » doit être un nombre supérieur à 0";
        } else {
            $catAmounts[$id] = (float) $value;
        }
    }

    if (count($errors) === 0) {
        setGlobalBudget($pdo, $userId, $globalAmount);
        foreach ($catAmounts as $id => $amount) {
            if ($amount === null) {
                deleteCategoryBudget($pdo, $userId, $id);
            } else {
                setCategoryBudget($pdo, $userId, $id, $amount);
            }
        }

        header('Location: /budgets?saved=1');
        exit;
    }
}

require __DIR__ . '/../views/budgets.php';

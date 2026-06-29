<?php
/** Import CSV de dépenses (GET : formulaire, POST : traitement). */
requireLogin();

$userId    = currentUserId();
$imported  = 0;
$skipped   = [];
$errors    = [];
$processed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();

    $file = $_FILES['csv_file'] ?? null;

    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Aucun fichier reçu ou erreur lors de l'upload.";
    } elseif ($file['size'] > 1_048_576) {
        $errors[] = "Le fichier ne doit pas dépasser 1 Mo.";
    } elseif (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'csv') {
        $errors[] = "Le fichier doit avoir l'extension .csv.";
    } else {
        // Construire la map nom → id_category (insensible à la casse).
        $categories = getCategories($pdo, $userId);
        $catMap = [];
        foreach ($categories as $cat) {
            $catMap[mb_strtolower($cat['name'])] = (int) $cat['id_category'];
        }

        $handle = fopen($file['tmp_name'], 'r');

        // Supprimer le BOM UTF-8 si présent.
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $lineNum       = 0;
        $headerSkipped = false;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $lineNum++;

            if (!$headerSkipped) {
                $headerSkipped = true;
                continue; // ignorer la ligne d'en-tête
            }

            if (count($row) < 4) {
                $skipped[] = "Ligne $lineNum : format invalide (moins de 4 colonnes).";
                continue;
            }

            [$date, $title, $catName, $amount] = array_map('trim', array_slice($row, 0, 4));

            // Valider la date.
            $dt = DateTime::createFromFormat('Y-m-d', $date);
            if (!$dt || $dt->format('Y-m-d') !== $date) {
                $skipped[] = "Ligne $lineNum : date invalide « $date ».";
                continue;
            }

            // Valider le libellé.
            if ($title === '') {
                $skipped[] = "Ligne $lineNum : libellé vide.";
                continue;
            }

            // Valider le montant (accepter virgule ou point décimal).
            $amount = str_replace(',', '.', $amount);
            if (!is_numeric($amount) || (float) $amount <= 0) {
                $skipped[] = "Ligne $lineNum : montant invalide « $amount ».";
                continue;
            }

            // Résoudre la catégorie.
            $catId = $catMap[mb_strtolower($catName)] ?? null;
            if ($catId === null) {
                $skipped[] = "Ligne $lineNum : catégorie inconnue « $catName ».";
                continue;
            }

            addExpense($pdo, $userId, $title, (float) $amount, $date, $catId);
            $imported++;
        }

        fclose($handle);
        $processed = true;
    }
}

require __DIR__ . '/../views/expense_import.php';

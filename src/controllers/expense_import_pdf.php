<?php
/** Import PDF de dépenses (GET : formulaire, POST : traitement). */
requireLogin();

$userId    = currentUserId();
$imported  = 0;
$skipped   = [];
$errors    = [];
$processed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();

    $file = $_FILES['pdf_file'] ?? null;

    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Aucun fichier reçu ou erreur lors de l'upload.";
    } elseif ($file['size'] > 10_485_760) {
        $errors[] = "Le fichier ne doit pas dépasser 10 Mo.";
    } elseif (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'pdf') {
        $errors[] = "Le fichier doit avoir l'extension .pdf.";
    } else {
        $tmpPath = $file['tmp_name'];

        // Extraire le texte du PDF via pdftotext.
        $pdftext = shell_exec('pdftotext -layout ' . escapeshellarg($tmpPath) . ' -');

        if ($pdftext === null || $pdftext === '') {
            $errors[] = "Impossible d'extraire le texte du PDF. Assurez-vous d'utiliser un PDF exporté depuis Gestio.";
        } else {
            // Localiser le bloc GESTIO-EXPORT.
            if (!preg_match('/GESTIO-EXPORT:v1\r?\n(.*?)END-GESTIO-EXPORT/s', $pdftext, $matches)) {
                $errors[] = "Bloc de données Gestio introuvable dans ce PDF. Importez uniquement les PDFs générés par Gestio.";
            } else {
                $categories = getCategories($pdo, $userId);
                $catMap = [];
                foreach ($categories as $cat) {
                    $catMap[mb_strtolower(trim($cat['name']))] = (int) $cat['id_category'];
                }

                $lines = preg_split('/\r?\n/', trim($matches[1]));
                $lineNum = 0;

                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    $lineNum++;

                    $parts = explode('|', $line, 5);
                    if (count($parts) < 4) {
                        $skipped[] = "Ligne $lineNum : format invalide.";
                        continue;
                    }

                    [$date, $title, $catName, $amount, $note] = array_pad(array_map('trim', $parts), 5, '');

                    // Valider la date (YYYY-MM-DD).
                    $dt = DateTime::createFromFormat('Y-m-d', $date);
                    if (!$dt || $dt->format('Y-m-d') !== $date) {
                        $skipped[] = "Ligne $lineNum : date invalide « $date ».";
                        continue;
                    }

                    if ($title === '') {
                        $skipped[] = "Ligne $lineNum : libellé vide.";
                        continue;
                    }

                    if (!is_numeric($amount) || (float) $amount <= 0) {
                        $skipped[] = "Ligne $lineNum : montant invalide « $amount ».";
                        continue;
                    }

                    $catId = $catMap[mb_strtolower($catName)] ?? null;
                    if ($catId === null) {
                        $skipped[] = "Ligne $lineNum : catégorie inconnue « $catName ».";
                        continue;
                    }

                    addExpense($pdo, $userId, $title, (float) $amount, $date, $catId, $note);
                    $imported++;
                }

                $processed = true;
            }
        }
    }
}

require __DIR__ . '/../views/expense_import_pdf.php';

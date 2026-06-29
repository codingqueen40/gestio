<?php
/**
 * Gestion de compte : changer l'email, changer le mot de passe, supprimer le compte.
 * Chaque action sensible exige la re-saisie du mot de passe actuel (+ CSRF).
 */
requireLogin();

$userId = currentUserId();
$user   = getUserById($pdo, $userId);

// Garde-fou : session pointant sur un compte disparu (ex. supprimé ailleurs) → on déconnecte.
if ($user === null) {
    logoutUser();
    header('Location: /login');
    exit;
}

// Erreurs cloisonnées par formulaire pour les afficher au bon endroit.
$errors = ['profile' => [], 'password' => [], 'delete' => []];
$old    = ['username' => $user['username'], 'email' => $user['email']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();

    $action          = $_POST['action'] ?? '';
    $currentPassword = $_POST['current_password'] ?? '';

    if ($action === 'profile') {
        $newUsername     = trim($_POST['username'] ?? '');
        $newEmail        = trim($_POST['email'] ?? '');
        $old['username'] = $newUsername;
        $old['email']    = $newEmail;

        if (!password_verify($currentPassword, $user['password'])) {
            $errors['profile'][] = "Mot de passe actuel incorrect";
        }
        if ($newUsername === '') {
            $errors['profile'][] = "Le nom d'utilisateur est obligatoire";
        }
        if ($newEmail === '') {
            $errors['profile'][] = "L'email est obligatoire";
        } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['profile'][] = "L'email n'est pas valide";
        } elseif ($newEmail !== $user['email'] && emailExists($pdo, $newEmail)) {
            $errors['profile'][] = "Un compte existe déjà avec cet email";
        }

        if (count($errors['profile']) === 0) {
            // Email inchangé : no-op silencieux ; sinon on tente la mise à jour.
            if ($newEmail === $user['email'] || updateUserEmail($pdo, $userId, $newEmail)) {
                if ($newUsername !== $user['username']) {
                    updateUsername($pdo, $userId, $newUsername);
                    $_SESSION['username'] = $newUsername; // header « Bonjour … » à jour
                }
                header('Location: /profil?profile_updated=1');
                exit;
            }
            $errors['profile'][] = "Un compte existe déjà avec cet email";
        }
    } elseif ($action === 'password') {
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPassword, $user['password'])) {
            $errors['password'][] = "Mot de passe actuel incorrect";
        }
        if (strlen($newPassword) < 8) {
            $errors['password'][] = "Le nouveau mot de passe doit contenir au moins 8 caractères";
        } elseif ($newPassword !== $confirmPassword) {
            $errors['password'][] = "La confirmation ne correspond pas au nouveau mot de passe";
        }

        if (count($errors['password']) === 0) {
            updateUserPassword($pdo, $userId, $newPassword);
            header('Location: /profil?password_updated=1');
            exit;
        }
    } elseif ($action === 'delete') {
        if (!password_verify($currentPassword, $user['password'])) {
            $errors['delete'][] = "Mot de passe actuel incorrect";
        }

        if (count($errors['delete']) === 0) {
            deleteUser($pdo, $userId); // dépenses supprimées en cascade
            logoutUser();
            header('Location: /login?account_deleted=1');
            exit;
        }
    }
}

require __DIR__ . '/../views/profile.php';

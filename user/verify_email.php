<?php
require_once("../config/database.php");
session_start();

function fetchUserByEmailAndActivationCode($pdo, $email, $activation_code) {
    $sql = 'SELECT id, new_email FROM users WHERE new_email = :email AND activation_code = :activation_code';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':activation_code', $activation_code);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateEmailAndClearActivationCode($pdo, $userId) {
    $sql = 'UPDATE users SET email = new_email, new_email = NULL, activation_code = "" WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $userId);
    return $stmt->execute();
}

$message = "";

if (isset($_GET['email']) && isset($_GET['activation_code'])) {
    $email = $_GET['email'];
    $activation_code = $_GET['activation_code'];

    // Fetch user by email and activation code
    $user = fetchUserByEmailAndActivationCode($pdo, $email, $activation_code);

    if ($user) {
        if (updateEmailAndClearActivationCode($pdo, $user['id'])) {
            // Logout the user after email update for security reasons
            session_start();
            session_unset();
            session_destroy();
            $message = "Email updated successfully. Please log in with your new email.";
        } else {
            $message = "Failed to update email. Please try again.";
        }
    } else {
        $message = "Invalid or expired activation link.";
    }
} else {
    $message = "Invalid request.";
}

header("Location: login.php?message=" . urlencode($message));
exit();
?>

<?php
require("../config/database.php");

$password_err = $confirm_password_err = $message = $err_invalid = "";
$password = $confirm_password = "";

function testInput($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function isValidPassword($password) {
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    return strlen($password) >= 8 && $uppercase && $lowercase && $number;
}

function fetchTokenByUsername($pdo, $username) {
    $query = $pdo->prepare('SELECT token FROM users WHERE username = :username');
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

function fetchToken($pdo, $token) {
    $query = $pdo->prepare('SELECT token FROM users WHERE token = :token');
    $query->bindParam(':token', $token, PDO::PARAM_STR);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

function updatePassword($pdo, $new_password, $token) {
    $new_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_pass = "UPDATE users SET password = :password WHERE token = :token";
    $stmt = $pdo->prepare($update_pass);
    $stmt->execute([
        ':password' => $new_password,
        ':token' => $token
    ]);
    return $stmt->rowCount();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = testInput($_POST["password"] ?? '');
    $confirm_password = testInput($_POST["confirm_password"] ?? '');

    if (isset($_POST['reset_password'])) {
        if (empty($password)) {
            $password_err = "Please enter a password.";     
        } elseif (!isValidPassword($password)) {
            $password_err = "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.";
        }

        if (empty($confirm_password)) {
            $confirm_password_err = "Please confirm password.";     
        } elseif ($password !== $confirm_password) {
            $confirm_password_err = "Password did not match.";
        }

        if (empty($password_err) && empty($confirm_password_err)) {
            $tokenExists = fetchToken($pdo, $_POST['reset']);

            if ($tokenExists) {
                if (updatePassword($pdo, $password, $_POST['reset'])) {
                    $message = "Your password has been changed. You'll be redirected to the login page soon.";
                    header("Refresh: 2; url=login.php");
                } else {
                    $message_err = "Your password wasn't changed.";
                }
            }
        }
    }
}

if (isset($_GET['username'])) {
    $token = fetchTokenByUsername($pdo, $_GET['username']);
    if (!$token || $token['token'] !== $_GET['reset']) {
        $err_invalid = 'Invalid link';
    }
} else {
    header("Location: ../index.php");
}
?>

<?php ob_start(); ?>

<div class="loginForm">
    <h2 id="title2">Reset password</h2>
    <p style="color:green;"><?php echo $message; ?></p>
    <p style="color:red;"><?php echo $err_invalid; ?></p>
    <form action="" method="post">
        <input type="password" name="password" placeholder="New Password" value="<?php echo htmlspecialchars($password); ?>">
        <span><?php echo $password_err; ?></span>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" value="<?php echo htmlspecialchars($confirm_password); ?>">
        <span><?php echo $confirm_password_err; ?></span>
        <input type="hidden" name="reset" value="<?php if(isset($_GET['reset'])){ echo htmlspecialchars($_GET['reset']); } ?>">
        <input type="submit" value="Reset Password" name="reset_password">
    </form>
</div><br/>    
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>

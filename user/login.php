<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../index.php");
    exit;
}

require_once("../config/database.php");

$username = $password = "";
$username_err = $password_err = $activation_message = "";

function testInput($data) {
    if (is_null($data)) {
        return '';
    }
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function isValidUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

function fetchUserByUsername($pdo, $username) {
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function logFailedAttempt($pdo, $user_id, $ip_address) {
    $sql = "INSERT INTO login_attempts (user_id, ip_address, attempt_time) VALUES (:user_id, :ip_address, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->bindParam(":ip_address", $ip_address, PDO::PARAM_STR);
    $stmt->execute();
}

function countFailedAttempts($pdo, $user_id, $ip_address) {
    $sql = "SELECT COUNT(*) FROM login_attempts WHERE user_id = :user_id AND ip_address = :ip_address AND attempt_time > (NOW() - INTERVAL 1 HOUR)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->bindParam(":ip_address", $ip_address, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function lockAccount($pdo, $user_id) {
    $sql = "UPDATE users SET account_locked = 1, account_locked_until = (NOW() + INTERVAL 1 HOUR) WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
    $stmt->execute();
}

function unlockAccount($pdo, $user_id) {
    $sql = "UPDATE users SET account_locked = 0, account_locked_until = NULL WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
    $stmt->execute();
}

function resetFailedAttempts($pdo, $user_id) {
    $sql = "DELETE FROM login_attempts WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();
}

// Capture the message from the URL
if (isset($_GET['message'])) {
    $activation_message = htmlspecialchars($_GET['message']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = testInput($_POST["username"]);
    $password = testInput($_POST["password"]);
    $ip_address = $_SERVER['REMOTE_ADDR'];

    if (empty($username)) {
        $username_err = "Please enter username.";
    } elseif (!isValidUsername($username)) {
        $username_err = "Invalid username. Only letters, numbers, and underscores are allowed. Length should be between 3 and 20 characters.";
    }
    if (empty($password)) {
        $password_err = "Please enter your password.";
    }
    if (empty($username_err) && empty($password_err)) {
        $user = fetchUserByUsername($pdo, $username);

        if ($user) {
            if ($user["account_locked"] === 1 && strtotime($user["account_locked_until"]) > time()) {
                $activation_message = "Your account is locked due to multiple failed login attempts. Please try again after 1 hour.";
            } else {
                if ($user["account_locked"] === 1) {
                    unlockAccount($pdo, $user["id"]);
                    resetFailedAttempts($pdo, $user["id"]);
                }

                $failed_attempts = countFailedAttempts($pdo, $user['id'], $ip_address);

                if ($failed_attempts >= 3) {
                    lockAccount($pdo, $user['id']);
                    $activation_message = "Your account is locked due to multiple failed login attempts. Please try again after 1 hour.";
                } else {
                    if ($user["user_status"] === 'verified') {
                        if (password_verify($password, $user["password"])) {
                            resetFailedAttempts($pdo, $user['id']);
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $user["id"];
                            $_SESSION["username"] = $user["username"];
                            $_SESSION["email"] = $user["email"];
                            header("location: ../camera.php");
                        } else {
                            $password_err = "The password you entered is not valid.";
                            logFailedAttempt($pdo, $user["id"], $ip_address);
                        }
                    } else {
                        $activation_message = "The account is not yet verified. Please check your email.";
                    }
                }
            }
        } else {
            $username_err = "No account found with that username.";
        }
    }
}
?>

<?php ob_start(); ?>
<div style="max-height: 705px;" id="a">
    <div class="loginForm" style="min-height:364px;">
        <form action="" method="post">
            <h2 id="title2">Login</h2>
            <?php if (!empty($activation_message)): ?>
                <div class="message" style="color: green;"><?php echo $activation_message; ?></div>
            <?php endif; ?>
            <span><?php echo $username_err; ?></span>
            <input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>">
            <span><?php echo $password_err; ?></span>
            <input type="password" name="password" placeholder="Password">
            <input type="submit" value="Login">
            <p>Forgot your password? <a href="forgotPassword.php">Click here!</a></p>
        </form>
    </div><br>
    <div class="loginForm">
        <p style="text-align:center">Don't have an account? <a href="register.php">Sign up now</a></p>
    </div><br>
</div>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>

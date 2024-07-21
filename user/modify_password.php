<?php
session_start();
require("../config/database.php");

if (empty($_SESSION['loggedin'])) {
    header('Location: ../index.php');
    exit;
}

$old_password = $new_password = $confirm_password = "";
$password_err = $confirm_password_err = $error = "";
$message = $message_err = "";

function testInput($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function fetchUserPassword($pdo, $username) {
    $sql = "SELECT password FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateUserPassword($pdo, $username, $new_password) {
    $sql = "UPDATE users SET password = :password WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':password' => $new_password, ':username' => $username));
    return $stmt->rowCount() > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_pwd"])) {
    $old_password = testInput($_POST['old_password']);
    $new_password = testInput($_POST['new_password']);
    $confirm_password = testInput($_POST['new_confirm_password']);

    $uppercase = preg_match('@[A-Z]@', $new_password);
    $lowercase = preg_match('@[a-z]@', $new_password);
    $number    = preg_match('@[0-9]@', $new_password);

    $user = fetchUserPassword($pdo, $_SESSION['username']);

    if ($user && password_verify($old_password, $user['password'])) {
        if (empty($new_password)) {
            $password_err = "Please enter a new password.";
        } elseif (strlen($new_password) < 8 || !$uppercase || !$lowercase || !$number) {
            $password_err = "Password must be at least 8 characters long, contain an uppercase letter (A-Z), and a number (0-9).";
        } elseif (empty($confirm_password)) {
            $confirm_password_err = "Please confirm the new password.";
        } elseif ($new_password !== $confirm_password) {
            $confirm_password_err = "Passwords do not match.";
        }

        if (empty($password_err) && empty($confirm_password_err)) {
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            if (updateUserPassword($pdo, $_SESSION['username'], $new_hashed_password)) {
                $message = "Your password has been changed.";
            } else {
                $message_err = "Your password wasn't changed.";
            }
        }
    } else {
        $error = "Your old password is incorrect.";
    }
}

unset($pdo);
?>

<?php ob_start(); ?>
<div class="background galleryB">
    <div id="test">
        <h2 id="title" style="padding-top:0;text-shadow: 4px 2px 1px #67e8a6;">Hey Kitty</h2>
        <div id="account">
            <nav id="account_nav">
                <a id="EdPro" href="account.php">Edit Profile</a>
                <a id="EdPwd" href="modify_password.php">Edit Password</a>
                <a id="DelPho" href="delete_photos.php">Delete Photos</a>
                <a id="DelAcc" href="delete_account.php">Delete Account</a>
                <a id="Notif" href="notifications.php">Notifications</a>
            </nav>
            <article>
                <div style="max-height: 705px;" id="a">
                    <div class="loginForm accountForm ModPwd">      
                        <h2 id="subTitle">Change Password</h2>
                        <form action="" method="post">
                            <span style="color:green"><?php echo htmlspecialchars($message); ?></span>
                            <span style="color:red"><?php echo htmlspecialchars($message_err); echo htmlspecialchars($error); ?></span>
                            <input type="password" style="border: 3px solid #6cf1ac;margin:14px;" name="old_password" placeholder="Old Password" value="" required>
                            <span><?php echo htmlspecialchars($password_err); echo htmlspecialchars($confirm_password_err); ?></span>
                            <input type="password" style="border: 3px solid #4bb7ec;margin:14px;" name="new_password" placeholder="New Password" value="">
                            <input type="password" style="border: 3px solid #4bb7ec;margin:14px;" name="new_confirm_password" placeholder="Confirm New Password" value="">
                            <input type="submit" id="saveBtt" value="Update" name="change_pwd">
                        </form>
                    </div><br>
                </div>
            </article>
        </div>
    </div>
</div>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>

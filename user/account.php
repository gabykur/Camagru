<?php
require_once("../config/database.php");
require_once "../vendor/autoload.php";
session_start();

if (empty($_SESSION['loggedin'])) {
    header('Location: ../index.php');
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$username = $email = "";
$error = "";
$message = "";

function testInput($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function isValidUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function fetchUserByUsername($pdo, $username) {
    $sql = "SELECT username FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function fetchUserByEmail($pdo, $email) {
    $sql = "SELECT email FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateUsername($pdo, $username, $userId) {
    $sql = "UPDATE users SET username = :username WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $stmt->bindParam(":id", $userId, PDO::PARAM_INT);
    return $stmt->execute();
}

function updateEmail($pdo, $email, $activation_code, $userId) {
    $sql = "UPDATE users SET new_email = :email, activation_code = :activation_code WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->bindParam(":activation_code", $activation_code, PDO::PARAM_STR);
    $stmt->bindParam(":id", $userId, PDO::PARAM_INT);
    return $stmt->execute();
}

function sendActivationEmail($email, $activation_code) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = $_ENV['SMTP_ENCRYPTION'];
        $mail->Port = $_ENV['SMTP_PORT'];

        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body = '
            Please click the following link to verify your email address:<br>
            <a href="http://'.$_SERVER['HTTP_HOST'].'/user/verify_email.php?email='.$email.'&activation_code='.$activation_code.'">Verify Email</a>
        ';

        $mail->send();
    } catch (Exception $e) {
        global $error;
        $error .= "Failed to send activation email. Please try again later. ";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = testInput($_POST["new_username"]);
    $new_email = testInput($_POST["new_email"]);

    // Username modifications
    if (!empty($new_username)) {
        if (!isValidUsername($new_username)) {
            $error .= "Username must be 3-20 characters long and can only contain letters, numbers, and underscores. ";
        } else {
            $userExists = fetchUserByUsername($pdo, $new_username);
            if ($userExists && $new_username !== $_SESSION['username']) {
                $error .= "This username is already taken. ";
            } else {
                if (updateUsername($pdo, $new_username, $_SESSION["id"])) {
                    $message .= "Your username has been updated. ";
                    $_SESSION['username'] = $new_username;
                } else {
                    $error .= "Something went wrong with updating the username. ";
                }
            }
        }
    }

    // Email modifications
    if (!empty($new_email)) {
        if (!isValidEmail($new_email)) {
            $error .= "Invalid email format. ";
        } else {
            $userExists = fetchUserByEmail($pdo, $new_email);
            if ($userExists && $new_email !== $_SESSION['email']) {
                $error .= "This email is already taken. ";
            } else {
                $activation_code = md5(rand(0, 1000));
                if (updateEmail($pdo, $new_email, $activation_code, $_SESSION["id"])) {
                    sendActivationEmail($new_email, $activation_code);
                    $message .= "A verification email has been sent to your new email address. Please verify to complete the update. ";
                } else {
                    $error .= "Something went wrong with updating the email. ";
                }
            }
        }
    }
}
?>

<?php ob_start(); ?>
<div class="background galleryB">
    <div id="test">
        <h2 id="title" style="padding-top:0;text-shadow: 4px 2px 1px #67e8a6;">Hey Kitty </h2>
        <div id="account">
            <nav id="account_nav">
                <a id="EdPro" href="account.php">Edit Profile</a>
                <a id="EdPwd" href="modifyPassw.php">Edit Password</a>
                <a id="DelPho" href="deletePhotos.php">Delete Photos</a>
                <a id="DelAcc" href="deleteAccount.php">Delete Account</a>
                <a id="Notif" href="notifications.php">Notifications</a>
            </nav>
            <article>
                <div style="max-height: 705px;" id="a">
                    <div class="loginForm accountForm">
                        <h2 id="subTitle">Edit your profile</h2>
                        <form action="" method="post">
                            <span style="color:green; margin-top: 34px;"><?php echo $message; ?></span>
                            <span style="color:red; margin-top: 34px;"><?php echo $error; ?></span>
                            <input type="text" style="border: 3px solid #efb4e4;margin:14px;" name="new_username" placeholder="New Username" value="<?php echo htmlspecialchars($_SESSION["username"]); ?>">
                            <input type="email" style="border: 3px solid #6cf1ac;margin:14px;" name="new_email" placeholder="New Email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>">
                            <input type="submit" id="saveBtt" value="Update" name="save">
                        </form>
                    </div><br>
                </div>
            </article>
        </div>
    </div>
</div>

<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>

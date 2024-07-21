<?php
require("../config/database.php");
require_once "../vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function testInput($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function isValidUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

$error = "";
$reset_mess = "";

function fetchUserByUsername($pdo, $username) {
    $sql = "SELECT email FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateUserToken($pdo, $username, $token) {
    $sql = "UPDATE users SET token = :token WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':token' => $token,
        ':username' => $username
    ]);
}

function sendResetEmail($email, $username, $token) {
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
        $mail->Subject = 'Reset password';
        $mail->Body = '
            Dear '.$username.',<br><br>
            If this e-mail does not apply to you please ignore it.<br>
            It appears that you have requested a password reset.<br><br>
            To reset your password, please click the link below:<br>
            <a href="http://'.$_SERVER['HTTP_HOST'].'/user/reset_password.php?username='.$username.'&reset='.$token.'">Reset Password</a><br><br>
            If you cannot click it, please paste it into your web browser\'s address bar.<br><br>
            Thanks,<br>
            The Administration :)
        ';

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['forgot_password'])) {
        if (empty(testInput($_POST['email'])) || empty(testInput($_POST['username']))) {
            $error = "Please enter your username and email.";
        } else {
            $email = testInput($_POST["email"]);
            $username = testInput($_POST["username"]);

            if (!isValidUsername($username)) {
                $error = "Invalid username format.";
            } elseif (!isValidEmail($email)) {
                $error = "Invalid email format.";
            } else {
                $user = fetchUserByUsername($pdo, $username);
                
                if ($user) {
                    $token = bin2hex(openssl_random_pseudo_bytes(16));
                    if (updateUserToken($pdo, $username, $token)) {
                        if (sendResetEmail($user['email'], $username, $token)) {
                            $reset_mess = "If the email exists, you will receive an email to reset your password.";
                        } else {
                            $error = "Failed to send the email. Please try again later.";
                        }
                    } else {
                        $error = "Failed to generate reset token. Please try again later.";
                    }
                } else {
                    $reset_mess = "If the email exists, you will receive an email to reset your password.";
                }
            }
        }
    }
}
?>

<?php ob_start();?>
<div class="loginForm">
    <h2 id="title2">Forgotten Password</h2>
    <p id="actMsg" style="color:green;"><?php echo $reset_mess; ?></p>
    <form method="post" action="" style="margin-top:7%;">
        <input type="text" placeholder="Enter your login" name="username">
        <input type="email" placeholder="Enter your email" name="email">
        <span><?php echo $error; ?></span><br />
        <input type="submit" value="Send Link" name="forgot_password">
    </form>
</div><br/>
<div class="loginForm">
    <p style="text-align:center">Know your password? <a href="login.php"> Login</a></p>
</div><br>
<?php $view = ob_get_clean();?>
<?php require("../template.php");?>

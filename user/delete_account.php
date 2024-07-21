<?php
session_start();
require("../config/database.php");
require_once "../vendor/autoload.php";

if (empty($_SESSION['loggedin'])) {
    header('Location: ../index.php');
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$username = $_SESSION['id'];
$error = "";
$password = "";

function testInput($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function fetchUserById($pdo, $userId) {
    $sql = "SELECT id, password FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function deletePhotosByUser($pdo, $userId) {
    $query = $pdo->prepare("SELECT * FROM pictures WHERE id_user = :id_user");
    if ($query->execute(array(':id_user' => $userId))) {
        $filesToDelete = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filesToDelete as $fileToDelete) {
            unlink("../" . $fileToDelete['img']);
        }
    }
}

function deleteUserComments($pdo, $userId) {
    $query = $pdo->prepare("DELETE FROM comments WHERE id_user = :id_user");
    $query->bindParam(':id_user', $userId, PDO::PARAM_INT);
    $query->execute();
}

function deleteUserLikes($pdo, $userId) {
    $query = $pdo->prepare("SELECT id_img FROM likes WHERE id_user = :id_user");
    $query->execute(array(':id_user' => $userId));
    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    if ($res) {
        foreach ($res as $likedPhoto) {
            $pdo->query("UPDATE pictures SET likes = likes - 1 WHERE id_img = " . intval($likedPhoto['id_img']));
        }
    }
    $query = $pdo->prepare("DELETE FROM likes WHERE id_user = :id_user");
    $query->bindParam(':id_user', $userId, PDO::PARAM_INT);
    $query->execute();
}

function deleteUserPictures($pdo, $userId) {
    $query = $pdo->prepare("DELETE FROM pictures WHERE id_user = :id_user");
    $query->bindParam(':id_user', $userId, PDO::PARAM_INT);
    $query->execute();
}

function deleteUserAccount($pdo, $userId) {
    $query = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $query->bindParam(':id', $userId, PDO::PARAM_INT);
    return $query->execute();
}

function sendDeletionEmail($email, $username) {
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
        $mail->Subject = 'Account Deletion Confirmation';
        $mail->Body = "
            Dear $username,<br><br>
            Your account has been successfully deleted.<br><br>
            Best regards,<br>
            The Team
        ";

        $mail->send();
    } catch (Exception $e) {
        $error = "Error : Failed to send deletion email. Mailer Error: {$mail->ErrorInfo}<br>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account'])) {
    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = testInput($_POST['password']);
        $user = fetchUserById($pdo, $_SESSION['id']);

        if ($user && password_verify($password, $user['password'])) {
            deletePhotosByUser($pdo, $_SESSION['id']);
            deleteUserComments($pdo, $_SESSION['id']);
            deleteUserLikes($pdo, $_SESSION['id']);
            deleteUserPictures($pdo, $_SESSION['id']);

            if (deleteUserAccount($pdo, $_SESSION['id'])) {
                sendDeletionEmail($_SESSION['email'], $_SESSION['username']);
                $message = "Account deleted";
                $_SESSION["loggedin"] = "";
                session_destroy();
                header("Location: ../index.php?message=" . urlencode($message));
                exit;
            } else {
                $error = "Your account wasn't deleted";
            }
        } else {
            $error = "Incorrect Password";
        }
    } else {
        $error = "Please enter your password.";
    }
}
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
                    <div class="loginForm accountForm DelAcc">
                        <h2 id="subTitle">Delete Your Account</h2>
                        <form action="" method="post">
                            <span><?php echo htmlspecialchars($error); ?></span>
                            <input type="password" style="margin-top:41px;" name="password" placeholder="Enter password to delete account" value="<?php echo htmlspecialchars($password); ?>" required>
                            <input type="submit" id="saveBtt" style="margin-top: 15px;font-size: 22px;" name="delete_account" value="Delete Account">
                        </form>
                    </div><br>
                </div>
            </article>
        </div>
    </div>
</div>

<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>

<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PhotoModel;
use App\Utils\InputValidator;
use App\Utils\SessionHelper;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

class UserController {
    private $userModel;
    private $photoModel;

    public function __construct($pdo) {
        $this->userModel = new UserModel($pdo);
        $this->photoModel = new PhotoModel($pdo);
    }

    private function sendActivationEmail($email, $activation_code) {
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
                <a href="http://' . $_SERVER['HTTP_HOST'] . '/user/verifyEmail?email=' . $email . '&activation_code=' . $activation_code . '">Verify Email</a>
            ';

            $mail->send();
        } catch (Exception $e) {
            $error = "Failed to send activation email. Please try again later. ";
        }
    }

    public function account() {
        SessionHelper::checkLoggedIn();

        $message = "";
        $error = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_username = InputValidator::testInput($_POST["new_username"]);
            $new_email = InputValidator::testInput($_POST["new_email"]);

            // Username modifications
            if (!empty($new_username)) {
                if (!InputValidator::isValidUsername($new_username)) {
                    $error .= "Username must be 3-20 characters long and can only contain letters, numbers, and underscores. ";
                } else {
                    $userExists = $this->userModel->fetchUserByUsername($new_username);
                    if ($userExists && $new_username !== $_SESSION['username']) {
                        $error .= "This username is already taken. ";
                    } else {
                        if ($this->userModel->updateUsername($new_username, $_SESSION["id"])) {
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
                if (!InputValidator::isValidEmail($new_email)) {
                    $error .= "Invalid email format. ";
                } else {
                    $userExists = $this->userModel->fetchUserByEmail($new_email);
                    if ($userExists && $new_email !== $_SESSION['email']) {
                        $error .= "This email is already taken. ";
                    } else {
                        $activation_code = md5(rand(0, 1000));
                        if ($this->userModel->updateEmail($new_email, $activation_code, $_SESSION["id"])) {
                            $this->sendActivationEmail($new_email, $activation_code);
                            $message .= "A verification email has been sent to your new email address. Please verify to complete the update. ";
                        } else {
                            $error .= "Something went wrong with updating the email. ";
                        }
                    }
                }
            }
        }

        $pageTitle = 'Edit your profile';
        $formAction = '/user/account';
        $formContent = '
            <input type="text" style="border: 3px solid #efb4e4;margin:14px;" name="new_username" placeholder="New Username" value="' . htmlspecialchars($_SESSION["username"]) . '">
            <input type="email" style="border: 3px solid #6cf1ac;margin:14px;" name="new_email" placeholder="New Email" value="' . htmlspecialchars($_SESSION['email']) . '">
            <input type="submit" id="saveBtt" value="Update" name="save">
        ';
        require '../Views/user/account_template.php';
    }

    public function modifyPassword() {
        SessionHelper::checkLoggedIn();

        $message = "";
        $error = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_pwd"])) {
            $old_password = InputValidator::testInput($_POST['old_password']);
            $new_password = InputValidator::testInput($_POST['new_password']);
            $confirm_password = InputValidator::testInput($_POST['new_confirm_password']);

            $uppercase = preg_match('@[A-Z]@', $new_password);
            $lowercase = preg_match('@[a-z]@', $new_password);
            $number = preg_match('@[0-9]@', $new_password);

            $user = $this->userModel->fetchUserPassword($_SESSION['username']);

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
                    if ($this->userModel->updateUserPassword($_SESSION['username'], $new_hashed_password)) {
                        $message = "Your password has been changed.";
                    } else {
                        $message_err = "Your password wasn't changed.";
                    }
                }
            } else {
                $error = "Your old password is incorrect.";
            }
        }

        $pageTitle = 'Change Password';
        $formAction = '/user/modifyPassword';
        $formContent = '
            <input type="password" style="border: 3px solid #6cf1ac;margin:14px;" name="old_password" placeholder="Old Password" value="" required>
            <span>' . htmlspecialchars($password_err) . htmlspecialchars($confirm_password_err) . '</span>
            <input type="password" style="border: 3px solid #4bb7ec;margin:14px;" name="new_password" placeholder="New Password" value="">
            <input type="password" style="border: 3px solid #4bb7ec;margin:14px;" name="new_confirm_password" placeholder="Confirm New Password" value="">
            <input type="submit" id="saveBtt" value="Update" name="change_pwd">
        ';
        require '../Views/user/account_template.php';
    }

    public function deleteAccount() {
        SessionHelper::checkLoggedIn();

        $message = "";
        $error = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account'])) {
            if (isset($_POST['password']) && !empty($_POST['password'])) {
                $password = InputValidator::testInput($_POST['password']);
                $user = $this->userModel->fetchUserById($_SESSION['id']);

                if ($user && password_verify($password, $user['password'])) {
                    $this->photoModel->deleteUserPhotos($_SESSION['id']);
                    $this->userModel->deleteUserComments($_SESSION['id']);
                    $this->userModel->deleteUserLikes($_SESSION['id']);
                    $this->photoModel->deleteUserPictures($_SESSION['id']);

                    if ($this->userModel->deleteUserAccount($_SESSION['id'])) {
                        $this->sendDeletionEmail($_SESSION['email'], $_SESSION['username']);
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

        $pageTitle = 'Delete Your Account';
        $formAction = '/user/deleteAccount';
        $formContent = '
            <input type="password" style="margin-top:41px;" name="password" placeholder="Enter password to delete account" value="" required>
            <input type="submit" id="saveBtt" style="margin-top: 15px;font-size: 22px;" name="delete_account" value="Delete Account">
        ';
        require '../Views/user/account_template.php';
    }

    public function deletePhotos() {
        SessionHelper::checkLoggedIn();

        $message = "";
        $error = "";

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
            if (isset($_POST['check']) && !empty($_POST['check'])) {
                $checkbox = array_map('intval', $_POST['check']);
                $delId = implode(",", $checkbox);
                $filesToDelete = $this->photoModel->fetchUserPhotos($_SESSION['id']);
                $filesToDelete = array_filter($filesToDelete, function($photo) use ($checkbox) {
                    return in_array($photo['id_img'], $checkbox);
                });
                $this->deleteFilesFromFilesystem($filesToDelete);
                $this->photoModel->deletePhotosFromDatabase($delId);
                $message = "Selected photos have been deleted successfully.";
            } else {
                $message_err = "Please choose photos to delete.";
            }
        }

        $pageTitle = 'Delete Photos';
        $formAction = '/user/deletePhotos';
        $formContent = '';
        $photos = $this->photoModel->fetchUserPhotos($_SESSION['id']);
        foreach ($photos as $photo) {
            $formContent .= "
                <div id='img'>
                    <img src='../" . htmlspecialchars($photo['img']) . "'>
                    <input type='checkbox' id='check_del' name='check[]' value='" . intval($photo['id_img']) . "'>
                </div>";
        }
        $formContent .= '
            <div class="loginForm accountForm" style="background:none; box-shadow:none">
                <input type="submit" id="saveBtt" style="margin-top: 11px;font-size: 24px;margin-bottom:7px" name="delete" value="Delete">          
            </div>
        ';
        require '../Views/user/account_template.php';
    }

    public function notifications() {
        SessionHelper::checkLoggedIn();

        $message = "";
        $error = "";

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
            $notif = isset($_POST['notif']) ? 1 : 0;
            if ($this->userModel->updateNotificationSetting($_SESSION['id'], $notif)) {
                $message = "Updated ;)";
                header("Refresh: 2");
            } else {
                $message = "Failed to update.";
            }
        }

        $pageTitle = 'Notifications';
        $formAction = '/user/notifications';
        $data = $this->userModel->getNotificationSetting($_SESSION['id']);
        $formContent = '
            <label id="notiflabel">
                <input id="checkbox" type="checkbox" name="notif" ' . (isset($data['notif']) && $data['notif'] === 1 ? 'checked="checked"' : '') . '>
                <p id="pNot">Set up notifications on comments</p>
            </label>
            <div class="loginForm accountForm" style="background:none; box-shadow:none">
                <input type="submit" id="saveBtt" style="width: 29%;font-size: 24px;" name="update" value="Update">          
            </div>
        ';
        require '../Views/user/account_template.php';
    }

    public function verifyEmail() {
        session_start();
        $message = "";

        if (isset($_GET['email']) && isset($_GET['activation_code'])) {
            $email = $_GET['email'];
            $activation_code = $_GET['activation_code'];

            $user = $this->userModel->fetchUserByEmailAndActivationCode($email, $activation_code);

            if ($user) {
                if ($this->userModel->updateEmailAndClearActivationCode($user['id'])) {
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

        header("Location: /user/login?message=" . urlencode($message));
        exit();
    }
}
?>

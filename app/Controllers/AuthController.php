<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Utils\InputValidator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

class AuthController {
    protected $authModel;

    public function __construct($pdo) {
        $this->authModel = new AuthModel($pdo);
    }

    public function login() {
        session_start();

        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            header("location: /");
            exit;
        }

        $username = $password = "";
        $username_err = $password_err = $activation_message = "";

        // Capture the message from the URL
        if (isset($_GET['message'])) {
            $activation_message = htmlspecialchars($_GET['message']);
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = InputValidator::testInput($_POST["username"]);
            $password = InputValidator::testInput($_POST["password"]);
            $ip_address = $_SERVER['REMOTE_ADDR'];

            if (empty($username)) {
                $username_err = "Please enter username.";
            } elseif (!InputValidator::isValidUsername($username)) {
                $username_err = "Invalid username. Only letters, numbers, and underscores are allowed. Length should be between 3 and 20 characters.";
            }
            if (empty($password)) {
                $password_err = "Please enter your password.";
            }
            if (empty($username_err) && empty($password_err)) {
                $user = $this->authModel->fetchUserByUsername($username);

                if ($user) {
                    if ($user["account_locked"] === 1 && strtotime($user["account_locked_until"]) > time()) {
                        $activation_message = "Your account is locked due to multiple failed login attempts. Please try again after 1 hour.";
                    } else {
                        if ($user["account_locked"] === 1) {
                            $this->authModel->unlockAccount($user["id"]);
                            $this->authModel->resetFailedAttempts($user["id"]);
                        }

                        $failed_attempts = $this->authModel->countFailedAttempts($user['id'], $ip_address);

                        if ($failed_attempts >= 3) {
                            $this->authModel->lockAccount($user['id']);
                            $activation_message = "Your account is locked due to multiple failed login attempts. Please try again after 1 hour.";
                        } else {
                            if ($user["user_status"] === 'verified') {
                                if (password_verify($password, $user["password"])) {
                                    $this->authModel->resetFailedAttempts($user['id']);
                                    $_SESSION["loggedin"] = true;
                                    $_SESSION["id"] = $user["id"];
                                    $_SESSION["username"] = $user["username"];
                                    $_SESSION["email"] = $user["email"];
                                    header("location: /camera");
                                } else {
                                    $password_err = "The password you entered is not valid.";
                                    $this->authModel->logFailedAttempt($user["id"], $ip_address);
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

        $view = 'auth/login.php';
        require_once __DIR__ . '/../../app/Views/template.php';;
    }

    public function register() {
        session_start();

        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            header('Location: /');
            exit;
        }

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $username = $email = $password = $confirm_password = "";
        $username_err = $email_err = $password_err = $confirm_password_err = $activation_mess = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = InputValidator::testInput($_POST["username"]);
            $email = InputValidator::testInput($_POST["email"]);
            $password = InputValidator::testInput($_POST["password"]);
            $confirm_password = InputValidator::testInput($_POST["confirm_password"]);

            // Validate username
            if (empty($username)) {
                $username_err = "Please enter a username.";
            } elseif (!InputValidator::isValidUsername($username)) {
                $username_err = "Username must be 3-20 characters long and can only contain letters, numbers, and underscores.";
            } elseif ($this->authModel->fetchUserByUsername($username)) {
                $username_err = "This username is already taken.";
            }

            // Validate email
            if (empty($email)) {
                $email_err = "Please enter an email.";
            } elseif (!InputValidator::isValidEmail($email)) {
                $email_err = "Please enter a valid email address.";
            } elseif ($this->authModel->fetchUserByEmail($email)) {
                $email_err = "This email is already taken.";
            }

            // Validate password
            if (empty($password)) {
                $password_err = "Please enter a password.";     
            } elseif (!InputValidator::isValidPassword($password)) {
                $password_err = "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.";
            }

            // Validate confirm password
            if (empty($confirm_password)) {
                $confirm_password_err = "Please confirm password.";     
            } elseif ($password !== $confirm_password) {
                $confirm_password_err = "Password did not match.";
            }

            // Check input errors before inserting in database
            if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $activation_code = md5(rand(0, 1000));

                if ($this->authModel->insertUser($username, $email, $hashed_password, $activation_code)) {
                    $this->sendActivationEmail($email, $username, $activation_code, $activation_mess);
                } else {
                    $activation_mess = "Something went wrong. Please try again later";
                }
            }
        }

        $view = 'auth/register.php';
        require_once __DIR__ . '/../../app/Views/template.php';;
    }

    public function sendActivationEmail($email, $username, $activation_code, &$activation_mess) {
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
            $mail->Subject = 'Signup | Verification';
            $mail->Body = '
                Thanks for signing up!<br>
                Your account Catgram has been created!<br>
                You can login with the following credentials after you have activated your account by pressing the url below.<br><br>

                ------------------------<br>
                Username: '.$username.'<br>
                Password: (the one you provided during signup)<br>
                ------------------------<br><br>

                Please click this link to activate your account:<br>
                http://' . $_SERVER['HTTP_HOST'] . '/auth/activationAccount?username=' . $username . '&activation_code=' . $activation_code . '
            ';

            $mail->send();
            $activation_mess = "Go check your email to activate your account";
        } catch (Exception $e) {
            $activation_mess = "Something went wrong while sending the activation email. Please try again later.";
        }
    }

    public function activationAccount() {
        $message = '';

        if (isset($_GET['activation_code'])) {
            $activation_code = htmlspecialchars($_GET['activation_code']);
            $user = $this->authModel->fetchUserByActivationCode($activation_code);

            if ($user) {
                if ($user['user_status'] === 'not verified') {
                    if ($this->authModel->verifyUser($user['username'])) {
                        $message = '<span style="color:green">Your Email Address Successfully Verified</span>';
                    } else {
                        $message = '<span style="color:red">Failed to verify your email address. Please try again later.</span>';
                    }
                } else {
                    $message = '<span style="color:blue">Your Email Address Already Verified</span>';
                }
            } else {
                $message = '<span style="color:darkred">Invalid Link</span>';
            }
        } else {
            header("Location: /");
            exit();
        }

        $view = 'auth/activationAccount.php';
        require_once __DIR__ . '/../../app/Views/template.php';;
    }

    public function forgotPassword() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $error = "";
        $reset_mess = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['forgot_password'])) {
                $email = InputValidator::testInput($_POST["email"]);
                $username = InputValidator::testInput($_POST["username"]);

                if (empty($email) || empty($username)) {
                    $error = "Please enter your username and email.";
                } elseif (!InputValidator::isValidUsername($username)) {
                    $error = "Invalid username format.";
                } elseif (!InputValidator::isValidEmail($email)) {
                    $error = "Invalid email format.";
                } else {
                    $user = $this->authModel->fetchUserByUsername($username);

                    if ($user) {
                        $token = bin2hex(openssl_random_pseudo_bytes(16));
                        if ($this->authModel->updateUserToken($username, $token)) {
                            if ($this->sendResetEmail($user['email'], $username, $token)) {
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

        $view = 'auth/forgotPassword.php';
        require_once __DIR__ . '/../../app/Views/template.php';;
    }

    private function sendResetEmail($email, $username, $token) {
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
                <a href="http://' . $_SERVER['HTTP_HOST'] . '/auth/resetPassword?username=' . $username . '&reset=' . $token . '">Reset Password</a><br><br>
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

    public function resetPassword() {
        $password_err = $confirm_password_err = $message = $err_invalid = "";
        $password = $confirm_password = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $password = InputValidator::testInput($_POST["password"] ?? '');
            $confirm_password = InputValidator::testInput($_POST["confirm_password"] ?? '');

            if (isset($_POST['reset_password'])) {
                if (empty($password)) {
                    $password_err = "Please enter a password.";     
                } elseif (!InputValidator::isValidPassword($password)) {
                    $password_err = "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.";
                }

                if (empty($confirm_password)) {
                    $confirm_password_err = "Please confirm password.";     
                } elseif ($password !== $confirm_password) {
                    $confirm_password_err = "Password did not match.";
                }

                if (empty($password_err) && empty($confirm_password_err)) {
                    $tokenExists = $this->authModel->fetchToken($_POST['reset']);

                    if ($tokenExists) {
                        if ($this->authModel->updatePassword($password, $_POST['reset'])) {
                            $message = "Your password has been changed. You'll be redirected to the login page soon.";
                            header("Refresh: 2; url=/auth/login");
                        } else {
                            $message_err = "Your password wasn't changed.";
                        }
                    }
                }
            }
        }

        if (isset($_GET['username'])) {
            $token = $this->authModel->fetchTokenByUsername($_GET['username']);
            if (!$token || $token['token'] !== $_GET['reset']) {
                $err_invalid = 'Invalid link';
            }
        } else {
            header("Location: /");
        }

        $view = 'auth/resetPassword.php';
        require_once __DIR__ . '/../../app/Views/template.php';;
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /auth/login");
        exit;
    }
}

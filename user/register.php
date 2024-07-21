<?php
require_once("../config/database.php");
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    header('Location: ../index.php');
    exit;
}

$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = $activation_mess = "";

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
    $sql = "SELECT id FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function fetchUserByEmail($pdo, $email) {
    $sql = "SELECT id FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function insertUser($pdo, $username, $email, $password, $activation_code) {
    $sql = "INSERT INTO users (username, email, password, activation_code, user_status, token, notif)
            VALUES (:username, :email, :password, :activation_code, :user_status, :token, :notif)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->bindParam(":password", $password, PDO::PARAM_STR);
    $stmt->bindParam(":activation_code", $activation_code, PDO::PARAM_STR);
    $stmt->bindParam(":user_status", $user_status, PDO::PARAM_STR);
    $stmt->bindParam(":token", $token, PDO::PARAM_STR);
    $stmt->bindParam(":notif", $notif, PDO::PARAM_INT);
    $user_status = 'not verified';
    $token = '';
    $notif = 1;
    return $stmt->execute();
}

function sendActivationEmail($email, $username, $activation_code) {
    $to = $email;
    $subject = 'Signup | Verification';
    $message = '
        Thanks for signing up!
        Your account Catgram has been created! 
        You can login with the following credentials after you have activated your account by pressing the url below.

        ------------------------
        Username: '.$username.'
        Password: (the one you provided during signup)
        ------------------------

        Please click this link to activate your account:
        http://'.$_SERVER['HTTP_HOST'].'/user/activation.php?username='.$username.'&activationCode='.$activation_code.'
    ';
    $headers = 'From:noreply@gabriele.com' . "\r\n"; 
    mail($to, $subject, $message, $headers); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = testInput($_POST["username"]);
    $email = testInput($_POST["email"]);
    $password = testInput($_POST["password"]);
    $confirm_password = testInput($_POST["confirm_password"]);

    // Validate username
    if (empty($username)) {
        $username_err = "Please enter a username.";
    } elseif (!isValidUsername($username)) {
        $username_err = "Username must be 3-20 characters long and can only contain letters, numbers, and underscores.";
    } elseif (fetchUserByUsername($pdo, $username)) {
        $username_err = "This username is already taken.";
    }

    // Validate email
    if (empty($email)) {
        $email_err = "Please enter an email.";
    } elseif (!isValidEmail($email)) {
        $email_err = "Please enter a valid email address.";
    } elseif (fetchUserByEmail($pdo, $email)) {
        $email_err = "This email is already taken.";
    }

    // Validate password
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);

    if (empty($password)) {
        $password_err = "Please enter a password.";     
    } elseif (strlen($password) < 8 || !$uppercase || !$lowercase || !$number) {
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

        if (insertUser($pdo, $username, $email, $hashed_password, $activation_code)) {
            sendActivationEmail($email, $username, $activation_code);
            $activation_mess = "Go check your email to activate your account";
        } else {
            $activation_mess = "Something went wrong. Please try again later";
        }
    }
}
?>
<?php ob_start(); ?>

<div class="loginForm" style="min-height:364px;">
    <h2 id="title2">Sign Up</h2>
    <p id="actMsg"><?php echo $activation_mess; ?></p><br>
    <form action="" method="post">
        <span><?php echo $username_err; ?></span>
        <input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>">
        <span><?php echo $email_err; ?></span>
        <input type="email" name="email" placeholder="Email" value="<?php echo $email; ?>">
        <span><?php echo $password_err; ?></span>
        <input type="password" name="password" placeholder="Enter Password" value="<?php echo $password; ?>">
        <span><?php echo $confirm_password_err; ?></span>
        <input type="password" name="confirm_password" placeholder="Confirm Password" value="<?php echo $confirm_password; ?>">
        <input type="submit" value="Register">
    </form>
</div><br>
<div class="loginForm">
    <p>Already have an account?<a href="login.php"> Login here</a></p>
</div><br>

<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>

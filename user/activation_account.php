<?php
require("../config/database.php");

$message = '';

function fetchUserByActivationCode($pdo, $activation_code) {
    $query = "SELECT * FROM users WHERE activation_code = :activation_code";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':activation_code', $activation_code, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
}

function verifyUser($pdo, $username) {
    $update_query = "UPDATE users SET user_status = 'verified', activation_code = '' WHERE username = :username";
    $statement = $pdo->prepare($update_query);
    $statement->bindParam(':username', $username, PDO::PARAM_STR);
    return $statement->execute();
}

if (isset($_GET['activation_code'])) {
    $activation_code = htmlspecialchars($_GET['activation_code']);
    $user = fetchUserByActivationCode($pdo, $activation_code);

    if ($user) {
        if ($user['user_status'] === 'not verified') {
            if (verifyUser($pdo, $user['username'])) {
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
    header("Location: ../index.php");
    exit();
}
?>

<?php ob_start(); ?>
<div style="min-height:250px;">
   <div class="loginForm" style="border:none;background-color:transparent">
        <h2 id="title2">Activate your account</h2>
        <h3 style="text-align:center"><?php echo $message; ?></h3>
        <input type="button" value="Login" onclick="window.location='login.php'" />
    </div>
</div>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>

<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Hi <b><?php if($_SESSION['loggedin'] == true){echo htmlspecialchars($_SESSION["username"]);} ?></b> Welcome to our site.</h1>
    </div>
<?php
    var_dump($_SESSION['loggedin']);
    if ($_SESSION['loggedin'] != ""){
        echo'<p>
                <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
                <a href="user/logout.php" class="btn btn-danger">Sign Out of Your Account</a>
            </p>';
    }else{
        echo'<p>
                <a href="login.php" class="btn btn-warning">Login</a>
                <a href="register.php" class="btn btn-danger">Register</a>
            </p>';
    }
?>
</body>
</html>
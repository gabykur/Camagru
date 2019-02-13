<?php
session_start();
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
       header('location: home.php');
    }else{
        echo'<p>
                <a href="http://'.$_SERVER['HTTP_HOST'].'/user/login.php" class="btn btn-warning">Login</a>
                <a href="http://'.$_SERVER['HTTP_HOST'].'/user/register.php" class="btn btn-danger">Register</a>
            </p>';
    }
?>
</body>
</html>
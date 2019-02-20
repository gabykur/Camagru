<?php
session_start();
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Camagru</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link rel="stylesheet" href="/public/css/main.css">
</head>
<body>
<header>
    <table class="container borderXwidth "><tr>
        <td><a href="/home.php">PHOTO</a></td>
        <td><h1>Camagru</h1></td>
        <td style="width:50%"></td>
        <?php
        if ($_SESSION['loggedin'] != ""){
            echo'
                
                <td><a href="http://'.$_SERVER['HTTP_HOST'].'/user/account.php">Account</a></td>
                <td><a href="http://'.$_SERVER['HTTP_HOST'].'/user/logout.php">Log Out</a></td>
            
                ';
        }else{
            echo'
            <td><a class="logButt" href="http://'.$_SERVER['HTTP_HOST'].'/user/login.php">Log In</a></td>
            <td><a class="signButt" href="http://'.$_SERVER['HTTP_HOST'].'/user/register.php">Sign In</a></td>';
        }?>
    </tr></table>
</header>
</body>
</html>

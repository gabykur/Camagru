<?php
session_start();
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Camagru</title>
    <link rel="stylesheet" href="/public/css/main.css">
</head>
<body>
<header>
    <table class="container borderXwidth"><tr>
        <td><a href="/home.php">PHOTO</a></td>
        <td><h1>Camagru</h1></td>
       
        <?php
        if ($_SESSION['loggedin'] != ""){
            echo'
                <td style="width:50px"><a href="http://'.$_SERVER['HTTP_HOST'].'/user/account.php">ACCOUNT</a></td>
                <td style="width:50px"><a href="http://'.$_SERVER['HTTP_HOST'].'/user/logout.php">LOGOUT</a></td>
                ';
        }else{
            echo'
            <td><a href="http://'.$_SERVER['HTTP_HOST'].'/user/login.php">LOGIN</a></td>';
        }?>
    </tr></table>
</header>
<div style="
    width: 100%;
    max-width: 1200px;
    margin: auto;
"></div>
</body>
</html>

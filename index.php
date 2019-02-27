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
    <table class="container"><tr>
        <td><a href="/home.php">Photo</a></td>
        <td id="Cam_logo"><h1>Camagru</h1></td>
        <td style="width:50%"></td>
        <?php
        if ($_SESSION['loggedin'] != ""){
            echo'
                
                <td style="width:10%"><a href="http://'.$_SERVER['HTTP_HOST'].'/user/account.php">Account</a></td>
                <td style="width:10%"><a href="http://'.$_SERVER['HTTP_HOST'].'/user/logout.php">LogOut</a></td>
            
                ';
        }else{
            echo'
            <td style="width:10%"><a class="logButt" href="http://'.$_SERVER['HTTP_HOST'].'/user/login.php">LogIn</a></td>
            <td style="width:10%"><a class="signButt" href="http://'.$_SERVER['HTTP_HOST'].'/user/register.php">SignIn</a></td>';
        }?>
    </tr></table>
</header>
<div class="main">
    <?= $view ?>
</div>
<footer><p>Camagru ©gkuraite | 2019</p></footer>
</body>
</html>

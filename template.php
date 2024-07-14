<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta charset="UTF-8">
    <title>Camagru</title>
    <link rel="icon" href="/public/icons/noel.jpg">
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
</head>
<body>
<header>
    <table class="container"><tr>
        <td><a href="/camera.php">Photo</a></td>
        <td><a href="/index.php" id="Cam_logo" style="padding:0">
            <h1>Camagru</h1>
            <h1 id="C">C</h1>
        </a>
        <td><a href="/index.php" id="C" style="padding:0">
            <h1>C</h1>
        </a>
        </td>
        <td style="width:50%"></td>
        <?php
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != ""){
            echo'
                
                <td style="width:10%"><a href="http://'.$_SERVER['HTTP_HOST'].'/user/account.php">Account</a></td>
                <td style="width:10%"><a href="http://'.$_SERVER['HTTP_HOST'].'/user/logout.php">Logout</a></td>
                ';
        }else{
            echo'
            <td style="width:10%"><a class="logButt" href="http://'.$_SERVER['HTTP_HOST'].'/user/login.php">Log In</a></td>
            <td style="width:10%"><a class="signButt" href="http://'.$_SERVER['HTTP_HOST'].'/user/register.php">Sign In</a></td>';
        }?>
    </tr></table>
</header>
<div class="main">
    <?= $view ?>
</div>
<div style="margin-bottom: 43px;"></div>
<div id="footer">
	<p style="margin: 10px;">©gabrielekuraite - 2024</p>
</div>
</body>
</html>

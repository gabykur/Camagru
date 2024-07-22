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
    <title>Catgram</title>
    <link rel="icon" href="/public/icons/noel.jpg">
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
</head>
<body>
<header>
    <div class="navbar">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != ""): ?>
            <a href="/camera" class="nav-icon camera-logo"><i class="fas fa-camera"></i></a>
        <?php endif; ?>
        <a href="/" id="cam_logo" class="logo">
            <h1>Catgram</h1>
        </a>
        <a href="/" id="c" class="logo">
            <h1>C</h1>
        </a>
        <div class="header-spacer"></div>
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != ""): ?>
            <a class="nav-icon" href="/user/account"><i class="fas fa-user"></i></a>
            <a class="nav-icon" href="/auth/logout"><i class="fas fa-sign-out-alt"></i></a>
        <?php else: ?>
            <a class="nav-button logButt" href="/auth/login">Log In</a>
            <a class="nav-button signButt" href="/auth/register">Sign In</a>
        <?php endif; ?>
    </div>
</header>
<div class="main">
    <?php 
    $viewPath = __DIR__ . "/$view";
    echo "Trying to load view from: $viewPath"; // Debugging statement
    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        echo "View file not found: $viewPath";
    }
    ?>
</div>
<div class="footer-spacer"></div>
<div id="footer">
    <p>©gabrielekuraite - 2024</p>
</div>
</body>
</html>

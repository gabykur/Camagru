<?php
session_start();
$_SESSION['logged'] = "";
header("Location: login.php");
?>
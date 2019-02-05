<?php
session_start();
$_SESSION['loggedin'] = "";
header("Location: ../index.php");
?>
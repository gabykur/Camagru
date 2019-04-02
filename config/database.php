<?php

$DB_DSN = 'mysql:host=localhost; dbname=db_camagru';
$DB_USER = 'root';
$DB_PASSWORD = "123456";
 
try{
    $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
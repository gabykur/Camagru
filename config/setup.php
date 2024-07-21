<?php
$DB_USER = 'root';
$DB_PASSWORD = "123456";

try {
    $db = new PDO("mysql:host=localhost", $DB_USER, $DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = file_get_contents(__DIR__ . '/db_catgram.sql');
    $db->exec($sql);
    echo "Database setup successfully!";
    header("Location:../index.php");
    exit;
} catch (PDOException $e) {
    echo "ERROR: Could not set up database. " . $e->getMessage();
}
?>
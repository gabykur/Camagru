<?php

	$servername = "localhost";
	$username = "root";
	$password = "123456";
	$db = "db_camagru";
	try{
        $pdo = new PDO("mysql:host=$servername;dbname=$db", $username, $passwrod);
        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e)
    {
        die("ERROR: Could not connect. " . $e->getMessage());
    }
?>
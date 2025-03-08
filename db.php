<?php
// db.php

$host = 'localhost'; // Database host
$dbname = 'hotel_booking'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password (leave empty for default in XAMPP/WAMP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("SET NAMES utf8");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
<?php
// config.php - Database connection
$host = '127.0.0.1'; // or localhost
$db = 'fire_safety'; // your database name
$user = 'root'; // your MySQL username (default is 'root' for XAMPP)
$pass = ''; // your MySQL password (leave blank for XAMPP)

try {
    // Initialize the PDO object for the database connection
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

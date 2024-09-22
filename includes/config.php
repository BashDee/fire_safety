<?php
// config.php - Database connection
$host = ''; // or localhost
$db = ''; // your database name
$user = ''; // your MySQL username 
$pass = ''; // your MySQL password 

try {
    // Initialize the PDO object for the database connection
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

<?php
if (file_exists(__DIR__ . "/autoload.php")) {
    require_once __DIR__ . "/autoload.php";
}

// Database connection
try {
    $connection = new PDO("mysql:host=localhost;dbname=unloadingreport", "omar", "f625268f");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

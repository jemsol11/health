<?php
// db_connect.php

$host = "localhost";        // Change if your DB is on another server
$user = "root";             // Your MySQL username (default in XAMPP is "root")
$pass = "";                 // Your MySQL password (default in XAMPP is empty)
$dbname = "barangay_health_center"; // Your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set default timezone to avoid warnings in PHP
date_default_timezone_set("Asia/Manila");
?>

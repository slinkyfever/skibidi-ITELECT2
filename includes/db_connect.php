<?php
$servername = "localhost"; // or 127.0.0.1
$username = "root"; // your MySQL username
$password = ""; // your MySQL password
$database = "ProcureEase"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
$server_name = "localhost";
$username = "root";
$password = "";
$db_name = "it_elect_paradas_db";

// Use object-oriented mysqli so ->prepare() and ->error work as expected
$conn = new mysqli($server_name, $username, $password, $db_name);
if ($conn->connect_error) {
    // Stop execution — a fatal DB connection error should not continue
    die("Database connection failed: " . $conn->connect_error);
}
?>
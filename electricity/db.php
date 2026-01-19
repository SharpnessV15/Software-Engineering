<?php

$host = 'localhost';
$user = 'root';
$password = ''; // Replace with your MySQL root password
$dbname = 'electricity_app';

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
<?php
// Database configuration.
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "workhausdb"; 

// Creating connection.
$conn = new mysqli($servername, $username, $password, $database);

// Checking connection.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "";
}

// closing connection.
$conn->close();
?>

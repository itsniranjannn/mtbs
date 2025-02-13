<?php
// Database configuration
$servername = "localhost"; // Replace with your database server 
$username = "root";        // Replace with your MySQL username
$password = "";            // Replace with your MySQL password
$dbname = "movie_booking"; // Replace with your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
// Database credentials
$host = "localhost";  // or "127.0.0.1"
$user = "root";       // default WAMP user
$pass = "";           // default WAMP password is empty
$dbname = "travelina"; // your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

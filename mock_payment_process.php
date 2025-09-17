<?php
include 'includes/config.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ensure the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];

    // Update the booking in your database to mark it as 'paid'
    $stmt = $conn->prepare("UPDATE bookings SET payment_status = 'paid' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        // Success
        $stmt->close();
        $conn->close();
        header("Location: payment_success.php");
        exit();
    } else {
        // Failure
        $error_message = "Error: Could not update your booking. Please try again.";
        $stmt->close();
        $conn->close();
        die($error_message);
    }
}
?>
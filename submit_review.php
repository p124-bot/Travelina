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
    $item_id = $_POST['item_id'];
    $item_type = $_POST['item_type'];
    $rating = $_POST['rating'];
    $review_text = trim($_POST['review_text']);
    $user_id = $_SESSION['user_id'];

    // Basic validation
    if (empty($item_id) || empty($item_type) || empty($rating)) {
        die("Error: Missing required fields.");
    }

    // Insert the review into the database
    $stmt = $conn->prepare("INSERT INTO reviews (item_id, item_type, user_id, rating, review_text) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isiis", $item_id, $item_type, $user_id, $rating, $review_text);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // Redirect back to the bookings page on success
        header("Location: my_bookings.php");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        die("Error: Could not submit your review. Please try again.");
    }

} else {
    // If not a POST request, redirect to homepage
    header("Location: index.php");
    exit();
}
?>
<?php
include 'includes/config.php';
session_start();

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Use Composer's autoloader
require 'vendor/autoload.php';

// 1. Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Get all the data from the form
    $item_id = $_POST['item_id'];
    $type = $_POST['type'];
    $price_per_day = $_POST['price_per_day'];
    $checkin_date_str = $_POST['checkin_date'];
    $checkout_date_str = $_POST['checkout_date'];
    
    // Get user details from the session
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];

    // 4. Validate and calculate price
    $checkin_date = new DateTime($checkin_date_str);
    $checkout_date = new DateTime($checkout_date_str);
    if ($checkout_date <= $checkin_date) {
        die("Error: Check-out date must be after the check-in date.");
    }
    $interval = $checkin_date->diff($checkout_date);
    $num_days = $interval->days;
    $total_price = $num_days * $price_per_day;

    // 5. Insert the booking into the database
    $stmt = $conn->prepare("INSERT INTO bookings (customer_name, customer_email, type, item_id, checkin_date, checkout_date, total_price) VALUES (?, (SELECT email FROM users WHERE id = ?), ?, ?, ?, ?, ?)");
    $stmt->bind_param("siisssd", $user_name, $user_id, $type, $item_id, $checkin_date_str, $checkout_date_str, $total_price);
    
    if ($stmt->execute()) {
        // --- AUTO EMAIL LOGIC STARTS HERE ---
        
        // Get the new booking ID and customer email
        $new_booking_id = $conn->insert_id;
        $customer_email_stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $customer_email_stmt->bind_param("i", $user_id);
        $customer_email_stmt->execute();
        $customer_email_result = $customer_email_stmt->get_result();
        $customer_email_row = $customer_email_result->fetch_assoc();
        $customer_email = $customer_email_row['email'];
        $customer_email_stmt->close();
        
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            
            // --- YOUR EMAIL CREDENTIALS ---
            $mail->Username   = 'your-email@gmail.com'; // Your website's Gmail address
            $mail->Password   = 'your-16-character-app-password'; // The App Password you generated
            // -----------------------------

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            //Recipients
            $mail->setFrom('your-email@gmail.com', 'TRAVElina Bookings');
            $mail->addAddress($customer_email, $user_name);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'We Received Your Booking Request!';
            $mail->Body    = "Dear " . htmlspecialchars($user_name) . ",<br><br>Thank you for your booking request (#" . $new_booking_id . ") with TRAVElina. We have received it and will review it shortly.<br><br>You will receive another email once your booking is confirmed by our team.<br><br>The TRAVElina Team";
            $mail->AltBody = "Dear " . htmlspecialchars($user_name) . ",\n\nThank you for your booking request (#" . $new_booking_id . ") with TRAVElina. We have received it and will review it shortly.\nYou will receive another email once your booking is confirmed by our team.\n\nThe TRAVElina Team";

            $mail->send();
        } catch (Exception $e) {
            // Optional: Log error if email fails, but don't stop the user
        }
        
        // --- AUTO EMAIL LOGIC ENDS HERE ---
        
        $stmt->close();
        $conn->close();
        header("Location: booking_success.php");
        exit();
        
    } else {
        $error_message = "Error: Could not process your booking. Please try again.";
        $stmt->close();
        $conn->close();
        die($error_message);
    }
} else {
    header("Location: index.php");
    exit();
}
?>
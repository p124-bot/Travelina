<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/config.php';

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Use Composer's autoloader
require '../vendor/autoload.php';

// Handle updating a booking's status
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];
    $customer_email = $_POST['customer_email'];
    $customer_name = $_POST['customer_name'];

    // Update the database
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $booking_id);
    $stmt->execute();
    $stmt->close();

    // If the new status is 'confirmed', send an email
    if ($new_status === 'confirmed') {
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
            $mail->addAddress($customer_email, $customer_name);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Booking is Confirmed!';
            $mail->Body    = "Dear " . htmlspecialchars($customer_name) . ",<br><br>We are pleased to inform you that your booking (#" . $booking_id . ") with TRAVElina has been <strong>confirmed</strong>.<br><br>Thank you for choosing us!<br>The TRAVElina Team";
            $mail->AltBody = "Dear " . htmlspecialchars($customer_name) . ",\n\nWe are pleased to inform you that your booking (#" . $booking_id . ") with TRAVElina has been confirmed.\n\nThank you for choosing us!\nThe TRAVElina Team";

            $mail->send();
        } catch (Exception $e) {
            // You can log the error if sending fails
            // For debugging, you can uncomment the line below
            // die("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
    
    header("Location: manage_bookings.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4 bg-light">
<div class="container-fluid">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Bookings</h1>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </header>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Fetch all bookings, newest first
                $result = $conn->query("SELECT * FROM bookings ORDER BY created_at DESC");
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $status_badge = '';
                        switch ($row['status']) {
                            case 'confirmed':
                                $status_badge = 'bg-success';
                                break;
                            case 'cancelled':
                                $status_badge = 'bg-danger';
                                break;
                            default:
                                $status_badge = 'bg-warning';
                        }
                        echo "
                        <tr>
                            <td>{$row['id']}</td>
                            <td>" . htmlspecialchars($row['customer_name']) . "</td>
                            <td>" . htmlspecialchars($row['customer_email']) . "</td>
                            <td>" . ucfirst(htmlspecialchars($row['type'])) . "</td>
                            <td>" . htmlspecialchars($row['checkin_date']) . "</td>
                            <td>" . htmlspecialchars($row['checkout_date']) . "</td>
                            <td>\${$row['total_price']}</td>
                            <td><span class='badge {$status_badge}'>" . ucfirst(htmlspecialchars($row['status'])) . "</span></td>
                            <td>
                                <form method='POST' class='d-flex'>
                                    <input type='hidden' name='booking_id' value='{$row['id']}'>
                                    <input type='hidden' name='customer_email' value='" . htmlspecialchars($row['customer_email']) . "'>
                                    <input type='hidden' name='customer_name' value='" . htmlspecialchars($row['customer_name']) . "'>
                                    <select name='status' class='form-select form-select-sm me-2'>
                                        <option value='pending' " . ($row['status'] == 'pending' ? 'selected' : '') . ">Pending</option>
                                        <option value='confirmed' " . ($row['status'] == 'confirmed' ? 'selected' : '') . ">Confirmed</option>
                                        <option value='cancelled' " . ($row['status'] == 'cancelled' ? 'selected' : '') . ">Cancelled</option>
                                    </select>
                                    <button type='submit' name='update_status' class='btn btn-primary btn-sm'>Update</button>
                                </form>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>No bookings found.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
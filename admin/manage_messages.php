<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/config.php';


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_messages.php");
    exit();
}


if (isset($_GET['read'])) {
    $id = $_GET['read'];
    $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_messages.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Messages</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4 bg-light">
<div class="container">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Contact Messages</h1>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </header>

    <div class="messages-container">
        <?php
        
        $result = $conn->query("SELECT * FROM messages ORDER BY received_at DESC");
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                
                $card_class = $row['is_read'] ? 'border-light' : 'border-primary border-2';
                echo "
                <div class='card mb-3 {$card_class}'>
                    <div class='card-header d-flex justify-content-between align-items-center'>
                        <div>
                            <strong>" . htmlspecialchars($row['subject']) . "</strong>
                            " . (!$row['is_read'] ? "<span class='badge bg-primary ms-2'>New</span>" : "") . "
                        </div>
                        <small class='text-muted'>" . date("F j, Y, g:i a", strtotime($row['received_at'])) . "</small>
                    </div>
                    <div class='card-body'>
                        <p><strong>From:</strong> " . htmlspecialchars($row['name']) . " (" . htmlspecialchars($row['email']) . ")</p>
                        <p class='card-text'>" . nl2br(htmlspecialchars($row['message'])) . "</p>
                    </div>
                    <div class='card-footer text-end'>
                        " . (!$row['is_read'] ? "<a href='?read={$row['id']}' class='btn btn-success btn-sm'>Mark as Read</a>" : "") . "
                        <a href='?delete={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this message?');\">Delete</a>
                    </div>
                </div>";
            }
        } else {
            echo "<div class='alert alert-info'>You have no messages.</div>";
        }
        ?>
    </div>
</div>
</body>
</html>
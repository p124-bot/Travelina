<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include '../includes/lms_db.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM lecturers WHERE id = ?";
    $stmt = $lms_conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: lecturers.php");
    exit();
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_lecturer'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $sql = "INSERT INTO lecturers (name, email) VALUES (?, ?)";
    $stmt = $lms_conn->prepare($sql);
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    header("Location: lecturers.php");
    exit();
}

// Fetch all lecturers
$lecturers_result = $lms_conn->query("SELECT * FROM lecturers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lecturers</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Manage Lecturers</h1>
        <a href="dashboard.php">Back to Dashboard</a>

        <h2>Add New Lecturer</h2>
        <form action="lecturers.php" method="post">
            <input type="text" name="name" placeholder="Lecturer Name" required>
            <input type="email" name="email" placeholder="Lecturer Email" required>
            <button type="submit" name="add_lecturer">Add Lecturer</button>
        </form>

        <h2>Existing Lecturers</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $lecturers_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td class="actions">
                        <a href="edit_lecturer.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a href="lecturers.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

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
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $lms_conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: students.php");
    exit();
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $sql = "INSERT INTO students (name, email) VALUES (?, ?)";
    $stmt = $lms_conn->prepare($sql);
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    header("Location: students.php");
    exit();
}

// Fetch all students
$students_result = $lms_conn->query("SELECT * FROM students ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Manage Students</h1>
        <a href="dashboard.php">Back to Dashboard</a>

        <h2>Add New Student</h2>
        <form action="students.php" method="post">
            <input type="text" name="name" placeholder="Student Name" required>
            <input type="email" name="email" placeholder="Student Email" required>
            <button type="submit" name="add_student">Add Student</button>
        </form>

        <h2>Existing Students</h2>
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
                <?php while ($row = $students_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td class="actions">
                        <a href="edit_student.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a href="students.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include '../includes/lms_db.php';

$id = $_GET['id'];

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_student'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $sql = "UPDATE students SET name = ?, email = ? WHERE id = ?";
    $stmt = $lms_conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $email, $id);
    $stmt->execute();
    header("Location: students.php");
    exit();
}

// Fetch student data
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $lms_conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Student</h1>
        <a href="students.php">Back to Students List</a>

        <form action="edit_student.php?id=<?php echo $id; ?>" method="post">
            <input type="text" name="name" placeholder="Student Name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
            <input type="email" name="email" placeholder="Student Email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            <button type="submit" name="update_student">Update Student</button>
        </form>
    </div>
</body>
</html>

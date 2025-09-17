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
    $sql = "DELETE FROM courses WHERE id = ?";
    $stmt = $lms_conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: courses.php");
    exit();
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_course'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $lecturer_id = $_POST['lecturer_id'];
    $sql = "INSERT INTO courses (name, description, lecturer_id) VALUES (?, ?, ?)";
    $stmt = $lms_conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $description, $lecturer_id);
    $stmt->execute();
    header("Location: courses.php");
    exit();
}

// Fetch all courses with lecturer names
$courses_sql = "SELECT courses.id, courses.name, courses.description, lecturers.name AS lecturer_name
                FROM courses
                LEFT JOIN lecturers ON courses.lecturer_id = lecturers.id
                ORDER BY courses.id DESC";
$courses_result = $lms_conn->query($courses_sql);

// Fetch all lecturers for the dropdown
$lecturers_result = $lms_conn->query("SELECT * FROM lecturers ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Manage Courses</h1>
        <a href="dashboard.php">Back to Dashboard</a>

        <h2>Add New Course</h2>
        <form action="courses.php" method="post">
            <input type="text" name="name" placeholder="Course Name" required>
            <br><br>
            <textarea name="description" placeholder="Course Description"></textarea>
            <br><br>
            <select name="lecturer_id" required>
                <option value="">Select a Lecturer</option>
                <?php while ($lecturer = $lecturers_result->fetch_assoc()): ?>
                <option value="<?php echo $lecturer['id']; ?>"><?php echo htmlspecialchars($lecturer['name']); ?></option>
                <?php endwhile; ?>
            </select>
            <br><br>
            <button type="submit" name="add_course">Add Course</button>
        </form>

        <h2>Existing Courses</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Lecturer</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $courses_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['lecturer_name']); ?></td>
                    <td class="actions">
                        <a href="edit_course.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a href="courses.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

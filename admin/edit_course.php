<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include '../includes/lms_db.php';

$id = $_GET['id'];

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_course'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $lecturer_id = $_POST['lecturer_id'];
    $sql = "UPDATE courses SET name = ?, description = ?, lecturer_id = ? WHERE id = ?";
    $stmt = $lms_conn->prepare($sql);
    $stmt->bind_param("ssii", $name, $description, $lecturer_id, $id);
    $stmt->execute();
    header("Location: courses.php");
    exit();
}

// Fetch course data
$sql = "SELECT * FROM courses WHERE id = ?";
$stmt = $lms_conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

// Fetch all lecturers for the dropdown
$lecturers_result = $lms_conn->query("SELECT * FROM lecturers ORDER BY name ASC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Course</h1>
        <a href="courses.php">Back to Courses List</a>

        <form action="edit_course.php?id=<?php echo $id; ?>" method="post">
            <input type="text" name="name" placeholder="Course Name" value="<?php echo htmlspecialchars($course['name']); ?>" required>
            <br><br>
            <textarea name="description" placeholder="Course Description"><?php echo htmlspecialchars($course['description']); ?></textarea>
            <br><br>
            <select name="lecturer_id" required>
                <option value="">Select a Lecturer</option>
                <?php while ($lecturer = $lecturers_result->fetch_assoc()): ?>
                <option value="<?php echo $lecturer['id']; ?>" <?php echo ($lecturer['id'] == $course['lecturer_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($lecturer['name']); ?>
                </option>
                <?php endwhile; ?>
            </select>
            <br><br>
            <button type="submit" name="update_course">Update Course</button>
        </form>
    </div>
</body>
</html>

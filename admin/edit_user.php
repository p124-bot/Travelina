<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
include '../includes/db.php';

$id = $_GET['id'];

// Handle update user
if (isset($_POST['update_user'])) {
    $username = $_POST['username'];
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssi", $username, $password, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $role, $id);
    }

    $stmt->execute();
    header("Location: users.php");
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        <a href="users.php">Back to Manage Users</a>

        <form action="edit_user.php?id=<?php echo htmlspecialchars($id); ?>" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
            <label for="password">Password (leave blank to keep current password):</label>
            <input type="password" name="password">
            <label for="role">Role:</label>
            <select name="role">
                <option value="user" <?php if ($row['role'] == 'user') echo 'selected'; ?>>User</option>
                <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
            </select>
            <button type="submit" name="update_user">Update User</button>
        </form>
    </div>
</body>
</html>

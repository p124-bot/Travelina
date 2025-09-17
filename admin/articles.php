<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
include '../includes/db.php';

// Handle delete article
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: articles.php");
}

// Handle add article
if (isset($_POST['add_article'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO articles (title, content, author_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $content, $author_id);
    $stmt->execute();
    header("Location: articles.php");
}

$sql = "SELECT articles.*, users.username FROM articles JOIN users ON articles.author_id = users.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Articles</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Manage Articles</h1>
        <a href="dashboard.php">Back to Dashboard</a>

        <h2>Add Article</h2>
        <form action="articles.php" method="post">
            <label for="title">Title:</label>
            <input type="text" name="title" required>
            <label for="content">Content:</label>
            <textarea name="content" required></textarea>
            <button type="submit" name="add_article">Add Article</button>
        </form>

        <h2>All Articles</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <a href="edit_article.php?id=<?php echo htmlspecialchars($row['id']); ?>">Edit</a>
                        <a href="articles.php?delete=<?php echo htmlspecialchars($row['id']); ?>">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

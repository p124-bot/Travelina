<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
include '../includes/db.php';

$id = $_GET['id'];

// Handle update article
if (isset($_POST['update_article'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("UPDATE articles SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);
    $stmt->execute();
    header("Location: articles.php");
}

$stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
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
    <title>Edit Article</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Article</h1>
        <a href="articles.php">Back to Manage Articles</a>

        <form action="edit_article.php?id=<?php echo htmlspecialchars($id); ?>" method="post">
            <label for="title">Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
            <label for="content">Content:</label>
            <textarea name="content" required><?php echo htmlspecialchars($row['content']); ?></textarea>
            <button type="submit" name="update_article">Update Article</button>
        </form>
    </div>
</body>
</html>

<?php
include(__DIR__ . '/../services/db.php');

// Get destination details
if (!isset($_GET['id'])) {
    die("Invalid request");
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM destinations WHERE id = $id");
$destination = $result->fetch_assoc();

if (!$destination) {
    die("Destination not found");
}

// Update destination
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    $image = $destination['image']; // keep old image
    if (!empty($_FILES['image']['name'])) {
        $targetDir = __DIR__ . '/../uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $image = basename($_FILES['image']['name']);
        $targetFile = $targetDir . $image;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = 'uploads/' . $image;
        }
    }

    $query = "UPDATE destinations SET name=?, location=?, description=?, image=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $name, $location, $description, $image, $id);

    if ($stmt->execute()) {
        header("Location: manage_destinations.php");
        exit;
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Destination</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Edit Destination</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($destination['name']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" value="<?= htmlspecialchars($destination['location']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required><?= htmlspecialchars($destination['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Image</label><br>
            <?php if ($destination['image']) { ?>
                <img src="../<?= $destination['image'] ?>" width="100"><br>
            <?php } ?>
            <input type="file" name="image" class="form-control mt-2">
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="manage_destinations.php" class="btn btn-secondary">Cancel</a>
    </form>
</body>
</html>

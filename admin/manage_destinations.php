<?php
// Include DB connection (adjust path if needed)
include(__DIR__ . '/../services/db.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    // Handle image upload
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = __DIR__ . '/../uploads/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // create uploads folder if not exist
        }

        $image = basename($_FILES['image']['name']);
        $targetFile = $targetDir . $image;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = 'uploads/' . $image; // save relative path
        } else {
            echo "<p style='color:red;'>Failed to upload image.</p>";
            $image = null;
        }
    }

    // Insert into DB
    $query = "INSERT INTO destinations (name, location, description, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $name, $location, $description, $image);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Destination added successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }
}

// Fetch destinations
$result = $conn->query("SELECT * FROM destinations ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Destinations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Manage Destinations</h2>

    <!-- Add Destination Form -->
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Image</label>
            <input type="file" name="image" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Add Destination</button>
    </form>

    <!-- Destinations List -->
    <h3>All Destinations</h3>
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Location</th>
            <th>Description</th>
            <th>Image</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td>
                    <?php if ($row['image']) { ?>
                        <img src="../<?= $row['image'] ?>" width="80">
                    <?php } ?>
                </td>
                <td>
                    <a href="edit_destination.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="delete_destination.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/config.php';

if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $inclusions = $_POST['inclusions'];
    $valid_until = $_POST['valid_until'];
    
    $main_image = uniqid() . '-' . $_FILES['main_image']['name'];
    move_uploaded_file($_FILES['main_image']['tmp_name'], "../images/".$main_image);

   
    $stmt = $conn->prepare("INSERT INTO deals (title, description, price, duration, inclusions, valid_until, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssss", $title, $description, $price, $duration, $inclusions, $valid_until, $main_image);
    $stmt->execute();
    
    
    $new_deal_id = $conn->insert_id;
    $stmt->close();

    
    if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
            $gallery_image_name = uniqid() . '-' . $_FILES['gallery_images']['name'][$key];
            move_uploaded_file($tmp_name, "../images/" . $gallery_image_name);

            $gallery_stmt = $conn->prepare("INSERT INTO service_images (service_id, service_type, image_filename) VALUES (?, 'deal', ?)");
            $gallery_stmt->bind_param("is", $new_deal_id, $gallery_image_name);
            $gallery_stmt->execute();
            $gallery_stmt->close();
        }
    }
    
    header("Location: manage_deals.php");
    exit();
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM deals WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_deals.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Deals</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Deals</h1>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </header>

    <div class="card mb-4">
        <div class="card-header"><h2>Add New Deal / Package</h2></div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3"><input type="text" name="title" class="form-control" placeholder="Package Title" required></div>
                <div class="mb-3"><textarea name="description" class="form-control" placeholder="Description" required></textarea></div>
                <div class="mb-3"><input type="number" step="0.01" name="price" class="form-control" placeholder="Total Price" required></div>
                <div class="mb-3"><input type="text" name="duration" class="form-control" placeholder="Duration (e.g., 3 Days / 2 Nights)" required></div>
                <div class="mb-3"><label class="form-label">Inclusions (comma-separated)</label><textarea name="inclusions" class="form-control" placeholder="e.g., Hotel Stay, Airport Transfer" required></textarea></div>
                <div class="mb-3"><label class="form-label">Valid Until</label><input type="date" name="valid_until" class="form-control" required></div>
                
                <div class="mb-3"><label class="form-label">Main Image</label><input type="file" name="main_image" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Gallery Images (Optional, select multiple)</label><input type="file" name="gallery_images[]" class="form-control" multiple></div>
                
                <button type="submit" name="add" class="btn btn-primary">Add Deal</button>
            </form>
        </div>
    </div>

    <h2>All Deals</h2>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr><th>ID</th><th>Title</th><th>Price</th><th>Duration</th><th>Valid Until</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT * FROM deals ORDER BY id DESC");
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>" . htmlspecialchars($row['title'] ?? '') . "</td>
                <td>$" . htmlspecialchars($row['price'] ?? '0.00') . "</td>
                <td>" . htmlspecialchars($row['duration'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['valid_until'] ?? '') . "</td>
                <td>
                    <a href='edit_deal.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                    <a href='?delete={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure?');\">Delete</a>
                </td>
            </tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
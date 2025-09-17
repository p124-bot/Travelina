<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}






if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/config.php';


if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    
    $main_image_name = $_FILES['main_image']['name'];
    
    $main_image_unique_name = uniqid() . '-' . $main_image_name;
    move_uploaded_file($_FILES['main_image']['tmp_name'], "../images/" . $main_image_unique_name);

    
    $stmt = $conn->prepare("INSERT INTO hotels (name, location, price, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $name, $location, $price, $description, $main_image_unique_name);
    $stmt->execute();
    
    
    $new_hotel_id = $conn->insert_id;
    $stmt->close();

    
    if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
        
        
        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
            
            $gallery_image_name = uniqid() . '-' . $_FILES['gallery_images']['name'][$key];
            move_uploaded_file($tmp_name, "../images/" . $gallery_image_name);

            
            $gallery_stmt = $conn->prepare("INSERT INTO service_images (service_id, service_type, image_filename) VALUES (?, 'hotel', ?)");
            $gallery_stmt->bind_param("is", $new_hotel_id, $gallery_image_name);
            $gallery_stmt->execute();
            $gallery_stmt->close();
        }
    }
    
    header("Location: manage_hotels.php");
    exit();
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    
    $stmt = $conn->prepare("DELETE FROM hotels WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_hotels.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Hotels</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Hotels</h1>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </header>

    <div class="card mb-4">
        <div class="card-header"><h2>Add New Hotel</h2></div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3"><input type="text" name="name" class="form-control" placeholder="Name" required></div>
                <div class="mb-3"><input type="text" name="location" class="form-control" placeholder="Location" required></div>
                <div class="mb-3"><input type="number" step="0.01" name="price" class="form-control" placeholder="Price per night" required></div>
                <div class="mb-3"><textarea name="description" class="form-control" placeholder="Description" required></textarea></div>
                
                <div class="mb-3">
                    <label class="form-label">Main Image</label>
                    <input type="file" name="main_image" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gallery Images (Optional, you can select multiple)</label>
                    <input type="file" name="gallery_images[]" class="form-control" multiple>
                </div>

                <button type="submit" name="add" class="btn btn-primary">Add Hotel</button>
            </form>
        </div>
    </div>

    <h2>All Hotels</h2>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr><th>ID</th><th>Name</th><th>Location</th><th>Price</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT * FROM hotels ORDER BY id DESC");
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
                <td>" . htmlspecialchars($row['location']) . "</td>
                <td>\${$row['price']}</td>
                <td>
                    <a href='edit_hotel.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
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
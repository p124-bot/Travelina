<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/config.php';

$vehicle_id = $_GET['id'];


if (isset($_GET['delete_image'])) {
    $image_id = $_GET['delete_image'];
    $stmt = $conn->prepare("DELETE FROM service_images WHERE id = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $stmt->close();
    header("Location: edit_vehicle.php?id=" . $vehicle_id);
    exit();
}


if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE vehicles SET name = ?, type = ?, price = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssdsi", $name, $type, $price, $description, $vehicle_id);
    $stmt->execute();
    $stmt->close();

    
    if (isset($_FILES['new_main_image']) && $_FILES['new_main_image']['error'] == 0) {
        $new_main_image = uniqid() . '-' . $_FILES['new_main_image']['name'];
        move_uploaded_file($_FILES['new_main_image']['tmp_name'], "../images/" . $new_main_image);
        $img_stmt = $conn->prepare("UPDATE vehicles SET image = ? WHERE id = ?");
        $img_stmt->bind_param("si", $new_main_image, $vehicle_id);
        $img_stmt->execute();
        $img_stmt->close();
    }

    
    if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
            $gallery_image_name = uniqid() . '-' . $_FILES['gallery_images']['name'][$key];
            move_uploaded_file($tmp_name, "../images/" . $gallery_image_name);
            $gallery_stmt = $conn->prepare("INSERT INTO service_images (service_id, service_type, image_filename) VALUES (?, 'vehicle', ?)");
            $gallery_stmt->bind_param("is", $vehicle_id, $gallery_image_name);
            $gallery_stmt->execute();
            $gallery_stmt->close();
        }
    }
    
    header("Location: manage_vehicles.php");
    exit();
}


$stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$vehicle = $stmt->get_result()->fetch_assoc();
$stmt->close();


$gallery_stmt = $conn->prepare("SELECT * FROM service_images WHERE service_id = ? AND service_type = 'vehicle'");
$gallery_stmt->bind_param("i", $vehicle_id);
$gallery_stmt->execute();
$gallery_images = $gallery_stmt->get_result();
$gallery_stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Vehicle</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4 bg-light">
<div class="container">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Vehicle: <?php echo htmlspecialchars($vehicle['name']); ?></h1>
        <a href="manage_vehicles.php" class="btn btn-secondary">← Back to Manage Vehicles</a>
    </header>

    <div class="card">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3"><label>Vehicle Name</label><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($vehicle['name']); ?>" required></div>
                <div class="mb-3"><label>Type</label><input type="text" name="type" class="form-control" value="<?php echo htmlspecialchars($vehicle['type']); ?>" required></div>
                <div class="mb-3"><label>Price per day</label><input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($vehicle['price']); ?>" required></div>
                <div class="mb-3"><label>Description</label><textarea name="description" class="form-control" required><?php echo htmlspecialchars($vehicle['description']); ?></textarea></div>

                <div class="mb-3"><label>Current Main Image</label><div><img src="../images/<?php echo htmlspecialchars($vehicle['image']); ?>" style="max-width: 200px; border-radius: 8px;"></div></div>
                <div class="mb-3"><label>Upload New Main Image (Optional)</label><input type="file" name="new_main_image" class="form-control"></div>
                
                <hr class="my-4">

                <div class="mb-3">
                    <label class="form-label">Current Gallery Images</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php while($img = $gallery_images->fetch_assoc()): ?>
                            <div class="position-relative">
                                <img src="../images/<?php echo htmlspecialchars($img['image_filename']); ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                <a href="?id=<?php echo $vehicle_id; ?>&delete_image=<?php echo $img['id']; ?>" class="btn btn-danger btn-sm position-absolute top-0 end-0" style="padding: 0 .4rem; line-height: 1.2;" onclick="return confirm('Are you sure?');">&times;</a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <div class="mb-3"><label class="form-label">Add More Gallery Images</label><input type="file" name="gallery_images[]" class="form-control" multiple></div>

                <button type="submit" name="update" class="btn btn-primary mt-3">Update Vehicle</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
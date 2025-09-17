<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/config.php';


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    
    header("Location: manage_hotels.php");
    exit();
}
$hotel_id = $_GET['id'];




if (isset($_GET['delete_image'])) {
    $image_to_delete_id = $_GET['delete_image'];
    
    $file_stmt = $conn->prepare("SELECT image_filename FROM service_images WHERE id = ?");
    $file_stmt->bind_param("i", $image_to_delete_id);
    $file_stmt->execute();
    $file_result = $file_stmt->get_result();
    if ($file_row = $file_result->fetch_assoc()) {
        $file_path = "../images/" . $file_row['image_filename'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    $file_stmt->close();

    $stmt = $conn->prepare("DELETE FROM service_images WHERE id = ?");
    $stmt->bind_param("i", $image_to_delete_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: edit_hotel.php?id=" . $hotel_id);
    exit();
}



if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE hotels SET name = ?, location = ?, price = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssdsi", $name, $location, $price, $description, $hotel_id);
    $stmt->execute();
    $stmt->close();

    if (isset($_FILES['new_main_image']) && $_FILES['new_main_image']['error'] == 0) {
        $new_main_image = uniqid() . '-' . $_FILES['new_main_image']['name'];
        move_uploaded_file($_FILES['new_main_image']['tmp_name'], "../images/" . $new_main_image);
        
        $img_stmt = $conn->prepare("UPDATE hotels SET image = ? WHERE id = ?");
        $img_stmt->bind_param("si", $new_main_image, $hotel_id);
        $img_stmt->execute();
        $img_stmt->close();
    }

    if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
            $gallery_image_name = uniqid() . '-' . $_FILES['gallery_images']['name'][$key];
            move_uploaded_file($tmp_name, "../images/" . $gallery_image_name);

            $gallery_stmt = $conn->prepare("INSERT INTO service_images (service_id, service_type, image_filename) VALUES (?, 'hotel', ?)");
            $gallery_stmt->bind_param("is", $hotel_id, $gallery_image_name);
            $gallery_stmt->execute();
            $gallery_stmt->close();
        }
    }
    
    header("Location: manage_hotels.php");
    exit();
}



$stmt = $conn->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
$hotel = $result->fetch_assoc();
$stmt->close();


if (!$hotel) {
    header("Location: manage_hotels.php");
    exit();
}


$gallery_stmt = $conn->prepare("SELECT * FROM service_images WHERE service_id = ? AND service_type = 'hotel'");
$gallery_stmt->bind_param("i", $hotel_id);
$gallery_stmt->execute();
$gallery_images = $gallery_stmt->get_result();
$gallery_stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Hotel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4 bg-light">
<div class="container">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Hotel: <?php echo htmlspecialchars($hotel['name']); ?></h1>
        <a href="manage_hotels.php" class="btn btn-secondary">← Back to Manage Hotels</a>
    </header>

    <div class="card">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($hotel['name']); ?>" required></div>
                <div class="mb-3"><label class="form-label">Location</label><input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($hotel['location']); ?>" required></div>
                <div class="mb-3"><label class="form-label">Price per night</label><input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($hotel['price']); ?>" required></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" required><?php echo htmlspecialchars($hotel['description']); ?></textarea></div>

                <div class="mb-3"><label class="form-label">Current Main Image</label><div><img src="../images/<?php echo htmlspecialchars($hotel['image']); ?>" alt="Current Image" style="max-width: 200px; border-radius: 8px;"></div></div>
                <div class="mb-3"><label class="form-label">Upload New Main Image (Optional)</label><input type="file" name="new_main_image" class="form-control"></div>
                
                <hr class="my-4">

                <div class="mb-3">
                    <label class="form-label">Current Gallery Images</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if ($gallery_images->num_rows > 0): ?>
                            <?php while($img = $gallery_images->fetch_assoc()): ?>
                                <div class="position-relative">
                                    <img src="../images/<?php echo htmlspecialchars($img['image_filename']); ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                    <a href="edit_hotel.php?id=<?php echo $hotel_id; ?>&delete_image=<?php echo $img['id']; ?>" class="btn btn-danger btn-sm position-absolute top-0 end-0" style="padding: 0 .4rem; line-height: 1.2;" onclick="return confirm('Are you sure you want to delete this gallery image?');">&times;</a>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted">No gallery images have been uploaded yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mb-3"><label class="form-label">Add More Gallery Images</label><input type="file" name="gallery_images[]" class="form-control" multiple></div>

                <button type="submit" name="update" class="btn btn-primary mt-3">Update Hotel</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
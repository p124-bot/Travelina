<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/config.php';


if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];
    
    move_uploaded_file($_FILES['image']['tmp_name'], "../images/".$image);

    $stmt = $conn->prepare("INSERT INTO vehicles (name, type, price, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $name, $type, $price, $description, $image);
    $stmt->execute();
    $stmt->close();
    
    header("Location: manage_vehicles.php");
    exit();
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_vehicles.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Vehicles</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Vehicles</h1>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </header>

    <div class="card mb-4">
        <div class="card-header"><h2>Add New Vehicle</h2></div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3"><input type="text" name="name" class="form-control" placeholder="Vehicle Name (e.g., Toyota Prius)" required></div>
                <div class="mb-3"><input type="text" name="type" class="form-control" placeholder="Type (e.g., Car, Van, Bus)" required></div>
                <div class="mb-3"><input type="number" step="0.01" name="price" class="form-control" placeholder="Price per day" required></div>
                <div class="mb-3"><textarea name="description" class="form-control" placeholder="Description" required></textarea></div>
                <div class="mb-3"><label class="form-label">Image</label><input type="file" name="image" class="form-control" required></div>
                <button type="submit" name="add" class="btn btn-primary">Add Vehicle</button>
            </form>
        </div>
    </div>

    <h2>All Vehicles</h2>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr><th>ID</th><th>Name</th><th>Type</th><th>Price</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT * FROM vehicles ORDER BY id DESC");
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
                <td>" . htmlspecialchars($row['type']) . "</td>
                <td>\${$row['price']}</td>
                <td>
    <a href='edit_vehicle.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
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
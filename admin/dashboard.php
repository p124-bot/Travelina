<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}


include '../includes/config.php';


$totalUsers = 0;
$totalBookings = 0;
$totalDestinations = 0;


$result = $conn->query("SELECT COUNT(*) as count FROM users");
if ($result) {
    $totalUsers = $result->fetch_assoc()['count'];
}


$result = $conn->query("SELECT COUNT(*) as count FROM bookings");
if ($result) {
    $totalBookings = $result->fetch_assoc()['count'];
}


$result = $conn->query("SELECT COUNT(*) as count FROM destinations");
if ($result) {
    $totalDestinations = $result->fetch_assoc()['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      background-color: #343a40;
      color: white;
      padding: 20px;
    }
    .sidebar a {
      display: block;
      padding: 10px;
      margin: 5px 0;
      color: #ddd;
      text-decoration: none;
      border-radius: 5px;
    }
    .sidebar a:hover {
      background-color: #495057;
      color: #fff;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">

    <div class="col-md-3 col-lg-2 sidebar">
      <h3 class="text-center">Admin Panel</h3>
      <hr>
    <a href="dashboard.php">🏠 Dashboard</a>
      <a href="manage_destinations.php">📍 Manage Destinations</a>
      <a href="manage_hotels.php">🏨 Manage Hotels</a>
      <a href="manage_vehicles.php">🚐 Manage Vehicles</a>
      <a href="manage_deals.php">🎉 Manage Deals</a>
      <a href="manage_messages.php">📧 Manage Messages</a>
      <a href="manage_bookings.php">📅 Manage Bookings</a>
      <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="col-md-9 col-lg-10 p-4">
      <h2>Welcome, Admin 🎉</h2>
      <p class="text-muted">Here’s an overview of your system.</p>

      <div class="row g-4">
       
        <div class="col-md-4">
          <div class="card p-3 text-center">
            <h4>👤 Users</h4>
            <p class="fs-3 fw-bold"><?= $totalUsers ?></p>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="card p-3 text-center">
            <h4>📅 Bookings</h4>
            <p class="fs-3 fw-bold"><?= $totalBookings ?></p>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="card p-3 text-center">
            <h4>🌍 Destinations</h4>
            <p class="fs-3 fw-bold"><?= $totalDestinations ?></p>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

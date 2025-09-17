<?php
session_start();

// Only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - TRAVElina</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-r from-blue-950 via-blue-900 to-blue-700 flex items-center justify-center">

  <div class="w-full max-w-5xl bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl p-8">
    <h1 class="text-4xl font-bold text-center text-white mb-10">TRAVElina Admin Dashboard</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Hotels -->
      <a href="manage_hotels.php" 
         class="bg-blue-800/60 hover:bg-blue-700/70 text-white rounded-xl shadow-lg p-6 flex flex-col items-center transition">
        <span class="text-2xl mb-2">🏨</span>
        <span class="text-lg font-semibold">Manage Hotels</span>
      </a>

      <!-- Vehicles -->
      <a href="manage_vehicles.php" 
         class="bg-blue-800/60 hover:bg-blue-700/70 text-white rounded-xl shadow-lg p-6 flex flex-col items-center transition">
        <span class="text-2xl mb-2">🚗</span>
        <span class="text-lg font-semibold">Manage Vehicles</span>
      </a>

      <!-- Packages -->
      <a href="manage_packages.php" 
         class="bg-blue-800/60 hover:bg-blue-700/70 text-white rounded-xl shadow-lg p-6 flex flex-col items-center transition">
        <span class="text-2xl mb-2">📦</span>
        <span class="text-lg font-semibold">Manage Packages</span>
      </a>

      <!-- Users -->
      <a href="manage_users.php" 
         class="bg-blue-800/60 hover:bg-blue-700/70 text-white rounded-xl shadow-lg p-6 flex flex-col items-center transition">
        <span class="text-2xl mb-2">👤</span>
        <span class="text-lg font-semibold">Manage Users</span>
      </a>

      <!-- Bookings -->
      <a href="manage_bookings.php" 
         class="bg-blue-800/60 hover:bg-blue-700/70 text-white rounded-xl shadow-lg p-6 flex flex-col items-center transition">
        <span class="text-2xl mb-2">📅</span>
        <span class="text-lg font-semibold">Manage Bookings</span>
      </a>

      <!-- Logout -->
      <a href="../logout.php" 
         class="bg-red-600/80 hover:bg-red-500 text-white rounded-xl shadow-lg p-6 flex flex-col items-center transition">
        <span class="text-2xl mb-2">🚪</span>
        <span class="text-lg font-semibold">Logout</span>
      </a>
    </div>
  </div>

</body>
</html>

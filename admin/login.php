<?php
session_start();


if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | TRAVElina</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600 min-h-screen flex items-center justify-center">

  <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-2xl">
    <h2 class="text-2xl font-bold text-center text-blue-800">🔐 Admin Login</h2>
    <p class="text-gray-500 text-center mb-6">Please login to access the dashboard</p>

    
    <?php if (isset($_GET['error'])): ?>
      <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
        <?= htmlspecialchars($_GET['error']); ?>
      </div>
    <?php endif; ?>

    <form action="login_process.php" method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Username</label>
        <input type="text" name="username" required 
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
      </div>
      
      <div>
        <label class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" required 
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
      </div>

      <button type="submit" 
        class="w-full bg-blue-700 text-white py-2 rounded-lg hover:bg-blue-800 transition">
        Login
      </button>
    </form>
  </div>

</body>
</html>

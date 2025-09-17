<?php
include 'includes/config.php';
session_start();

$error = '';

// If a user is already logged in, redirect them to the homepage
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Both email and password are required.";
    } else {
        // Prepare a statement to get the user by email
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Verify user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
            // SUCCESS: Credentials are valid
            
            // Store user data in the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            
            // Redirect to the homepage
            header("Location: index.php");
            exit();
        } else {
            // FAILURE: Invalid credentials
            $error = "Invalid email or password.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'inc/head.php'; ?>
</head>
<body class="text-gray-200">

  <div class="container mx-auto px-4 py-10">
    <main class="w-full max-w-lg mx-auto p-6 sm:p-10 rounded-2xl shadow-2xl space-y-8 glass-container">

      <header class="text-center">
        <h1 class="text-4xl font-bold text-white">Welcome Back</h1>
        <p class="text-gray-300 mt-2">Log in to your TRAVElina account.</p>
      </header>
      
      <section>
        <?php if ($error): ?>
          <div class="bg-red-500/30 text-white p-3 rounded-lg text-center mb-6">
            <p><?php echo $error; ?></p>
          </div>
        <?php endif; ?>

        <form action="login.php" method="post" class="space-y-6">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-300">Email Address</label>
            <input type="email" id="email" name="email" required
                   class="mt-1 block w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-400 outline-none">
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
            <input type="password" id="password" name="password" required
                   class="mt-1 block w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-400 outline-none">
          </div>
          <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg shadow-lg font-semibold transition">
            Log In
          </button>
        </form>
        <p class="text-center text-sm text-gray-400 mt-6">
          Don't have an account? <a href="register.php" class="font-semibold text-blue-300 hover:underline">Register here</a>.
        </p>
      </section>

    </main>
  </div>
</body>
</html>
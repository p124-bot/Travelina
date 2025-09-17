<?php
include 'includes/config.php';
session_start();

$error = '';
$success = false;

// If a user is already logged in, redirect them to the homepage
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "An account with this email already exists.";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $insert_stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($insert_stmt->execute()) {
                $success = true;
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $insert_stmt->close();
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
        <h1 class="text-4xl font-bold text-white">Create an Account</h1>
        <p class="text-gray-300 mt-2">Join TRAVElina to unlock exclusive deals.</p>
      </header>
      
      <?php if ($success): ?>
        <div class="bg-green-500/30 text-white p-4 rounded-lg text-center">
            <p><strong>Registration Successful!</strong> You can now <a href="login.php" class="font-bold underline hover:text-blue-300">log in</a>.</p>
        </div>
      <?php else: ?>
        <section>
          <?php if ($error): ?>
            <div class="bg-red-500/30 text-white p-3 rounded-lg text-center mb-6">
              <p><?php echo $error; ?></p>
            </div>
          <?php endif; ?>

          <form action="register.php" method="post" class="space-y-6">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-300">Full Name</label>
              <input type="text" id="name" name="name" required
                     class="mt-1 block w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-400 outline-none">
            </div>
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
              Create Account
            </button>
          </form>
          <p class="text-center text-sm text-gray-400 mt-6">
            Already have an account? <a href="login.php" class="font-semibold text-blue-300 hover:underline">Log in here</a>.
          </p>
        </section>
      <?php endif; ?>

    </main>
  </div>
</body>
</html>
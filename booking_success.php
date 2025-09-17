<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'inc/head.php'; ?>
</head>
<body class="text-gray-200">

  <div class="container mx-auto px-4 py-10">
    <main class="w-full max-w-2xl mx-auto p-6 sm:p-10 rounded-2xl shadow-2xl glass-container text-center">
    
      <?php include 'inc/header.php'; ?>

      <section class="mt-10">
        <h1 class="text-4xl font-bold text-white">🎉 Booking Request Sent!</h1>
        <p class="text-gray-300 mt-4">
          Thank you, <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?>! 
          Your booking request has been received. We will review it and contact you shortly to confirm the details.
        </p>
        <a href="index.php" class="mt-8 inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition">
          Back to Homepage
        </a>
      </section>
      
    </main>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>
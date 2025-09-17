<?php
include 'includes/config.php';
$message_sent = false; // A flag to check if the message was sent

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    // Prepare a secure statement to insert the message into the database
    $stmt = $conn->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    
    // If the insertion is successful, set the flag to true
    if ($stmt->execute()) {
        $message_sent = true;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'inc/head.php'; ?>
</head>
<body class="text-gray-200">

  <div class="container mx-auto px-4 py-10">
    <main class="w-full max-w-4xl mx-auto p-6 sm:p-10 rounded-2xl shadow-2xl space-y-8 glass-container">

      <?php include 'inc/header.php'; ?>
      
      <?php if ($message_sent): ?>
        <div class="bg-green-500/30 text-white p-4 rounded-lg text-center">
            <p><strong>Thank You!</strong> Your message has been sent successfully. We'll get back to you shortly.</p>
        </div>
      <?php else: ?>
        <section>
          <form action="contact.php" method="post" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
                <input type="text" id="name" name="name" required
                       class="mt-1 block w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-400 outline-none">
              </div>
              <div>
                <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                <input type="email" id="email" name="email" required
                       class="mt-1 block w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-400 outline-none">
              </div>
            </div>
            <div>
              <label for="subject" class="block text-sm font-medium text-gray-300">Subject</label>
              <input type="text" id="subject" name="subject" required
                     class="mt-1 block w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-400 outline-none">
            </div>
            <div>
              <label for="message" class="block text-sm font-medium text-gray-300">Message</label>
              <textarea id="message" name="message" rows="5" required
                        class="mt-1 block w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-400 outline-none"></textarea>
            </div>
            <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg shadow-lg font-semibold transition">
              Send Message
            </button>
          </form>
        </section>
      <?php endif; ?>

    </main>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>
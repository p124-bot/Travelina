<?php
include 'includes/config.php';
session_start();

if (!isset($_GET['type']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$type = $_GET['type'];
$id = $_GET['id'];
$table = '';
$item = null;

// Determine table and fetch item name
if ($type === 'hotel') { $table = 'hotels'; }
elseif ($type === 'vehicle') { $table = 'vehicles'; }
elseif ($type === 'deal') { $table = 'deals'; }
else { header("Location: index.php"); exit(); }

$stmt = $conn->prepare("SELECT name, title FROM {$table} WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$itemName = $item['name'] ?? $item['title'] ?? 'Service';
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'inc/head.php'; ?>
  <style>.stars { color: #FFC107; }</style>
</head>
<body class="text-gray-200">

  <div class="container mx-auto px-4 py-10">
    <main class="w-full max-w-4xl mx-auto p-6 sm:p-10 rounded-2xl shadow-2xl glass-container">
      <?php include 'inc/header.php'; ?>
      <section class="mt-10">
        <header>
            <h1 class="text-4xl font-bold text-white">Reviews for <?php echo htmlspecialchars($itemName); ?></h1>
            <a href="services/<?php echo $type; ?>s.php" class="text-blue-300 hover:underline mt-2 inline-block">&larr; Back to all <?php echo $type; ?>s</a>
        </header>

        <div class="space-y-6 mt-8">
            <?php
            // Fetch all reviews for this item, joining with the users table to get the reviewer's name
            $review_stmt = $conn->prepare("SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.item_id = ? AND r.item_type = ? ORDER BY r.created_at DESC");
            $review_stmt->bind_param("is", $id, $type);
            $review_stmt->execute();
            $reviews = $review_stmt->get_result();

            if ($reviews->num_rows > 0) {
                while($review = $reviews->fetch_assoc()) {
                    echo "
                    <div class='bg-white/5 p-4 rounded-lg'>
                        <div class='flex justify-between items-center'>
                            <h4 class='font-semibold text-white'>" . htmlspecialchars($review['user_name']) . "</h4>
                            <span class='text-xs text-gray-400'>" . date("F j, Y", strtotime($review['created_at'])) . "</span>
                        </div>
                        <div class='stars mt-1'>";
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= $review['rating'] ? '<i class=\"fas fa-star\"></i>' : '<i class=\"far fa-star\"></i>';
                    }
                    echo "</div>
                        <p class='text-gray-300 mt-2 text-sm'>" . nl2br(htmlspecialchars($review['review_text'])) . "</p>
                    </div>";
                }
            } else {
                echo "<p class='text-center text-gray-400'>There are no reviews for this item yet.</p>";
            }
            $review_stmt->close();
            ?>
        </div>
      </section>
    </main>
  </div>
  <?php include 'footer.php'; ?>
</body>
</html>
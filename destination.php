<?php
// connect to database
include "services/db.php";  // make sure db.php is in services/ folder

// Get search keyword
$search = isset($_GET['deal']) ? trim($_GET['deal']) : "";

// Prepare query
if ($search !== "") {
    $stmt = $conn->prepare("SELECT * FROM destinations WHERE name LIKE ? OR location LIKE ?");
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM destinations ORDER BY created_at DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Results</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
  <div class="container mx-auto px-6 py-12">
    <h1 class="text-3xl font-bold text-center mb-4">Search Results</h1>
    <p class="text-center mb-8">
      <?php if ($search !== ""): ?>
        Showing results for: <span class="text-blue-400">"<?php echo htmlspecialchars($search); ?>"</span>
      <?php else: ?>
        Showing all available destinations.
      <?php endif; ?>
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="bg-gray-800 rounded-xl shadow-lg p-4">
            <?php if (!empty($row['image'])): ?>
              <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" 
                   alt="<?php echo htmlspecialchars($row['name']); ?>" 
                   class="w-full h-48 object-cover rounded-lg mb-3">
            <?php endif; ?>

            <h2 class="text-xl font-semibold text-blue-300 mb-1">
              <?php echo htmlspecialchars($row['name']); ?>
            </h2>
            <p class="text-sm text-gray-400 mb-2"><?php echo htmlspecialchars($row['location']); ?></p>
            <p class="text-gray-300 text-sm mb-4"><?php echo htmlspecialchars($row['description']); ?></p>

            <a href="#" class="block text-center bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg">
              Book Now
            </a>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="col-span-4 text-center text-gray-400">No destinations found.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>


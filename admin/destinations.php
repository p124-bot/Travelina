<?php
include __DIR__ . "/../services/db.php";

// Get search term
$search = isset($_GET['deal']) ? trim($_GET['deal']) : "";

// Prepare query
if ($search !== "") {
    $stmt = $conn->prepare("SELECT * FROM destinations WHERE name LIKE ? OR location LIKE ?");
    $like = "%" . $search . "%";
    $stmt->bind_param("ss", $like, $like);
} else {
    $stmt = $conn->prepare("SELECT * FROM destinations ORDER BY created_at DESC");
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Results - TRAVELina</title>
  <link rel="stylesheet" href="../css/blog.css"> <!-- blog style -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white">

  <div class="max-w-6xl mx-auto px-6 py-12">
    <h1 class="text-4xl font-bold text-center mb-4">Search Results</h1>
    <?php if ($search !== ""): ?>
      <p class="text-center mb-8">Showing results for: <span class="text-blue-400">"<?php echo htmlspecialchars($search); ?>"</span></p>
    <?php else: ?>
      <p class="text-center mb-8">Showing all available destinations.</p>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="card bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-2xl transition">
            <img src="../<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="rounded-lg mb-4 w-full h-48 object-cover">
            <h2 class="text-xl font-semibold text-blue-300 mb-2"><?php echo htmlspecialchars($row['name']); ?></h2>
            <p class="text-gray-400 mb-2"><i class="fas fa-map-marker-alt text-red-400"></i> <?php echo htmlspecialchars($row['location']); ?></p>
            <p class="text-sm text-gray-300 mb-4"><?php echo substr(htmlspecialchars($row['description']), 0, 120) . "..."; ?></p>
            
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="col-span-3 text-center text-gray-400">No destinations found.</p>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>





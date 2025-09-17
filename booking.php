<?php
include 'includes/config.php';
session_start();

if (!isset($_GET['type']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$type = $_GET['type'];
$id = $_GET['id'];
$item = null;
$table = '';

if ($type === 'hotel') { $table = 'hotels'; }
elseif ($type === 'vehicle') { $table = 'vehicles'; }
elseif ($type === 'deal') { $table = 'deals'; }
else { header("Location: index.php"); exit(); }

// Fetch the main item details
$stmt = $conn->prepare("SELECT * FROM {$table} WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    header("Location: index.php");
    exit();
}

// Fetch gallery images for this item
$gallery_stmt = $conn->prepare("SELECT * FROM service_images WHERE service_id = ? AND service_type = ?");
$gallery_stmt->bind_param("is", $id, $type);
$gallery_stmt->execute();
$gallery_images = $gallery_stmt->get_result();
$gallery_stmt->close();

// Handle different column names for display
$itemName = $item['name'] ?? $item['title'] ?? 'N/A';
$itemLocation = $item['location'] ?? 'Package Deal';
$itemPrice = $item['price'] ?? '0.00';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'inc/head.php'; ?>
</head>
<body class="text-gray-200">
  <div class="container mx-auto px-4 py-10">
    <main class="w-full max-w-4xl mx-auto p-6 sm:p-10 rounded-2xl shadow-2xl glass-container">
      <?php include 'inc/header.php'; ?>
      <section class="mt-10">
        <div class="grid md:grid-cols-2 gap-8">
          <div>
            <h1 class="text-3xl font-bold text-white"><?php echo htmlspecialchars($itemName); ?></h1>
            <p class="text-blue-300 mt-1"><?php echo htmlspecialchars($itemLocation); ?></p>
            
            <img src="images/<?php echo htmlspecialchars($item['image'] ?? ''); ?>" alt="<?php echo htmlspecialchars($itemName); ?>" class="rounded-lg shadow-lg w-full mt-4">

            <?php if ($gallery_images->num_rows > 0): ?>
            <div class="mt-4">
                <h3 class="text-xl font-semibold text-white mb-2">Photo Gallery</h3>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                    <?php while($img = $gallery_images->fetch_assoc()): ?>
                        <a href="images/<?php echo htmlspecialchars($img['image_filename']); ?>" data-lightbox="service-gallery" data-title="<?php echo htmlspecialchars($itemName); ?>">
                            <img src="images/<?php echo htmlspecialchars($img['image_filename']); ?>" alt="Gallery Image" class="rounded-md w-full h-24 object-cover cursor-pointer hover:opacity-80 transition">
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>

            <p class="mt-4 text-gray-300"><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>
          </div>

          <div class="bg-white/5 p-6 rounded-lg">
            <?php if (isset($_SESSION['user_id'])): ?>
              <h2 class="text-2xl font-semibold text-white mb-4">Confirm Your Booking</h2>
              <form action="booking_process.php" method="post">
                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                <input type="hidden" name="type" value="<?php echo $type; ?>">
                <input type="hidden" id="price_per_day" name="price_per_day" value="<?php echo $itemPrice; ?>">
                <div class="space-y-4">
                  <div>
                    <label for="checkin_date" class="block text-sm font-medium text-gray-300">Start Date</label>
                    <input type="date" id="checkin_date" name="checkin_date" required class="mt-1 block w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white">
                  </div>
                  <div>
                    <label for="checkout_date" class="block text-sm font-medium text-gray-300">End Date</label>
                    <input type="date" id="checkout_date" name="checkout_date" required class="mt-1 block w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white">
                  </div>
                  
                  <div id="price_display" class="bg-black/20 p-4 rounded-lg text-center hidden">
                      <p class="text-gray-400 text-sm">Total Price</p>
                      <p id="total_price" class="text-2xl font-bold text-white"></p>
                  </div>

                  <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg shadow-lg font-semibold transition">
                    Submit Booking Request
                  </button>
                </div>
              </form>
            <?php else: ?>
              <h2 class="text-2xl font-semibold text-white mb-4">Log In to Book</h2>
              <p class="text-gray-300 mb-6">You need to be logged in to book this item.</p>
              <a href="login.php" class="w-full block text-center bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg shadow-lg font-semibold transition">
                Log In or Register
              </a>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </main>
  </div>
  <?php include 'footer.php'; ?>

  <script>
    const checkinDate = document.getElementById('checkin_date');
    const checkoutDate = document.getElementById('checkout_date');
    const pricePerDay = parseFloat(document.getElementById('price_per_day').value);
    const priceDisplay = document.getElementById('price_display');
    const totalPriceElem = document.getElementById('total_price');

    checkinDate.addEventListener('change', () => {
      checkoutDate.min = checkinDate.value;
      calculatePrice(); 
    });
    checkoutDate.addEventListener('change', calculatePrice);

    function calculatePrice() {
      const bookingType = document.querySelector('input[name="type"]').value;
      
      if (bookingType === 'deal') {
        totalPriceElem.textContent = '$' + pricePerDay.toFixed(2);
        priceDisplay.classList.remove('hidden');
        return;
      }

      const startDate = new Date(checkinDate.value);
      const endDate = new Date(checkoutDate.value);

      if (checkinDate.value && checkoutDate.value && endDate > startDate) {
        const timeDiff = endDate.getTime() - startDate.getTime();
        const numDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
        const total = numDays * pricePerDay;
        totalPriceElem.textContent = '$' + total.toFixed(2);
        priceDisplay.classList.remove('hidden');
      } else {
        priceDisplay.classList.add('hidden');
      }
    }
  </script>
</body>
</html>
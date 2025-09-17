<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'inc/head.php'; ?>
  <style>
    /* Basic styles for the modal */
    .modal { display: none; }
    .modal.is-active { display: flex; }
  </style>
</head>
<body class="text-gray-200">
  <div class="container mx-auto px-4 py-10">
    <main class="w-full max-w-5xl mx-auto p-6 sm:p-10 rounded-2xl shadow-2xl glass-container">
      <?php include 'inc/header.php'; ?>
      <section class="mt-10">
        <header class="text-center">
            <h1 class="text-4xl font-bold text-white">My Bookings</h1>
            <p class="text-gray-300 mt-2">Here is a history of all your booking requests with TRAVElina.</p>
        </header>
        <div class="mt-8 overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-white/10">
                    <tr>
                        <th class="p-3">Booking ID</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Dates</th>
                        <th class="p-3">Price</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $stmt = $conn->prepare("
                    SELECT b.*, r.id as review_id FROM bookings b 
                    LEFT JOIN reviews r ON b.id = r.item_id AND r.item_type = b.type AND r.user_id = ?
                    WHERE b.customer_email = (SELECT email FROM users WHERE id = ?) 
                    ORDER BY b.created_at DESC");
                $stmt->bind_param("ii", $user_id, $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $status_badge = '';
                        switch ($row['status']) {
                            case 'confirmed': $status_badge = 'bg-green-500/80'; break;
                            case 'cancelled': $status_badge = 'bg-red-500/80'; break;
                            default: $status_badge = 'bg-yellow-500/80';
                        }
                        echo "
                        <tr class='border-b border-white/10'>
                            <td class='p-3'>#{$row['id']}</td>
                            <td class='p-3 capitalize'>" . htmlspecialchars($row['type']) . "</td>
                            <td class='p-3'>" . htmlspecialchars($row['checkin_date']) . " to " . htmlspecialchars($row['checkout_date']) . "</td>
                            <td class='p-3'>\${$row['total_price']}</td>
                            <td class='p-3'><span class='px-2 py-1 rounded-full text-xs font-semibold {$status_badge}'>" . ucfirst(htmlspecialchars($row['status'])) . "</span></td>
                            <td class='p-3'>";

                        // --- Action button logic ---
                        if ($row['status'] == 'confirmed' && $row['payment_status'] == 'unpaid') {echo "<a href='mock_payment.php?booking_id={$row['id']}' class='bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1 rounded'>Pay Now</a>";
                            // If booking is confirmed but unpaid, show "Pay Now"
                            
                        } elseif ($row['status'] == 'confirmed' && is_null($row['review_id']) && $row['payment_status'] == 'paid') {
                            // If booking is paid and not reviewed, show "Leave a Review"
                            echo "<button onclick=\"openReviewModal('{$row['id']}', '{$row['type']}')\" class='bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded'>Leave a Review</button>";
                        } elseif (!is_null($row['review_id'])) {
                            // If it has been reviewed, show "Reviewed"
                            echo "<span class='text-gray-400 text-xs'>Reviewed</span>";
                        } elseif ($row['payment_status'] == 'paid') {
                            // If it's paid but not yet eligible for review (e.g., trip date is in the future), show "Paid"
                            echo "<span class='text-green-400 text-xs'>Paid</span>";
                        }
                        
                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center p-4 text-gray-400'>You have not made any bookings yet.</td></tr>";
                }
                $stmt->close();
                ?>
                </tbody>
            </table>
        </div>
      </section>
    </main>
  </div>

  <div id="reviewModal" class="modal fixed inset-0 bg-black/60 items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md">
      <h2 class="text-2xl font-bold text-white mb-4">Leave a Review</h2>
      <form action="submit_review.php" method="post">
        <input type="hidden" name="item_id" id="modal_item_id">
        <input type="hidden" name="item_type" id="modal_item_type">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-300">Rating (1-5)</label>
          <select name="rating" class="mt-1 block w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white" required>
            <option value="5">5 Stars (Excellent)</option>
            <option value="4">4 Stars (Great)</option>
            <option value="3">3 Stars (Good)</option>
            <option value="2">2 Stars (Fair)</option>
            <option value="1">1 Star (Poor)</option>
          </select>
        </div>
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-300">Your Review</label>
          <textarea name="review_text" rows="4" class="mt-1 block w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white" placeholder="Share your experience..."></textarea>
        </div>
        <div class="flex justify-end gap-4">
          <button type="button" onclick="closeReviewModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Cancel</button>
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Submit Review</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const modal = document.getElementById('reviewModal');
    function openReviewModal(itemId, itemType) {
      document.getElementById('modal_item_id').value = itemId;
      document.getElementById('modal_item_type').value = itemType;
      modal.classList.add('is-active');
    }
    function closeReviewModal() {
      modal.classList.remove('is-active');
    }
  </script>
</body>
</html>
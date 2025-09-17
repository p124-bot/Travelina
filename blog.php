<?php include 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'inc/head.php'; ?>
</head>
<body class="text-gray-200">

  <div class="container mx-auto px-4 py-10">
    <main class="w-full max-w-6xl mx-auto p-6 sm:p-10 rounded-2xl shadow-2xl space-y-12 glass-container">

     <?php include 'inc/header.php'; ?>

      <section class="grid md:grid-cols-2 gap-8 items-center">
        <div class="order-2 md:order-1 space-y-4">
          <h2 class="text-3xl font-semibold text-blue-300">Adventure Awaits</h2>
          <p class="text-gray-300">
            Sri Lanka is a paradise for adventure lovers. From hiking the scenic trails of Ella and climbing the famous Sigiriya Rock Fortress 
            to white-water rafting in Kitulgala, the island is packed with excitement.
          </p>
          <p class="text-gray-300">
            One of the most thrilling experiences is <span class="font-semibold text-white">"Flying Ravana"</span> in Ella, a mega zipline stretching over half a kilometer. 
            Glide at speeds of up to 80 km/h while enjoying breathtaking views of Little Adam’s Peak.
          </p>
        </div>
        <div class="order-1 md:order-2">
          <img src="images/flying.webp" alt="Flying Ravana Zipline" class="rounded-lg shadow-lg w-full h-full object-cover">
        </div>
      </section>

      <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 pt-8">
        <div class="p-6 rounded-xl glass-container hover:bg-white/20 transition">
            <img src="images/beaches.webp" alt="Beaches" class="rounded-md mb-4 w-full h-40 object-cover">
            <h3 class="text-xl font-semibold text-blue-300 mb-2">Golden Beaches 🏖️</h3>
            <p class="text-gray-300 text-sm">World-famous for golden sands, clear waters, and perfect spots for surfing and swimming.</p>
        </div>
        <div class="p-6 rounded-xl glass-container hover:bg-white/20 transition">
            <img src="images/forests.webp" alt="Forests" class="rounded-md mb-4 w-full h-40 object-cover">
            <h3 class="text-xl font-semibold text-blue-300 mb-2">Lush Forests 🌿</h3>
            <p class="text-gray-300 text-sm">Rich in biodiversity, offering exotic wildlife and peaceful nature trails for exploration.</p>
        </div>
        <div class="p-6 rounded-xl glass-container hover:bg-white/20 transition">
            <img src="images/parana.webp" alt="Ancient places" class="rounded-md mb-4 w-full h-40 object-cover">
            <h3 class="text-xl font-semibold text-blue-300 mb-2">Ancient Wonders</h3>
            <p class="text-gray-300 text-sm">Sites like Sigiriya and Polonnaruwa showcase the island’s rich history and architecture.</p>
        </div>
      </section>

    </main>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>
<?php include 'includes/config.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>

  <?php include 'inc/head.php'; ?>
</head>
<body class="text-gray-200">

  <?php include 'inc/header.php'; ?>

  <section class="hero-main text-blue-300">
  <div class="max-w-xl text-center">
    <h1 class="text-white">Your Next Adventure Awaits</h1>
    
    <p class="mt-6 text-black hero-subheading">
        Discover Sri Lanka like never before. Unforgettable journeys, curated just for you
    </p>

    <div class="mt-10">
        <a href="services/deals.php" class="btn btn-primary">
            Explore Packages
        </a>
    </div>
</div>
      </div>
  </section>

  <div class="container mx-auto px-4 py-16">
    <main class="w-full max-w-7xl mx-auto space-y-16">

      <section>
        <h2 class="section-title text-white">Explore Our Services</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
          <a href="services/hotels.php" class="block p-8 rounded-xl glass-container hover:bg-white/20 transition service-card-pop">
            <h3 class="text-5xl font-semibold text-blue-300 mb-2">🏨 Hotels</h3>
            <p class="text-gray-300">Stay in luxury hotels and resorts across Sri Lanka.</p>
          </a>
          <a href="services/vehicles.php" class="block p-8 rounded-xl glass-container hover:bg-white/20 transition service-card-pop">
            <h3 class="text-5xl font-semibold text-blue-300 mb-2">🚐 Vehicles</h3>
            <p class="text-gray-300">Travel in style with our premium cars, vans, and buses.</p>
          </a>
          <a href="services/deals.php" class="block p-8 rounded-xl glass-container hover:bg-white/20 transition service-card-pop">
            <h3 class="text-5xl font-semibold text-blue-300 mb-2">🎉 Deals</h3>
            <p class="text-gray-300">Find exclusive travel packages curated just for you.</p>
          </a>
        </div>
      </section>

      <section class="py-30">
 <div class="max-w-xl mx-auto text-center">
  <h2 class="section-title text-blue-300">Find Your Perfect Trip</h2>
  <form action="admin/destinations.php" method="get" class="w-full p-6 rounded-lg glass-container mt-6 flex flex-wrap gap-4 items-center bg-gray-900/60 backdrop-blur-md shadow-lg">
    
    <!-- Input -->
    <div class="flex items-center flex-1 min-w-[200px] border-b border-white/30 py-2">
      <i class="fas fa-search text-blue-300 mr-3"></i>
      <input 
        type="text" 
        name="deal" 
        placeholder="Search destinations..." 
        class="w-full bg-transparent text-white placeholder-gray-400 outline-none"
        required
      >
    </div>

    <!-- Button -->
    
    
    <button type="submit" 
      class="bg-yellow-500 hover:bg-yellow-600 text-black font-semibold px-6 py-2 rounded-full shadow-md transition">
      Search
    </button>
  </form>
</div>


      </section>

    </main>
  </div>

  <?php include 'footer.php'; ?>

</body>
</html>
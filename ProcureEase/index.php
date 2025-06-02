<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ProcureEase</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- AOS Animation CSS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
</head>
<body class="min-h-screen bg-white text-gray-800">

  <!-- Navbar (No Animation) -->
  <header class="flex justify-between items-center p-2 bg-gray-800 text-amber-500 shadow-lg">
    <div class="text-2xl font-extrabold">ProcureEase</div>
    <div class="flex items-center gap-6">
      <span class="text-sm text-amber-300">Simplifying Government Procurement</span>
      <button onclick="window.location.href='login.php'" class="px-6 py-2 border-2 border-amber-500 text-amber-500 rounded-md hover:bg-amber-500 hover:text-white transition duration-300">
        Login/Sign Up
      </button>
    </div>
  </header>

  <!-- Hero Section (Background not animated) -->
  <main class="flex items-center justify-center h-screen bg-cover bg-center" style="background-image: url('./image/modern-nyc-interior-architecture.jpg');">
    <div class="text-center text-white p-6 max-w-lg space-y-6 bg-opacity-70 bg-black rounded-lg">
      <h1 class="text-5xl font-extrabold text-amber-500 leading-tight" data-aos="fade-right">
        Streamlining Procurement for the Government
      </h1>
      <p class="text-lg text-gray-300" data-aos="fade-left" data-aos-delay="200">
        Transform the way local suppliers and government agencies work together with enhanced transparency, efficiency, and security.
      </p>
      <p class="text-sm text-gray-200" data-aos="fade-up" data-aos-delay="400">
        A unified platform for seamless interactions, ensuring a smooth procurement journey every time.
      </p>
      <button onclick="window.location.href='login.php'" class="mt-6 px-8 py-3 bg-amber-500 text-white font-semibold rounded-lg shadow-lg hover:bg-amber-600 transition duration-300 transform hover:scale-105" data-aos="zoom-in" data-aos-delay="100">
        Get Started Now
      </button>
    </div>
  </main>

  <!-- Key Features Section -->
  <section class="py-16 bg-gradient-to-r from-gray-100 via-gray-200 to-amber-50 text-gray-800" data-aos="fade-up">
    <div class="text-center space-y-6">
      <h2 class="text-4xl font-extrabold text-amber-500" data-aos="zoom-in">Key Features of ProcureEase</h2>
      <p class="max-w-xl mx-auto text-lg text-gray-600" data-aos="fade-up" data-aos-delay="200">
        Our platform offers powerful tools designed to improve transparency, simplify communication, and secure your procurement process.
      </p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 px-4 mt-12">
      <div class="bg-amber-500 text-white p-6 rounded-lg shadow-lg transform hover:scale-105 transition duration-300" data-aos="flip-left">
        <h3 class="text-xl font-semibold">Real-Time Updates</h3>
        <p class="mt-2 text-gray-200">Track every step of the procurement process with live updates, ensuring that all parties stay informed.</p>
      </div>
      <div class="bg-amber-500 text-white p-6 rounded-lg shadow-lg transform hover:scale-105 transition duration-300" data-aos="flip-up" data-aos-delay="200">
        <h3 class="text-xl font-semibold">Secure Transactions</h3>
        <p class="mt-2 text-gray-200">Our platform uses state-of-the-art encryption to ensure that all transactions are secure and private.</p>
      </div>
      <div class="bg-amber-500 text-white p-6 rounded-lg shadow-lg transform hover:scale-105 transition duration-300" data-aos="flip-right" data-aos-delay="400">
        <h3 class="text-xl font-semibold">Efficient Communication</h3>
        <p class="mt-2 text-gray-200">A robust messaging system to facilitate direct communication between suppliers and government agencies.</p>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="bg-gray-800 text-white py-16" data-aos="fade-up">
    <div class="text-center space-y-6">
      <h2 class="text-4xl font-extrabold text-amber-500" data-aos="zoom-in">What Our Clients Say</h2>
      <p class="max-w-xl mx-auto text-lg text-gray-300" data-aos="fade-up" data-aos-delay="200">
        Hear from some of the organizations that trust ProcureEase for their procurement needs.
      </p>
    </div>
    <div class="flex flex-col md:flex-row justify-center gap-8 mt-12 px-4">
      <div class="w-full md:w-1/3 bg-white p-6 rounded-lg shadow-lg" data-aos="fade-right" data-aos-delay="300">
        <p class="text-gray-800 italic">"ProcureEase has transformed the way we handle procurement. The transparency and ease of use are unmatched."</p>
        <div class="mt-4 text-gray-600 font-semibold">John Doe</div>
        <p class="text-gray-500">Local Supplier</p>
      </div>
      <div class="w-full md:w-1/3 bg-white p-6 rounded-lg shadow-lg" data-aos="fade-left" data-aos-delay="500">
        <p class="text-gray-800 italic">"Our government agency has saved so much time and money using ProcureEase. The platform is both secure and user-friendly."</p>
        <div class="mt-4 text-gray-600 font-semibold">Jane Smith</div>
        <p class="text-gray-500">Government Agency</p>
      </div>
    </div>
  </section>

  <!-- Footer (No Animation) -->
  <footer class="bg-gray-800 text-amber-500 py-6 text-center">
    <p>&copy; 2025 ProcureEase. All rights reserved. <span class="text-gray-400"> | </span> Designed for seamless procurement</p>
  </footer>

  <!-- AOS JS -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 1000,
      once: false // Ensure animations can be triggered multiple times on scroll
    });

    // Refresh AOS on scroll
    window.addEventListener('scroll', () => {
      AOS.refresh();
    });
  </script>

</body>
</html>

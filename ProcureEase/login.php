<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login / Sign Up</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

</head>
<body class="min-h-screen flex">

  <!-- Left Banner -->
<div class="w-1/2 bg-gray-800 text-white flex flex-col justify-center items-center px-10 py-20 space-y-6 rounded-br-[50px] rounded-tr-[50px]">
  <img src="./image/logo2.png" alt="Logo" class="rounded-lg shadow-lg mx-auto max-w-52 bg-white" />
  <h1 class="text-4xl font-extrabold text-amber-400">ProcureEase</h1>
  <p class="text-center text-gray-300 text-lg max-w-md">
    Trusted by local suppliers and government agencies for seamless and transparent procurement.
  </p>
</div>

  <!-- Right Side with Sliding Form Panel -->
  <div class="w-1/2 bg-white overflow-hidden relative flex items-center justify-center">
    <div class="w-full overflow-hidden relative">
      <div id="form-container" class="flex w-[200%] transition-transform duration-500">

       <!-- Container for Centering the Form -->
<div id="login-wrapper" class="w-full h-screen flex justify-center items-center">
  <div class="w-[450px] p-8 border-2 border-gray-300 rounded-xl bg-white shadow-lg">
    <!-- Login Form -->
    <h2 class="text-3xl font-bold text-amber-500 mb-6 text-center">Login</h2>
    <form class="space-y-4 login-form">
      <input type="email" name="email" placeholder="Email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" />
      <input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" />
      <button type="submit" class="w-full bg-amber-500 text-white py-2 rounded-lg hover:bg-amber-600 font-semibold">Login</button>
    </form>
    <p class="mt-6 text-sm text-center">
      Don't have an account?
      <button id="show-signup" class="text-amber-500 font-semibold hover:underline">Sign Up</button>
    </p>
  </div>
</div>


    <!-- Sign Up Form Container -->
<div class="w-full h-screen flex justify-center items-center">
  <div class="w-[450px] p-8 border-2 border-gray-300 rounded-xl bg-white shadow-lg">
    <h2 class="text-3xl font-bold text-amber-500 mb-6 text-center">Sign Up</h2>
    <form class="space-y-4 signup-form">
      <input type="text" name="name" placeholder="Full Name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" />
      <input type="email" name="email" placeholder="Email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" />
      <input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" />

      <!-- Role Selection (value="invalid" avoids empty default issues) -->
      <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
        <option value="invalid" selected disabled>Select Role</option>
        <option value="supplier">Supplier</option>
        <option value="government">Government</option>
      </select>

      <button type="submit" class="w-full bg-amber-500 text-white py-2 rounded-lg hover:bg-amber-600 font-semibold">Sign Up</button>
    </form>
    <p class="mt-6 text-sm text-center">
      Already have an account?
      <button id="show-login" class="text-amber-500 font-semibold hover:underline">Login</button>
    </p>
  </div>
</div>


      </div>
    </div>
  </div>

  <script src="./js/login.js"></script>



</body>
</html>

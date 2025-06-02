<?php
include('./includes/db_connect.php');
session_start();

// Check if the user is a supplier and logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier Information Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded shadow-md w-full max-w-lg">
    <h2 class="text-2xl font-bold mb-6 text-amber-500">Supplier Registration Form</h2>

    <form id="supplierForm" class="space-y-4">
        <div>
            <label class="block text-gray-700">Company Name</label>
            <input type="text" name="company_name" class="w-full p-2 border border-gray-300 rounded" required>
        </div>
        <div>
            <label class="block text-gray-700">Address</label>
            <input type="text" name="address" class="w-full p-2 border border-gray-300 rounded" required>
        </div>
        <div>
            <label class="block text-gray-700">Contact Number</label>
            <input type="text" name="contact" class="w-full p-2 border border-gray-300 rounded" required>
        </div>

        <button type="submit" class="w-full bg-amber-500 text-white p-2 rounded hover:bg-amber-600 transition">Submit</button>
    </form>
</div>

<script src="./js/form_submit.js"></script>
</body>
</html>

<?php
include('../includes/db_connect.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .sidebar-collapsed .sidebar-text {
            display: none;
        }
        .sidebar-collapsed .sidebar {
            width: 80px;
        }
        .sidebar-expanded .sidebar {
            width: 260px;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex">

<!-- Sidebar -->
<aside id="sidebar" class="sidebar sidebar-expanded fixed top-0 left-0 h-full bg-gray-800 text-white flex flex-col p-4 transition-all duration-300 z-30">
    <div class="flex items-center justify-between mb-6">
        <div class="text-2xl font-bold sidebar-text text-amber-400">ProcureEase</div>
        <button id="toggleSidebar" class="md:block text-white p-1 rounded hover:bg-gray-700">
            <i data-feather="menu"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex flex-col space-y-2">
        <a href="#" data-target="allUsers" class="nav-link flex items-center space-x-3 p-3 rounded hover:bg-gray-700 bg-gray-700">
            <i data-feather="users"></i><span class="sidebar-text">All Users</span>
        </a>
        <a href="#" data-target="userModeration" class="nav-link flex items-center space-x-3 p-3 rounded hover:bg-gray-700">
            <i data-feather="user-check"></i><span class="sidebar-text">User Moderation</span>
        </a>
        <a href="#" data-target="subscriptionReview" class="nav-link flex items-center space-x-3 p-3 rounded hover:bg-gray-700">
            <i data-feather="file-text"></i><span class="sidebar-text">Subscription Review</span>
        </a>
        <a href="#" data-target="systemAnalytics" class="nav-link flex items-center space-x-3 p-3 rounded hover:bg-gray-700">
            <i data-feather="bar-chart-2"></i><span class="sidebar-text">System Analytics</span>
        </a>
    </nav>

    <!-- Logout -->
    <a href="../logout.php" class="mt-auto bg-red-500 text-center p-3 rounded hover:bg-red-600 flex items-center justify-center space-x-2">
        <i data-feather="log-out"></i><span class="sidebar-text">Logout</span>
    </a>
</aside>

<!-- Main Content -->
<main id="mainContent" class="flex-1 pl-[260px] pr-6 pt-6 pb-6 space-y-10 overflow-y-auto transition-all duration-300">
    <h2 class="text-4xl font-bold text-amber-500 mb-4">Admin Dashboard</h2>

    <!-- Sections -->
    <section id="allUsers">
        <?php include './all_users.php'; ?>
    </section>

    <section id="userModeration" class="hidden">
        <?php include './user_moderation.php'; ?>
    </section>

    <section id="subscriptionReview" class="hidden">
        <?php include './subscription_review.php'; ?>
    </section>

    <section id="systemAnalytics" class="hidden">
        <?php include './system_analytics.php'; ?>
    </section>
</main>

<!-- JS -->
<script>
    feather.replace();

    const sidebar = document.getElementById('sidebar');
    const toggleSidebar = document.getElementById('toggleSidebar');
    const mainContent = document.getElementById('mainContent');

    toggleSidebar.addEventListener('click', () => {
        sidebar.classList.toggle('sidebar-collapsed');
        sidebar.classList.toggle('sidebar-expanded');

        // Adjust main content padding based on sidebar state
        if (sidebar.classList.contains('sidebar-collapsed')) {
            mainContent.style.paddingLeft = '120px';
        } else {
            mainContent.style.paddingLeft = '260px';
        }
    });

    const links = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('main > section');

    links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            // Reset all links
            links.forEach(l => l.classList.remove('bg-gray-700'));
            link.classList.add('bg-gray-700');

            const target = link.getAttribute('data-target');

            // Toggle section visibility
            sections.forEach(section => {
                section.classList.toggle('hidden', section.id !== target);
            });

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
</script>
</body>
</html>

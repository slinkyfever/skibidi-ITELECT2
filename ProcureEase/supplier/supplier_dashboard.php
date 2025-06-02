<?php
include('../includes/db_connect.php');
session_start();

// Check if the user is a supplier and logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    header('Location: login.php');
    exit();
}

// Fetch supplier info
$user_id = $_SESSION['user_id'];
$query = "SELECT company_name, profile_image FROM suppliers WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$supplier = $result->fetch_assoc();

// Fallbacks in case of missing data
$companyName = $supplier['company_name'] ?? 'Your Company';
$profileImage = $supplier['profile_image'] ?? '../assets/profile-placeholder.png'; // Use relative path to default image

$query = "
    SELECT 
        u.name, u.email, s.company_name, s.contact, s.profile_image 
    FROM suppliers s
    JOIN users u ON s.user_id = u.id
    WHERE s.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$supplier = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Supplier Dashboard</title>
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
        <!-- Toggle Button -->
        <div class="flex items-center justify-between mb-2">
            <div class="text-2xl font-bold sidebar-text text-amber-400">ProcureEase</div>
            <button id="toggleSidebar" class="md:block text-white p-1 rounded hover:bg-gray-700">
                <i data-feather="menu"></i>
            </button>
        </div>
        <p class="sidebar-text text-sm text-gray-300 pl-6 mb-5">Welcome, Supplier</p>
        <!-- Profile Section -->
        <div class="flex flex-col items-center mb-">
            <!-- Profile Image -->
            <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile" class="w-16 h-16 rounded-full border-4 border-amber-500 object-cover mb-2">

            <!-- Company Name -->
            <h2 class="text-lg font-bold text-amber-400 sidebar-text mb-2 text-center">
                <?php echo htmlspecialchars($companyName); ?>
            </h2>
        </div>



        <!-- Navigation -->
        <nav class="flex flex-col space-y-2">
            <button id="profileBtn" class="flex items-center space-x-3 p-3 rounded hover:bg-gray-700 text-left w-full">
                <i data-feather="user"></i><span class="sidebar-text">Profile</span>
            </button>
            <a href="#" data-target="dashboardSection" class="nav-link flex items-center space-x-3 p-3 rounded hover:bg-gray-700 active-link bg-gray-700">
                <i data-feather="home"></i><span class="sidebar-text">Dashboard</span>
            </a>
            <a href="#" data-target="postProductSection" class="nav-link flex items-center space-x-3 p-3 rounded hover:bg-gray-700">
                <i data-feather="plus-square"></i><span class="sidebar-text">Post Product</span>
            </a>
            <a href="#" data-target="yourProductsSection" class="nav-link flex items-center space-x-3 p-3 rounded hover:bg-gray-700">
                <i data-feather="box"></i><span class="sidebar-text">Your Products</span>
            </a>
            <a href="#" data-target="ordersSection" class="nav-link flex items-center space-x-3 p-3 rounded hover:bg-gray-700">
                <i data-feather="shopping-cart"></i><span class="sidebar-text">Orders</span>
            </a>
            <a href="#" data-target="salesSection" class="nav-link flex items-center space-x-3 p-3 rounded hover:bg-gray-700">
                <i data-feather="bar-chart-2"></i><span class="sidebar-text">Sales</span>
            </a>

        </nav>


        <!-- Logout -->
        <a href="../logout.php" class="mt-auto bg-red-500 text-center p-3 rounded hover:bg-red-600 flex items-center justify-center space-x-2">
            <i data-feather="log-out"></i><span class="sidebar-text">Logout</span>
        </a>
    </aside>

    <!-- Profile Modal -->
    <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <button id="closeProfileModal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800">
                <i data-feather="x"></i>
            </button>
            <form id="profileForm" method="POST" action="update_supplier_profile.php" enctype="multipart/form-data" class="space-y-4 text-left">
                <div class="relative w-24 h-24 mx-auto mb-4">
                    <label for="profile_image" class="cursor-pointer group">
                        <!-- Profile Image -->
                        <img id="profilePreview" src="<?= htmlspecialchars($supplier['profile_image'] ?? '../assets/profile-placeholder.png') ?>"
                            alt="Profile" class="w-24 h-24 rounded-full border-4 border-amber-500 object-cover">
                        <input type="file" name="profile_image" id="profile_image" class="hidden" accept="image/*">

                        <!-- Camera Icon Overlay -->
                        <div class="absolute bottom-0 right-0 p-1 rounded-full group-hover:bg-opacity-80 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                            </svg>

                        </div>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Company Name</label>
                    <input type="text" name="company_name" value="<?= htmlspecialchars($supplier['company_name'] ?? '') ?>"
                        class="w-full mt-1 border rounded p-2 focus:outline-none focus:ring focus:ring-amber-400">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($supplier['name'] ?? '') ?>"
                        class="w-full mt-1 border rounded p-2 focus:outline-none focus:ring focus:ring-amber-400">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($supplier['email'] ?? '') ?>"
                        class="w-full mt-1 border rounded p-2 focus:outline-none focus:ring focus:ring-amber-400">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="contact" value="<?= htmlspecialchars($supplier['contact'] ?? '') ?>"
                        class="w-full mt-1 border rounded p-2 focus:outline-none focus:ring focus:ring-amber-400">
                </div>

                <div class="text-right">
                    <button type="submit" class="bg-amber-500 text-white px-4 py-2 rounded hover:bg-amber-600">Save</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Main Content -->
    <main id="mainContent" class="flex-1 pl-[260px] pr-6 pt-6 pb-6 space-y-10 overflow-y-auto transition-all duration-300">

        <!-- Dashboard Section -->
        <section id="dashboardSection">
            <?php include './dashboard_section.php'; ?>
        </section>

        <!-- Post Product Section -->
        <section id="postProductSection" class="hidden" style="margin-top:-5px;">
            <?php include './post_product_form.php'; ?>
        </section>

        <!-- Your Products Section -->
        <section id="yourProductsSection" class="hidden" style="margin-top:-5px;">
            <?php include './your_products_section.php'; ?>
        </section>

        <!-- Orders Section -->
        <section id="ordersSection" class="hidden" style="margin-top:-5px;">
            <?php include './orders_section.php'; ?>
        </section>

        <!-- Sales Section -->
        <section id="salesSection" class="hidden" style="margin-top:-5px;">
            <?php include './sales_section.php'; ?>
        </section>


    </main>
    <script src="../js/fetch_products.js"></script>
    <script src="../js/product_submit.js"> </script>
    <script src="../js/update-product.js"></script>
    <script>
        feather.replace();

        const sidebar = document.getElementById('sidebar');
        const toggleSidebar = document.getElementById('toggleSidebar');
        const mainContent = document.getElementById('mainContent');

        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-collapsed');
            sidebar.classList.toggle('sidebar-expanded');

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
                links.forEach(l => l.classList.remove('bg-gray-700'));
                link.classList.add('bg-gray-700');

                const target = link.getAttribute('data-target');
                sections.forEach(section => {
                    if (section.id === target) {
                        section.classList.remove('hidden');
                    } else {
                        section.classList.add('hidden');
                    }
                });
            });
        });

        // Feather icons refresh
        feather.replace();

        // Profile modal toggle
        const profileBtn = document.getElementById('profileBtn');
        const profileModal = document.getElementById('profileModal');
        const closeProfileModal = document.getElementById('closeProfileModal');

        profileBtn.addEventListener('click', (e) => {
            e.preventDefault();
            profileModal.classList.remove('hidden');
        });

        closeProfileModal.addEventListener('click', () => {
            profileModal.classList.add('hidden');
        });

        window.addEventListener('click', (e) => {
            if (e.target === profileModal) {
                profileModal.classList.add('hidden');
            }
        });

        const profileImageInput = document.getElementById('profile_image');
        const profilePreview = document.getElementById('profilePreview');

        profileImageInput.addEventListener('change', (event) => {
            const [file] = event.target.files;
            if (file) {
                profilePreview.src = URL.createObjectURL(file);
            }
        });
    </script>

</body>

</html>
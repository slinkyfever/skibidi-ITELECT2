<?php
require '../includes/db_connect.php';

// Get filters from query string
$filter = isset($_GET['role']) ? $_GET['role'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build dynamic WHERE clause
$whereClause = "WHERE u.is_new = 0";

if ($filter && in_array($filter, ['admin', 'supplier', 'government'])) {
    $whereClause .= " AND u.role = '" . mysqli_real_escape_string($conn, $filter) . "'";
}

if (!empty($search)) {
    $escapedSearch = mysqli_real_escape_string($conn, $search);
    $whereClause .= " AND (u.name LIKE '%$escapedSearch%' OR u.email LIKE '%$escapedSearch%')";
}

// SQL query
$query = "
    SELECT 
        u.id AS user_id,
        u.name,
        u.email,
        u.role,
        g.agency_name,
        g.address AS government_address,
        g.contact AS government_contact,
        s.company_name,
        s.address AS supplier_address,
        s.contact AS supplier_contact
    FROM users u
    LEFT JOIN governments g ON u.id = g.user_id
    LEFT JOIN suppliers s ON u.id = s.user_id
    $whereClause
    ORDER BY u.id DESC
";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<div class="bg-white p-6 rounded shadow">
    <h3 class="text-xl font-bold mb-4">All Users (Admins, Governments, Suppliers)</h3>

    <!-- Search + Filter -->
    <form method="get" class="mb-4 flex flex-wrap items-center gap-2">
        <input type="hidden" name="role" value="<?= htmlspecialchars($filter) ?>">
        <input type="text" name="search" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>"
               class="px-3 py-2 border rounded w-64">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
        <?php if (!empty($search)): ?>
            <a href="?role=<?= urlencode($filter) ?>" class="text-sm text-red-500 ml-2">Clear search</a>
        <?php endif; ?>
    </form>

    <!-- Filter Buttons -->
    <div class="mb-4 space-x-2">
        <a href="?role=&search=<?= urlencode($search) ?>" class="px-4 py-2 rounded <?= $filter == '' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">All</a>
        <a href="?role=government&search=<?= urlencode($search) ?>" class="px-4 py-2 rounded <?= $filter == 'government' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">Government</a>
        <a href="?role=supplier&search=<?= urlencode($search) ?>" class="px-4 py-2 rounded <?= $filter == 'supplier' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">Supplier</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">Name</th>
                    <th class="px-4 py-2 border">Email</th>
                    <th class="px-4 py-2 border">Role</th>
                    <th class="px-4 py-2 border">Agency/Company</th>
                    <th class="px-4 py-2 border">Address</th>
                    <th class="px-4 py-2 border">Contact</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['role']) ?></td>
                            <td class="px-4 py-2 border">
                                <?= htmlspecialchars(
                                    $row['role'] === 'government' ? ($row['agency_name'] ?? 'N/A') :
                                    ($row['role'] === 'supplier' ? ($row['company_name'] ?? 'N/A') : 'N/A')
                                ) ?>
                            </td>
                            <td class="px-4 py-2 border">
                                <?= htmlspecialchars(
                                    $row['role'] === 'government' ? ($row['government_address'] ?? 'N/A') :
                                    ($row['role'] === 'supplier' ? ($row['supplier_address'] ?? 'N/A') : 'N/A')
                                ) ?>
                            </td>
                            <td class="px-4 py-2 border">
                                <?= htmlspecialchars(
                                    $row['role'] === 'government' ? ($row['government_contact'] ?? 'N/A') :
                                    ($row['role'] === 'supplier' ? ($row['supplier_contact'] ?? 'N/A') : 'N/A')
                                ) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center px-4 py-4 text-gray-500">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

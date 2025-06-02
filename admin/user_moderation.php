<?php
require '../includes/db_connect.php'; // adjust path as needed

// Fetch pending users
$pendingUsers = $conn->query("SELECT id, name, email, role FROM users WHERE is_new = 1");
?>

<section class="p-6">
    <h3 class="text-2xl font-semibold mb-4">User & Content Moderation</h3>
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Role</th>
                    <th class="px-4 py-2 text-center">View Details</th>
                    <th class="px-4 py-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = $pendingUsers->fetch_assoc()): ?>
                    <?php
                        $userId = $user['id'];
                        $role = $user['role'];
                        $details = [];

                        if ($role == 'supplier') {
                            $details = $conn->query("SELECT company_name, address, contact FROM suppliers WHERE user_id = $userId")->fetch_assoc();
                        } elseif ($role == 'government') {
                            $details = $conn->query("SELECT agency_name AS company_name, address, contact FROM governments WHERE user_id = $userId")->fetch_assoc();
                        }

                        $company = $details['company_name'] ?? 'N/A';
                        $address = $details['address'] ?? 'N/A';
                        $contact = $details['contact'] ?? 'N/A';
                    ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?= htmlspecialchars($user['name']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="px-4 py-2"><?= ucfirst($role) ?></td>
                        <td class="px-4 py-2 text-center">
                            <button 
                                onclick="openModal('<?= addslashes($company) ?>', '<?= addslashes($address) ?>', '<?= addslashes($contact) ?>')"
                                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                View
                            </button>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <form method="POST" action="process_user_action.php" class="inline">
                                <input type="hidden" name="user_id" value="<?= $userId ?>">
                                <input type="hidden" name="action" value="approve">
                                <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Approve</button>
                            </form>
                            <form method="POST" action="process_user_action.php" class="inline ml-2">
                                <input type="hidden" name="user_id" value="<?= $userId ?>">
                                <input type="hidden" name="action" value="reject">
                                <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Modal -->
<div id="detailsModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">User Credentials</h2>
        <p><strong>Company/Agency:</strong> <span id="modalCompany"></span></p>
        <p><strong>Address:</strong> <span id="modalAddress"></span></p>
        <p><strong>Contact:</strong> <span id="modalContact"></span></p>
        <div class="text-right mt-4">
            <button onclick="closeModal()" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Close</button>
        </div>
    </div>
</div>

<script>
    function openModal(company, address, contact) {
        document.getElementById('modalCompany').textContent = company;
        document.getElementById('modalAddress').textContent = address;
        document.getElementById('modalContact').textContent = contact;
        document.getElementById('detailsModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('detailsModal').classList.add('hidden');
    }
</script>

<?php
include('./includes/db_connect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, email, password, role, is_new FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];

            // Check if user is new
            if ($user['is_new'] == 1) {
                // Check if the user has already submitted their form
                $role = $user['role'];
                $user_id = $user['id'];
                $submitted = false;

                if ($role === 'supplier') {
                    $check = $conn->query("SELECT id FROM suppliers WHERE user_id = $user_id");
                    $submitted = $check->num_rows > 0;
                } elseif ($role === 'government') {
                    $check = $conn->query("SELECT id FROM governments WHERE user_id = $user_id");
                    $submitted = $check->num_rows > 0;
                }

                if ($submitted) {
                    echo "error:Your credentials are under review. Please wait for admin approval.";
                } else {
                    // Redirect to fill-up form
                    if ($role === 'supplier') {
                        echo "redirect:supplier_form.php";
                    } elseif ($role === 'government') {
                        echo "redirect:government_form.php";
                    }
                }
            } else {
                // Already approved
                if ($user['role'] == 'supplier') {
                    echo "redirect:./supplier/supplier_dashboard.php";
                } elseif ($user['role'] == 'government') {
                    echo "redirect:./government/government_dashboard.php";
                } elseif ($user['role'] == 'admin') {
                    echo "redirect:./admin/admin_dashboard.php";
                }
            }
        } else {
            echo "error:Invalid password";
        }
    } else {
        echo "error:User not found";
    }
}
?>

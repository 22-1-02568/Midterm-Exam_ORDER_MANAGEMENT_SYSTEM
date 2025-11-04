<?php
require '../session.php';
check_role(['superadmin']); // Only superadmin can do this
require '../db.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Get current status
    $stmt = $pdo->prepare("SELECT status FROM users WHERE id = ? AND role = 'admin'");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user) {
        // Toggle status
        $new_status = ($user['status'] == 'active') ? 'suspended' : 'active';

        $update_stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $update_stmt->execute([$new_status, $user_id]);
    }
}

// Redirect back to the management page
header("Location: ../manage_users.php");
exit();
?>

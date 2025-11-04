<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to check for a specific role
function check_role($allowed_roles) {
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        // If not allowed, send to their respective dashboard
        if ($_SESSION['role'] == 'superadmin') {
            header("Location: superadmin_dashboard.php");
        } else {
            header("Location: admin_dashboard.php");
        }
        exit();
    }
}
?>

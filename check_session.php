<?php
session_start();

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Redirect to login page if not logged in
    exit();
}

$user_role = $_SESSION['role'];  // Get user role from the session
if ($user_role !== 'Admin' && $user_role !== 'Editor') {
    echo "Permission Denied: You are not authorized to create a post.";
    exit();
}
?>

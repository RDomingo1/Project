<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  
    exit();
}

$user_role = $_SESSION['role']; 
if ($user_role !== 'Admin' && $user_role !== 'Editor') {
    echo "Permission Denied: You are not authorized to create a post.";
    exit();
}
?>

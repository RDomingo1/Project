<?php

// print_r($_POST); 
session_start();


require_once 'connect.php'; 
require('authenticate.php');


if (!isset($_SESSION['user_data']['role'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_data']['role'];
print_r($user_role);
if ($user_role != 'Admin' && $user_role != 'Editor' ) {
    $_SESSION['error'] = 'Permission Denied: You are not authorized to create a post.';
    header('Location: index.php'); 
    exit();
}

if($_POST && !empty($_POST['title']) && !empty($_POST['context'])) {


        
    $title = filter_input(INPUT_POST,'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $context = filter_input(INPUT_POST,'context', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
        // Validate form data (basic validation)
    if (empty($title) || empty($context)) {
        $_SESSION['error'] = 'Title and context are required fields.';
        // header('Location: create_post.php');  // Redirect back to the form with an error
        exit();
    }
    $query = "INSERT INTO posts(User_id,title,context)
              VALUES (:User_id, :title, :context)";

    $statement = $db->prepare($query);
    $statement->bindValue(":title", $title);
    $statement->bindValue(":context", $context);
    $statement->bindValue(":User_id", NULL);
    $statement->execute();

    //  header("Location: index.php");

    
}    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Display error or success message -->
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <h2>Create a New Post</h2>

    <!-- Form to create a new post -->
    <form action="create_post.php" method="POST">
        <label for="title">Post Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="context">Content:</label><br>
        <textarea id="context" name="context" rows="10" cols="30" required></textarea><br><br>

        <input type="submit" value="Create Post">
    </form>

</body>
</html>

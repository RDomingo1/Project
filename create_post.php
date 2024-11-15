<?php

 
session_start();

require_once 'connect.php'; 

if (!isset($_SESSION['user_data']['role'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_data']['role'];
// print_r($user_role);
if ($user_role != 'Admin' && $user_role != 'Editor' ) {
    $_SESSION['error'] = 'Permission Denied: You are not authorized to create a post.';
    header('Location: index.php'); 
    exit();
}

if($_POST && !empty($_POST['title']) && !empty($_POST['context'])) {
     
    $title = filter_input(INPUT_POST,'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $context = filter_input(INPUT_POST,'context', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    if (empty($title) || empty($context)) {
        $_SESSION['error'] = 'Title and context are required fields.';
        header('Location: create_post.php'); 
        exit();
    }
    $query = "INSERT INTO posts(User_id,title,context)
              VALUES (:User_id, :title, :context)";

    $statement = $db->prepare($query);
    $statement->bindValue(":title", $title);
    $statement->bindValue(":context", $context);
    $statement->bindValue(":User_id", NULL);
    $statement->execute();

    header("Location: index.php");

    
}    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.tiny.cloud/1/870tdp4rg7jz4jr9r3ftapd4d3k0nwsbg805k9poqlrg1aw1/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script type="text/javascript">
        tinymce.init({
            selector: 'textarea',
            plugins: ['autolink', 'link', 'autoresize', 'wordcount', 'visualchars'],
            toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify | ' +
                     'bullist numlist outdent indent | visualchars |' +
                     'forecolor backcolor emoticons | help | wordcount'
        });
    </script>
</head>
<body>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <h2>Create a New Post</h2>

    <form action="create_post.php" method="POST">
        <label for="title">Post Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>
        <label for="context">Content:</label><br>
        <textarea id="context" name="context" rows="10" cols="30" ></textarea><br><br>
        <input type="submit" value="Create Post">
    </form>

</body>
</html>

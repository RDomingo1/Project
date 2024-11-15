<?php

 
session_start();

require_once 'connect.php'; 

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

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if($id){
    $query = "SELECT * FROM posts WHERE Post_id = :id ORDER BY date_created DESC";
    $statement = $db->prepare($query);
    $statement -> bindValue(':id', $id);
    $statement->execute();
    $post = $statement->fetch();
}
elseif($_POST && !empty($_POST['id']) && !empty($_POST['command'])) {
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT); 
    $command = filter_input(INPUT_POST, "command", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if($command == "edit"){
        $title = filter_input(INPUT_POST,'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $context = filter_input(INPUT_POST,'context', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (empty($title) || empty($context)) {
            $_SESSION['error'] = 'Title and context are required fields.';
            header('Location: edit.php?id=' + $id); 
            exit();
        }
        $query = "UPDATE posts SET User_id = :User_id, title = :title, context = :context WHERE Post_id = :Post_id";
    
        $statement = $db->prepare($query);
        $statement->bindValue(":title", $title);
        $statement->bindValue(":Post_id", $id);
        $statement->bindValue(":context", $context);
        $statement->bindValue(":User_id", NULL);
        $statement->execute();
    }
    elseif($command == "delete"){
        $query = "DELETE FROM posts WHERE Post_id=:id";
        $statement = $db->prepare($query);
        $statement->bindValue(":id", $id);
        $statement->execute();
    }    
    header("Location: index.php");
    exit();
}
else{
    header("Location: index.php");
    exit();
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

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <h2>Edit Post</h2>

    <form action="edit.php" method="POST">
        <label for="title">Post Title:</label><br>
        <input type="text" value="<?=$post["title"] ?>" id="title" name="title" required><br><br>

        <label for="context">Content:</label><br>
        <textarea id="context" name="context" rows="10" cols="30" required><?=$post["context"] ?></textarea><br><br>
        <input type="hidden" name="id" value="<?=$post["Post_id"] ?>">
        <button type="submit" value="edit" name="command">Edit Post</button>
        <button type="submit" value="delete" name="command">Delete Post </button>
    </form>

</body>
</html>

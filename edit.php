<?php

use Gumlet\ImageResize;
session_start();

require_once 'connect.php'; 
require("vendor\autoload.php");

$file_upload = "";

if (!isset($_SESSION['user_data']['role'])) {
    header("Location: login.php");
    exit();
}
$user_role = $_SESSION['user_data']['role'];
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
        $remove_image = filter_input(INPUT_POST,'remove_image', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            $image_extension = "." . pathinfo(basename($_FILES['image']['name']), PATHINFO_EXTENSION);
            $image_name = basename($_FILES['image']['name'], $image_extension) . time() . $image_extension;
            $file_upload = "uploads/" . $image_name;
            $valid_types = ['jpg','jpeg','png','gif'];
            $valid_mime_types = ['image/jpg','image/jpeg','image/png','image/gif'];  
            $actual_mime_types = mime_content_type($_FILES['image']['tmp_name']);
            
    
            if (in_array($actual_mime_types, $valid_mime_types)) {
                $medium = new ImageResize($_FILES['image']['tmp_name']);
                $medium->resizeToWidth(400);
                $medium->save($file_upload);
                $query = "SELECT image_path FROM posts WHERE Post_id = :id";

                $statement = $db->prepare($query);
                $statement->bindValue(":id", $id);
                $statement->execute();

                $old_image = $statement->fetch()["image_path"];
                if(!empty($old_image)){
                    unlink(realpath($old_image));
                }

            } else {
                $file_upload = "";
            }
        } else {
            $file_upload = ""; 
        }

        if (empty($title) || empty($context)) {
            $_SESSION['error'] = 'Title and context are required fields.';
            header('Location: edit.php?id=' + $id); 
            exit();
        }
        if($remove_image == "on"){
            $query = "SELECT image_path FROM posts WHERE Post_id = :id";
            $statement = $db->prepare($query);
            $statement->bindValue(":id", $id);
            $statement->execute();
            $old_image = $statement->fetch()["image_path"];
            if(!empty($old_image)){
                unlink(realpath($old_image));
            }
            $query = "UPDATE posts SET image_path = NULL WHERE Post_id = :id";
            $statement = $db->prepare($query);
            $statement->bindValue(":id", $id);
            $statement->execute();
        }

        $query = "";
        if($file_upload != ""){
            $query = "UPDATE posts SET User_id = :User_id, title = :title, context = :context, image_path = :image_path  WHERE Post_id = :Post_id";
        }
        else{
            $query = "UPDATE posts SET User_id = :User_id, title = :title, context = :context WHERE Post_id = :Post_id";
        }
        
    
        $statement = $db->prepare($query);
        $statement->bindValue(":title", $title);
        $statement->bindValue(":Post_id", $id);
        $statement->bindValue(":context", $context);
        $statement->bindValue(":User_id", NULL);
        if($file_upload !=""){
            $statement->bindValue(":image_path", $file_upload);
        }
        $statement->execute();
    }
    elseif($command == "delete"){
        $query = "SELECT image_path FROM posts WHERE Post_id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(":id", $id);
        $statement->execute();
        $old_image = $statement->fetch()["image_path"];
        if(!empty($old_image)){
            unlink(realpath($old_image));
        }
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
    <title>Edit Post</title>
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
    <?php include('header.php') ?>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <h2>Edit Post</h2>

    <form action="edit.php" method="POST" enctype="multipart/form-data">
        <label for="title">Post Title:</label><br>
        <input type="text" value="<?=$post["title"] ?>" id="title" name="title" required><br><br>

        <label for="context">Content:</label><br>
        <textarea id="context" name="context" rows="10" cols="30" required><?=$post["context"] ?></textarea><br><br>
        <input type="hidden" name="id" value="<?=$post["Post_id"] ?>">
        <input type="file" name="image" id="img">
        <label for="check">Delete Image?:</label>
        <input type="checkbox" name="remove_image" id="check">
        <button type="submit" value="edit" name="command">Edit Post</button>
        <button type="submit" value="delete" name="command" onclick="return confirm('Do you really want to delete this post?')">Delete Post </button>
    </form>

</body>
</html>

<?php
use Gumlet\ImageResize;
 
session_start();

require_once 'connect.php'; 
require("vendor\autoload.php");

$upload_status = "";
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

if($_POST && !empty($_POST['title']) && !empty($_POST['context'])) {
     
    $title = filter_input(INPUT_POST,'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $context = filter_input(INPUT_POST,'context', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    if(isset($_FILES['image'])){
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
        } else {
            $file_upload = "";
        }
    } else {
        $file_upload = ""; 
    }




    if(empty($title) || empty($context)) {
        $_SESSION['error'] = 'Title and context are required fields.';
        header('Location: create_post.php'); 
        exit();
    }
    $query = "INSERT INTO posts(User_id,title,context,image_path)
              VALUES (:User_id, :title, :context, :image_path)";

    $statement = $db->prepare($query);
    $statement->bindValue(":title", $title);
    $statement->bindValue(":context", $context);
    $statement->bindValue(":User_id", NULL);
    $statement->bindValue(":image_path", $file_upload);
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

    <form action="create_post.php" method="POST" enctype="multipart/form-data">
        <label for="title">Post Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>
        <label for="context">Content:</label><br>
        <textarea id="context" name="context" rows="10" cols="30" ></textarea><br><br>
        <input type="file" name="image" id="img">
        <input type="submit" value="Create Post">
    </form>



</body>
</html>

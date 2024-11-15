<?php

session_start();
print_r($_SESSION);

require_once 'connect.php';  

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
$query = "SELECT * FROM posts WHERE Post_id = :id ORDER BY date_created DESC";
$statement = $db->prepare($query);
$statement -> bindValue(':id', $id);
$statement->execute();
$post = $statement->fetch();

if(empty($post)) {
    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $post['title'] ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include('header.php') ?>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <h2><?=$post['title']?></h2>
        <p>Created On <?=date("F j, Y, g:i a", strtotime($post['date_created']))?></p>
        <p><?= htmlspecialchars_decode($post['context']) ?></p>
        <p><a href="edit.php?id=<?=$post['Post_id']?>">Edit</a></p>
</body>
</html>

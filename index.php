<?php

session_start();
print_r($_SESSION);

require_once 'connect.php';  

$query = "SELECT * FROM posts ORDER BY date_created DESC";
$statement = $db->prepare($query);
$statement->execute();
$posts = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Posts</title>
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

    <h2>Available Content</h2>
  
        <ul>
            <li><a href="create_post.php">Create a Post</a></li>
        </ul>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Date Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($post['date_created'])); ?></td>
                    <td><a href="view.php?id=<?=$post['Post_id']?>">View</a></td>
                    <td><a href="edit.php?id=<?=$post['Post_id']?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>

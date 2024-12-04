<?php

session_start();

require_once 'connect.php';  

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
$query = "SELECT * FROM posts WHERE Post_id = :id ORDER BY date_created DESC";
$statement = $db->prepare($query);
$statement -> bindValue(':id', $id);
$statement->execute();
$post = $statement->fetch();
$selectComments = "SELECT * FROM comments WHERE Post_id = :id ORDER BY Date_Time DESC";
$statementComment = $db->prepare($selectComments);
$statementComment -> bindValue(':id', $id);
$statementComment->execute();
$comments = $statementComment->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER ['REQUEST_METHOD'] === 'POST'){
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if(!isset($_SESSION['user_data'])){
        $_SESSION['error'] = 'Permission Denied: You are not a member.';
        header('Location: view.php?id='.$post['Post_id']); 
        exit();
    }

    if(empty($comment)){
        $_SESSION['error'] = 'Comment cannot be empty';
        header('Location: view.php?id='.$post['Post_id']); 
        exit();
    }
    
    $query = "INSERT INTO comments (Post_id, User_id, content) VALUES (:Post_id, :User_id, :content)";
    $statement = $db->prepare($query);
    $statement -> bindValue(':Post_id', $id);
    $statement -> bindValue(':User_id', $_SESSION['user_data']['User_id']);
    $statement -> bindValue(':content', $comment);
    $statement->execute();
    header('Location: view.php?id='.$post['Post_id']);
}

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
    <?php if($post['image_path'] !=""):?>
        <img src="<?= $post['image_path']?>"/>
        <?php endif; ?>
    <?php if(isset($_SESSION['user_data'])): ?>
        <form action=<?="view.php?id=".$post['Post_id']?> method="POST"> 
            <label for="comment">Comments:</label><br>
            <input type="text" id="comment" name="comment" required><br><br>
            <input type="submit" id="submit" name="submit">
        </form>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Comments</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comments as $comment): ?>
                <tr>
                    <td><?php echo $comment['content']; ?></td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($comment['Date_Time'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

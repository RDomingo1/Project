<?php

session_start();
print_r($_SESSION);

require_once 'connect.php';  

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


$query = "SELECT * FROM users ORDER BY role";
$statement = $db->prepare($query);
$statement->execute();
$users = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
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

    <h2>Users</h2>
  
        <ul>
            <li><a href="createUsers.php">Create a user</a></li>
        </ul>

    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><a href="editUsers.php?id=<?=$user['User_id']?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>

<?php

session_start();


require_once 'connect.php'; 

if (!isset($_SESSION['user_data']['role'])) {
    header("Location: login.php");
    exit();
}
$user_role = $_SESSION['user_data']['role'];

if ($user_role != 'Admin' && $user_role != 'Editor' ) {
    $_SESSION['error'] = 'Permission Denied: You are not authorized to create a user.';
    header('Location: index.php'); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   
    if (empty($username) || empty($password) || empty($confirm_password) || empty($email)) {
        $_SESSION['error'] = 'All fields are required!';
        header('Location: createUsers.php');
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Password Does Not Match';
        header('Location: createUsers.php');
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT,['cost'=>12]);

    $checkQuery = "SELECT * FROM users WHERE username = :username";
    $statement = $db->prepare($checkQuery);
    $statement->bindValue(':username', $username);
    $statement->execute();
    
    if ($statement->rowCount() > 0) {
        $_SESSION['error'] = 'Username already exists!';
        header('Location: createUsers.php');
        exit();
    }

    $insertQuery = "INSERT INTO users (username, password, role, email) VALUES (:username, :password, :role, :email)";
    $statement = $db->prepare($insertQuery);
    $statement->bindValue(':username', $username);
    $statement->bindValue(':password', $hashed_password);
    $statement->bindValue(':role', $role);
    $statement->bindValue(':email', $email);
    $statement->execute();

    $_SESSION['success'] = 'User Creation successful!';
    header('Location: Users.php');  
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
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

    <h2>User Creation</h2>

    <form action="createUsers.php" method="POST">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
  
        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="role">Choose a role for user:</label>
        <select name="role" id="role">
        <option value="Admin">Admin</option>
        <option value="Editor">Editor</option>
        <option value="Member">Member</option>
        </select>

        <input type="submit" value="Register">
    </form>

</body>
</html>
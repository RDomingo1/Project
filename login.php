<?php
session_start();

require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username and Password are required.';
        header('Location: login.php'); 
        exit();
    }

    $checkQuery = "SELECT * FROM users WHERE username = :username";
    $statement = $db->prepare($checkQuery);
    $statement->bindValue(':username', $username);
    $statement->execute();

    if ($statement->rowCount() == 0) {
        $_SESSION['error'] = 'Username and Password Does not match';
        header('Location: login.php');
        exit();
    }

    $user = $statement->fetch();

    if(password_verify($password, $user['password'])){
        $_SESSION['user_data'] = [
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        $_SESSION['success'] = 'Login Successful';
        header('Location: index.php');
        exit();
    }
    else {
        $_SESSION['error'] = 'Username and Password Does not match';
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WCSA</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include('header.php') ?>
    <div class="login-container">
        <h2>Login to Your Account</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <p style="color: green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" value="Login">
        </form>
        
        <p>Don't have an account? <a href="registration.php">Register here</a></p>
    </div>
</body>
</html>

<?php
session_start();

// Check if user is already logged in, if so, redirect to the dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');  // Change to your dashboard page
    exit();
}

// Include database connection
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the login credentials
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Basic validation (check if fields are empty)
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username and Password are required.';
        header('Location: login.php');  // Redirect back with error
        exit();
    }

    // Query to check if user exists with the given username
    $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // User found, check password
        $user = mysqli_fetch_assoc($result);
        
        // Verify the password (assuming the password is hashed)
        if (password_verify($password, $user['password'])) {
            // Create session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];  // Admin, Editor, Member, etc.

            // Redirect based on role
            if ($user['role'] === 'Admin') {
                header('Location: admin_dashboard.php');  // Admin's dashboard
            } elseif ($user['role'] === 'Editor') {
                header('Location: editor_dashboard.php');  // Editor's dashboard
            } else {
                header('Location: member_dashboard.php');  // Member's dashboard
            }
            exit();
        } else {
            $_SESSION['error'] = 'Invalid password. Please try again.';
            header('Location: login.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'Username not found.';
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
    <div class="login-container">
        <h2>Login to Your Account</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" value="Login">
        </form>
        
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>

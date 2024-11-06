<?php
// Start the session
session_start();

// // Include the database connection
require_once 'connect.php';  // Adjust path to your database connection

// // Check if user is logged in (even non-admin users can access this page)
// if (!isset($_SESSION['user_id'])) {
//     $_SESSION['error'] = 'You must be logged in to view content.';
//     header('Location: login.php');  // Redirect to login page if not logged in
//     exit();
// }

// Fetch posts from the database
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

    <!-- Display error or success message -->
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <!-- Main content section -->
    <h2>Available Content</h2>

    <!-- Navigation Menu (optional) -->
    <nav>
        <ul>
            <li><a href="create_post.php">Create a Post</a></li>
        </ul>
    </nav>

    <!-- List of posts -->
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Date Created</th>
                <th>Context</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($post['date_created'])); ?></td>
                    <td><?php echo $post['context']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>

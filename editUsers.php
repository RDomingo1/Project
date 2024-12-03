<?php

session_start();

require_once 'connect.php'; 


if (!isset($_SESSION['user_data']['role'])) {
    header("Location: login.php");
    exit();
}
$user_role = $_SESSION['user_data']['role'];

if ($user_role != 'Admin' && $user_role != 'Editor' ) {
    $_SESSION['error'] = 'Permission Denied: You are not authorized to edit a user.';
    header('Location: index.php'); 
    exit();
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if($id){
    $query = "SELECT * FROM users WHERE User_id = :id";
    $statement = $db->prepare($query);
    $statement -> bindValue(':id', $id);
    $statement->execute();
    $user = $statement->fetch();
}
elseif($_POST && !empty($_POST['id']) && !empty($_POST['command'])) {
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT); 
    $command = filter_input(INPUT_POST, "command", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if($command == "edit"){
        $username = filter_input(INPUT_POST,'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST,'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST,'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $passwordConfirm = filter_input(INPUT_POST,'passwordConfirm', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $role = filter_input(INPUT_POST,'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

       

        if(empty($username) || empty($email) || empty($role)) {
            $_SESSION['error'] = 'Username, Email, and Role are required fields.';
            header('Location: editUsers.php?id=' + $id); 
            exit();
        }
        
        if(!empty($password) && $password != $passwordConfirm ){
            $_SESSION['error'] = 'Password must match.';
            header('Location: editUsers.php?id=' + $id); 
            exit();
        }

        $query = "";
        if(empty($password)){
            $query = "UPDATE users SET username = :username, email = :email, role = :role WHERE User_id = :User_id";
        }
        else{
            $query = "UPDATE users SET username = :username, email = :email, role = :role, password = :password WHERE User_id = :User_id";
        }
        
    
        $statement = $db->prepare($query);
        $statement->bindValue(":username", $username);
        $statement->bindValue(":email", $email);
        $statement->bindValue(":role", $role);
        $statement->bindValue(":User_id", $id);
        if(!empty($password)){
            $statement->bindValue(":password", $password);
        }
        $statement->execute();
    }
    elseif($command == "delete"){
        $query = "DELETE FROM users WHERE User_id=:id";
        $statement = $db->prepare($query);
        $statement->bindValue(":id", $id);
        $statement->execute();
    }    
    header("Location: users.php");
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
    <title>Edit Users</title>
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

    <h2>Edit User</h2>

    <form action="editUsers.php" method="POST">
        <input type="hidden" name="id" value="<?=$user["User_id"] ?>">
        <label for="username">Username:</label><br>
        <input type="text" value="<?=$user["username"] ?>" id="username" name="username" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password"><br><br>
  
        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password"><br><br>

        <label for="email">Email:</label><br>
        <input type="email" value="<?=$user["email"] ?>" id="email" name="email" required><br><br>

        <label for="role">Choose a role for user:</label>
        <select name="role" value="<?=$user["role"] ?>" id="role">
        <option value="Admin">Admin</option>
        <option value="Editor">Editor</option>
        <option value="Member">Member</option>
        </select>

        <button type="submit" value="edit" name="command">Edit User</button>
        <button type="submit" value="delete" name="command" onclick="return confirm('Do you really want to delete this user?')">Delete User</button>
    </form>

</body>
</html>

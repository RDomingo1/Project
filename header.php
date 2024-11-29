    <nav>
        <ul>
            <?php if(isset($_SESSION['user_data']['role'])): ?>
            <li><a href="logout.php">Logout</a></li>
            <?php else:?> 
            
            <li><a href="login.php">Login</a></li>
            <li><a href="registration.php">Register for an Account</a></li>
            <?php endif?>
            <li><a href="index.php">Index</a></li>
            <?php if(isset($_SESSION['user_data']['role']) && $_SESSION['user_data']['role'] == "Admin"): ?>
            <li><a href="users.php">Users</a></li>
            <?php endif?> 
        </ul>
        <form action="index.php" method="POST">
            <label for="title">Search</label><br>
            <input type="text" id="search" name="search" required><br><br>
            <input type="submit" id="search" name="submit" required>
        </form>
    </nav>



    
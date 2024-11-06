    <nav>
        <ul>
            <?php if(isset($_SESSION['user_data']['role'])): ?>
            <li><a href="logout.php">Logout</a></li>
            <?php else:?> 
            
            <li><a href="login.php">Login</a></li>
            <li><a href="registration.php">Register for an Account</a></li>
            <?php endif?>
            <li><a href="index.php">Index</a></li>
        </ul>
    </nav>

    
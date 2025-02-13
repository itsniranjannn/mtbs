<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Check if the user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NK THEATRE AND Movies</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="n.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="n.png">
</head>

<body>
    <!-- Header -->
    <header>
        <nav class="navbar"> 
            <div class="navbar-left">
                <img src="logo.png" alt="Nk" class="logo">
            </div>
            <div class="navbar-center">
                <ul class="navbar-menu">
                    <li><a href="home.php"><i class="fa fa-home"></i></a></li>
                    <li><a href="upcoming.php" class="btn">Movies</a></li>
                    <li><a href="movie.php" class="btn">Book Now</a></li>
                    <li><a href="about.php" class="btn">About Us</a></li>
                    <li>
                        <form class="search-form">
                            <input type="text" placeholder="Search..." class="search-input">
                            <button type="submit" class="search-button"><i class="fa fa-search"></i></button>
                        </form>
                    </li>
                </ul>
            </div>
           <div class="navbar-right">
    <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_username'])): ?>
        <!-- Display user/admin options -->
        <span class="btn">Welcome, <?php echo isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : htmlspecialchars($_SESSION['username']); ?>!</span>
        
        <?php if (isset($_SESSION['admin_username'])): ?>
            <!-- Admin specific button -->
            <a href="admin_panel.php" class="btn btn-warning">Admin Panel</a>
        <?php else: ?>
            <!-- User specific button -->
            <a href="user_dashboard.php" class="btn btn-primary">User Dashboard</a>
        <?php endif; ?>

        <a href="logout.php" class="btn">Logout</a>
    <?php else: ?>
        <!-- Display login and sign-up options -->
        <a href="login.php" class="btn">Log In</a>
        <a href="signup.php" class="btn">Sign Up</a>
    <?php endif; ?>
</div>

    </header>
    
</body>
</html>

<?php
include('db.php'); // Connect to the database
session_start(); // Start session for login tracking

// Check if the user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_username'])) {
    header("Location: land.php"); // Redirect to front.php if not logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="n.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="n.png">
    <title>About- NK Movies</title>

    <style>
    .about-us{
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
    }
    .container{
        color: beige;
    }
    </style>
</head>

<body>
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
                    <form class="search-form" method="GET" action="home.php">
    <input type="text" name="search_query" placeholder="Search..." class="search-input" value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>">
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
        </nav>
    </header>
    <?php if (!empty($search_results)): ?>
<div class="search-results" style="border: 1px solid #ddd; background: white; position: absolute; z-index: 1000; width: 100%; max-height: 300px; overflow-y: auto; padding: 10px;">
    <?php foreach ($search_results as $movie): ?>
        <div class="search-item" style="display: flex; align-items: center; margin-bottom: 10px; padding: 5px; border-bottom: 1px solid #f0f0f0;">
            <img src="<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" style="width: 40px; height: 60px; margin-right: 10px; object-fit: cover;">
            <div>
                <h6 style="margin: 0; font-size: 16px;"><?php echo htmlspecialchars($movie['title']); ?></h6>
                <small class="text-muted"><?php echo htmlspecialchars($movie['description']); ?></small>
                <br>
                <small class="text-muted">Language: <?php echo htmlspecialchars($movie['language']); ?></small>
                <br>
                <a href="movie.php?movie_id=<?php echo $movie['id']; ?>" class="btn btn-primary btn-sm" style="margin-top: 5px;">Book Now</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php elseif (isset($_GET['search_query']) && empty($search_results)): ?>
<div class="alert alert-warning" style="margin-top: 10px;">
    No results found for "<strong><?php echo htmlspecialchars($_GET['search_query']); ?></strong>"
</div>
<?php endif; ?>

    <main>
        <section class="about-us">
            <div class="container">
                <h1>About Us</h1>
                <p>
                    Welcome to NK Theater and Movies! <br>We are dedicated to providing you with an exceptional movie-going experience. <br>
                    We want to provide good experience to our customers 
                <hr>This movie ticket booking system can be deployed on any website offering tickets for movies, making the booking process easier and more convenient.
                <hr> If you have any questions or need assistance, feel free to contact us at <br>
                email:<a href="mailto:nkmoviestheater@gmail.com">nkmoviestheater@gmail.com</a>.
                <br>contact no: 01-4567890
                </p> <h4> This was make with the purpose to make movie booking more easier and faster.</h4>
            </div>
        </section>
    </main>

    <footer>
        <div class="foot">
            <a href="home.php">Home</a>
            <a href="movie.php">Book here</a>
            <a href="upcoming.php">Shows</a>
         <hr>
        </div>
        <p> &copy;2024 NK Theater And Movies | All Rights Are Reserved</p>
        <img src="logo.png" alt="logo" width="15%">
    </footer>
</body>
</html>

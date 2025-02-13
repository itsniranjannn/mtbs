<?php
session_start(); // Start the session to access session variables

// Check if the user is logged in (either as a user or admin)
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_username'])) {
    // Redirect to login if the user is not logged in
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="n.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="movie.css">
    <link rel="icon" type="image/x-icon" href="n.png">
    <style>
        h3{
            color: blueviolet;
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

    <div class="now-showing">
        <h2>NOW SHOWING & UPCOMING <span>Nepali Movies</span></h2>
        <div class="movie-list">
            <?php
            $nepali_movies = glob("images/nepali/*.jpg"); // Fetch all JPG images in the 'nepali' folder
            foreach ($nepali_movies as $movie) {
                echo '<div class="movie-card">
                        <img src="' . htmlspecialchars($movie) . '" alt="Movie">
                      </div>';
            }
            ?>
        </div>
    </div>

    <div class="now-showing">
        <h2>NOW SHOWING & UPCOMING <span>Hindi Movies</span></h2>
        <div class="movie-list">
            <?php
            $hindi_movies = glob("images/hindi/*.jpg"); // Fetch all JPG images in the 'hindi' folder
            foreach ($hindi_movies as $movie) {
                echo '<div class="movie-card">
                        <img src="' . htmlspecialchars($movie) . '" alt="Movie">
                      </div>';
            }
            ?>
        </div>
    </div>

    <div class="now-showing">
        <h2>NOW SHOWING & UPCOMING <span>English Movies</span></h2>
        <div class="movie-list">
            <?php
            $english_movies = glob("images/english/*.jpg"); // Fetch all JPG images in the 'english' folder
            foreach ($english_movies as $movie) {
                echo '<div class="movie-card">
                        <img src="' . htmlspecialchars($movie) . '" alt="Movie">
                      </div>';
            }
            ?>
        </div>
    </div>

    <!-- Booking Section -->
    <div class="booking-section text-center my-4">
        <h3>If you want to book the movie being shown, you can go through the button below:</h3>
        <a href="movie.php" class="btn btn-primary btn-lg">BOOK NOW</a>
    </div>

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

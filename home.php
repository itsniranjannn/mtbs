<?php
include('db.php'); // Connect to the database
session_start(); // Start session for login tracking

// Check if the user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

// Handle search functionality
$search_results = [];
if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
    $search_query = trim($_GET['search_query']);
    
    // Sanitize input to prevent SQL injection
    $search_query = mysqli_real_escape_string($conn, $search_query);

    // Query the database for matching movies
    $query = "
        SELECT id, title, description, language, poster
        FROM movies
        WHERE 
            title LIKE '%$search_query%' OR
            description LIKE '%$search_query%' OR
            language LIKE '%$search_query%'
    ";
    $result = mysqli_query($conn, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $search_results[] = $row;
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
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
         <form class="search-form" method="GET" action="home.php">
    <input type="text" name="search_query" placeholder="Search..." class="search-input" value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>">
    <button type="submit" class="search-button"><i class="fa fa-search"></i></button>
         </form>
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

    <!-- Carousel Section -->
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="t.jpg" class="d-block w-100" alt="Slide 1">
                <div class="carousel-caption d-none d-md-block"></div>
            </div>
            <div class="carousel-item">
                <img src="l.jpg" class="d-block w-100" alt="Slide 2">
                <div class="carousel-caption d-none d-md-block"></div>
            </div>
            <div class="carousel-item">
                <img src="a.jpg" class="d-block w-100" alt="Slide 3">
                <div class="carousel-caption d-none d-md-block"></div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <footer>
        <div class="foot">
            <a href="home.php">Home</a>
            <a href="movie.php">Book here</a>
            <a href="upcoming.php">Shows</a>
            <hr>
        </div>
        <p>&copy;2024 NK Theater And Movies | All Rights Reserved</p>
        <img src="logo.png" alt="logo" width="15%">
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

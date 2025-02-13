<?php
include('db.php');
session_start();

if (isset($_SESSION['user_id']) || isset($_SESSION['admin_username'])) {
    header("Location: home.php");
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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: linear-gradient( green, gold, red, black, #38a2d7, purple, black);
            color: white;
        }
        .header {
            background: #222;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header img {
            width: 150px;
        }
        .nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
            font-size: 1em;
        }
        .nav a:hover {
            text-decoration: underline;
        }
    
    .overlay {
        position: relative;
    }

    .overlay img {
        filter: brightness(50%);
    }
    .btn-primary:hover{
        color:black;
        background-color: purple;
    }

    .btn-primary {
    
        background-color: #007bff;
        border: none;
        padding: 10px 20px;
        font-size: 1rem;
    }
    .carousel-caption {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .carousel-caption h5, .carousel-caption p {
            text-shadow: 2px 2px 5px black;
        }

        .section-title {
            font-size: 1.8em;
            margin: 20px 0;
            text-align: center;
        }
        .slider-container {
            margin: 20px auto;
            padding: 20px 0;
            max-width: 90%;
            position: relative;
            overflow: hidden;
        }
        .slider-wrapper {
            display: flex;
            gap: 15px;
            transition: transform 0.5s ease;
        }
        .slider-item {
            flex: 0 0 auto;
            width: 200px;
            text-align: center;
            background: #222;
            border-radius: 8px;
            padding: 10px;
        }
        .slider-item img {
            width: 100%;
            border-radius: 8px;
        }
        .slider-item p {
            margin: 10px 0;
            font-size: 1em;
        }
        .slider-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            font-size: 24px;
            padding: 10px;
            cursor: pointer;
            z-index: 10;
        }
        .slider-button-left {
            left: 0;
        }
        .slider-button-right {
            right: 0;
        }
        .book-now-container {
            text-align: center;
            margin: 20px 0;
        }
        .book-now-button {
            background-color: #e50914; /* Example red color */
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2em;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .book-now-button:hover {
            background-color: #f40612;
        }
        .features-grid {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            padding: 20px 0;
        }
        .feature-card {
            flex: 0 0 300px; /* Fixed width for each card */
            scroll-snap-align: center; /* Snap cards to the center */
            background: #222;
            color: white;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
            padding: 10px;
        }
        .feature-card img {
            width: 100%;
            border-radius: 8px 8px 0 0;
        }
        .feature-card p {
            font-weight: bold;
            margin: 10px 0;
        }
        .feature-card span {
            font-size: 0.9em;
            color: #ccc;
        }
        .trailers-section {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px 0;
        }
        .trailer-card {
            width: 320px;
            text-align: center;
        }
        .trailer-card iframe {
            width: 100%;
            height: 180px;
            border-radius: 8px;
        }
        .trailer-card p {
            margin-top: 10px;
            font-weight: bold;
        }
        
        .footer {
            background: #222;
            padding: 10px;
            text-align: center;
            font-size: 0.9em;
        }
    </style>
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
                <img src="puspa.jpg" class="d-block w-100" alt="Pushpa 2">
                <div class="carousel-caption">
                    <h5>Pushpa: The Rule - Part 2</h5>
                    <p>The Rule Begins</p>
                    <a href="login.php" class="btn btn-primary">Buy Now</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="venom.jpg" class="d-block w-100" alt="venom">
                <div class="carousel-caption">
                    <h5>Venom: The Last Dance</h5>
                    <p>lets destory Knull's empire</p>
                    <a href="login.php" class="btn btn-primary">Buy Now</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="mufasa.jpg" class="d-block w-100" alt="Mufasa">
                <div class="carousel-caption">
                    <h5>Mufasa: The Lion King</h5>
                    <p>prequal of The lion King </p>
                    <a href="login.php" class="btn btn-primary">Buy Now</a>
                </div>
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

    <!-- Movies in Theaters Section -->
    <h2 class="section-title">Movies In Theaters</h2>
    <div class="slider-container">
        <button class="slider-button slider-button-left" onclick="moveSlider('left', 'theaters')">&#8249;</button>
        <div class="slider-wrapper" id="theatersSlider">
            <!-- Movies -->
            <div class="slider-item"><img src="n8.jpg" alt="Movie 1"><p>Purna Bahadur Ko Sarangi</p></div>
            <div class="slider-item"><img src="h2.jpg" alt="Movie 2"><p>Puspa 2</p></div>
            <div class="slider-item"><img src="h6.jpg" alt="Movie 3"><p>Vettaiyan</p></div>
            <div class="slider-item"><img src="e6.jpg" alt="Movie 4"><p>Jurrasic World</p></div>
            <div class="slider-item"><img src="e5.jpg" alt="Movie 5"><p>Venom: The last dance</p></div>
            <div class="slider-item"><img src="n4.jpg" alt="Movie 6"><p>Eklo</p></div>
            <div class="slider-item"><img src="n5.jpg" alt="Movie 7"><p>Balidaan</p></div>
            <div class="slider-item"><img src="n6.jpg" alt="Movie 8"><p>Gunyo Cholo</p></div>
            <div class="slider-item"><img src="h8.jpg" alt="Movie 9"><p>Baby JOhn</p></div>
            <div class="slider-item"><img src="e1.jpg" alt="Movie 10"><p>Kraven The Hunter</p></div>
        </div>
        <button class="slider-button slider-button-right" onclick="moveSlider('right', 'theaters')">&#8250;</button>
    </div>

    <!-- Book Now Button -->
    <div class="book-now-container">
        <a href="login.php?redirect=movie.php" class="book-now-button">Book Now</a>
    </div><hr>
    <!-- Upcoming Movies Section -->
    <h2 class="section-title">Upcoming Movies In Theater</h2>
    <div class="slider-container">
        <button class="slider-button slider-button-left" onclick="moveSlider('left', 'upcoming')">&#8249;</button>
        <div class="slider-wrapper" id="upcomingSlider">
            <!-- Upcoming Movies -->
            <div class="slider-item"><img src="u1.jpg" alt="Upcoming Movie 1"><p>The Batman II</p></div>
            <div class="slider-item"><img src="u2.jpg" alt="Upcoming Movie 2"><p>Coolie</p></div>
            <div class="slider-item"><img src="u3.jpg" alt="Upcoming Movie 3"><p>Kaithi</p></div>            
            <div class="slider-item"><img src="u4.jpg" alt="Upcoming Movie 4"><p>THE RAJA SAAB</p></div>
            <div class="slider-item"><img src="u5.jpg" alt="Upcoming Movie 5"><p>Mufasa: The Lion King</p></div>
            <div class="slider-item"><img src="u6.jpg" alt="Upcoming Movie 6"><p>The Conjuring: The Last Rites</p></div>
            <div class="slider-item"><img src="u7.jpg" alt="Upcoming Movie 7"><p>Game Changer</p></div>
            <div class="slider-item"><img src="u8.jpg" alt="Upcoming Movie 8"><p>Tunderbolts</p></div>
            <div class="slider-item"><img src="e3.jpg" alt="Upcoming Movie 9"><p>The Plantation</p></div>
            <div class="slider-item"><img src="u10.jpg" alt="Upcoming Movie 10"><p>Kantara: Chapter II</p></div>
        </div>
        <button class="slider-button slider-button-right" onclick="moveSlider('right', 'upcoming')">&#8250;</button>
    </div><hr>

    <!-- Features Section -->
    <h2 class="section-title">Upcoming Features</h2>
    <div class="features-grid">
        <div class="feature-card">
            <img src="h8.jpg" alt="Feature 1">
            <p>Baby John</p>
            <span>Baby John is an upcoming Indian Hindi-language action thriller film directed by Kalees that serves as a remake of Atlee's 2016 Tamil film Theri. The film stars Varun Dhawan in the title role, alongside Keerthy Suresh, Wamiqa Gabbi and Jackie Shroff, and is produced under Jio Studios, Cine1 Studios and A for Apple Productions.</span>
        </div>
        <div class="feature-card">
            <img src="h6.jpg" alt="Feature 2">
            <p>Vettaiyan</p>
            <span>Vettaiyan is yet another blockbuster for the SuperStar with highly performing co-stars. A strong messaging highlighting the evolution of irregular coaching centres that's minting money using the demands of education system in the nation. It highlights how partial our system is with privileged sector vs unprivileged sectors and sends a strong message on how people from slums are stereotyped by this Society. </span>
        </div>
        <div class="feature-card">
            <img src="n6.jpg" alt="Feature 3">
            <p>Gunyo Cholo</p>
            <span>Gulabi, a trans-woman is groomed by his patriarchal father to join the military. His father disowns him, leading him to live a life of prostitution in Kathmandu.Director: Samundra Bhatta,Writers:Samundra BhattaNajir HusenV. Vansay Zanubon. and Stars NAjir ,Sanchita Luitel</span>
        </div>
    </div>
<hr>
    <!-- Trailers Section -->
    <h2 class="section-title">Trailers</h2>
    <div class="trailers-section">
        <div class="trailer-card">
            <iframe src="https://www.youtube.com/embed/1kVK0MZlbI4" frameborder="0" allowfullscreen></iframe>
            <p>Puspa 2 Trailer</p>
        </div>
        <div class="trailer-card">
            <iframe src="https://www.youtube.com/embed/5zbtEmxEyGk" frameborder="0" allowfullscreen></iframe>
            <p>Purna Bahadur Ko Sarangi</p>
        </div>
        <div class="trailer-card">
            <iframe src="https://www.youtube.com/embed/idqfhoa4qu4" frameborder="0" allowfullscreen></iframe>
            <p>Venom: The last dance</p>
        </div>
    </div>
<hr>
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

    <script>
        function moveSlider(direction, section) {
            const slider = document.getElementById(section + 'Slider');
            const slideWidth = slider.querySelector('.slider-item').offsetWidth + 15; // Include gap
            const maxOffset = -(slider.children.length * slideWidth - slider.parentElement.offsetWidth);
            let currentTransform = window.getComputedStyle(slider).transform;
            let offset = 0;

            if (currentTransform !== 'none') {
                const matrix = new WebKitCSSMatrix(currentTransform);
                offset = matrix.m41;
            }

            if (direction === 'left') {
                if (offset < 0) {
                    offset += slideWidth;
                    if (offset > 0) offset = 0;
                }
            } else if (direction === 'right') {
                if (offset > maxOffset) {
                    offset -= slideWidth;
                    if (offset < maxOffset) offset = maxOffset;
                }
            }

            slider.style.transform = `translateX(${offset}px)`;
        }
    </script>
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

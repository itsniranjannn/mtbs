<?php
include('db.php'); // Database connection
session_start(); // Start session for user tracking

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch user details
$query_user = "SELECT username, email FROM users WHERE id = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($result_user->num_rows > 0) {
    $user_data = $result_user->fetch_assoc();
} else {
    die("User not found.");
}

// Generate tickets for approved bookings without tickets
$query_approved_booking = "
    SELECT 
        b.id AS booking_id,
        b.movie_id,
        b.total_price,
        b.category,
        b.status,
        b.seats,
        m.title AS movie_title,
        m.showtimes AS movie_showtime
    FROM bookings b
    JOIN movies m ON b.movie_id = m.id
    WHERE b.user_id = ? 
      AND b.status = 'approved' 
      AND NOT EXISTS (
          SELECT 1 
          FROM tickets t 
          WHERE t.booking_id = b.id
      )
";
$stmt_approved_booking = $conn->prepare($query_approved_booking);
$stmt_approved_booking->bind_param("i", $user_id); // Use user_id
$stmt_approved_booking->execute();
$result_approved_booking = $stmt_approved_booking->get_result();

while ($booking = $result_approved_booking->fetch_assoc()) {
    // Generate unique ticket code
    $ticket_code = strtoupper(uniqid('TICKET-'));

    // Insert ticket directly without redundant existence check
    $query_insert_ticket = "
        INSERT INTO tickets (booking_id, ticket_code, seat_number)
        VALUES (?, ?, ?)
    ";
    $stmt_insert_ticket = $conn->prepare($query_insert_ticket);
    $stmt_insert_ticket->bind_param(
        "iss",
        $booking['booking_id'],
        $ticket_code,
        $booking['seats'] // Ensure this is pre-validated
    );

    if (!$stmt_insert_ticket->execute()) {
        error_log("Failed to insert ticket for booking ID {$booking['booking_id']}: " . $stmt_insert_ticket->error);
    }
}

// Fetch user bookings (consider adding LIMIT for large datasets)
$query_booking = "
    SELECT 
        b.id AS booking_id,
        m.title AS movie_title,
        m.description AS movie_description,
        m.language AS movie_language,
        m.showtimes AS movie_showtime,
        m.price AS ticket_price,
        m.release_date AS movie_release_date,
        m.poster AS movie_poster,
        b.seats AS total_seats,
        b.total_price AS total_price,
        b.category AS category,
        b.status AS booking_status,
        t.ticket_code AS ticket_code,
        t.seat_number AS ticket_seat,
        t.id AS ticket_id
    FROM bookings b
    JOIN movies m ON b.movie_id = m.id
    LEFT JOIN tickets t ON b.id = t.booking_id
    WHERE b.user_id = ?
    ORDER BY b.booking_time DESC
";
$stmt_booking = $conn->prepare($query_booking);
$stmt_booking->bind_param("i", $user_id); // Use user_id
$stmt_booking->execute();
$result_booking = $stmt_booking->get_result();
;

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - NK Theatre and Movies</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="n.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="n.png">
    <style>
        .ticket-card {
            border: 2px solid #4CAF50;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            display: flex;
            align-items: center;
        }
        h2{
            color: antiquewhite;
        }
        h3 { color: brown; 
        }
        h1,h4 { color: aliceblue; 
                text-align: center;
            }
        .movie-poster { max-height: 150px; }
        .ticket-card { border: 2px solid #4CAF50; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9; } 
        /* General ticket container styling */
.container {
    margin-top: 20px;
}

/* Ticket card styling */
.ticket-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #1e3c72, #2a5298); /* Vibrant gradient background */
    color: #fff;
    border-radius: 15px;
    padding: 20px;
    margin: 10px 0;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.5);
    font-family: 'Arial', sans-serif;
}

/* Ticket heading */
.ticket-card h3 {
    font-size: 28px;
    font-weight: bold;
    color: #ffdc62; /* Golden yellow for heading */
    margin-bottom: 15px;
}

/* Ticket details text */
.ticket-card p {
    font-size: 16px;
    margin: 5px 0;
    color: #eaeaea;
}

.ticket-card p strong {
    color: #ffd700; /* Gold for important text */
}

/* Special styling for message */
.ticket-card p:last-child {
    font-style: italic;
    color: #f5f5f5;
}

/* Divider (dashed) in the middle of the ticket */
.ticket-card::before {
    content: "";
    position: absolute;
    height: 90%;
    width: 3px;
    background: repeating-linear-gradient(
        to bottom,
        #ffffff,
        #ffffff 5px,
        transparent 5px,
        transparent 10px
    );
    left: 50%;
    transform: translateX(-50%);
    z-index: 1;
}

/* Left section of the ticket */
.ticket-card .col-md-8 {
    flex: 1;
    padding-right: 20px;
    z-index: 2;
}

/* Movie poster */
.movie-poster {
    max-height: 300px;
    border-radius: 15px;
    border: 3px solid #ffdc62; /* Golden border for the poster */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
    z-index: 2;
}

/* Container heading */
.container h1 {
    font-size: 32px;
    font-weight: bold;
    color: #34495e;
    margin-bottom: 20px;
    text-align: center;
    color: #ffdc62; /* Golden yellow */
}

/* Hover effect for ticket card */
.ticket-card:hover {
    transform: scale(1.02);
    transition: 0.3s ease-in-out;
}

/* Animation for poster */
.movie-poster:hover {
    transform: rotate(-3deg) scale(1.05);
    transition: transform 0.3s ease-in-out;
}
/* Container styling */
.container {
    margin-top: 40px;
    font-family: 'Arial', sans-serif;
    color: #333;
}

/* Heading styling */
.container h1 {
    font-size: 32px;
    font-weight: bold;
     text-align: center;
    margin-bottom: 30px;
}

/* Card styling */
.card {
    background: linear-gradient(135deg, #ffffff, #f3f7fa); /* Subtle gradient for the card */
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease-in-out;
}

/* Card hover effect */
.card:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
}
.foot a:hover {
    color: #ffffff;
}
/* Card body */
.card-body {
    padding: 20px;
    color: #34495e;
}

/* Section title styling */
.card-body h3 {
    font-size: 24px;
       margin-bottom: 15px;
    border-bottom: 2px solid #1e3c72;
    display: inline-block;
    padding-bottom: 5px;
}

/* Text styling */
.card-body p {
    font-size: 16px;
    margin: 10px 0;
    color: #555;
}

/* Strong elements (labels) */
.card-body p strong {
    color: #1e3c72;
    font-weight: bold;
} /* General container styling */
.container {
    margin-top: 20px;
    font-family: 'Arial', sans-serif;
}

/* Section heading */
.container h2 {
    font-size: 28px;
    color: silver;
    margin-bottom: 20px;
    text-align: center;
    font-weight: bold;
    border-bottom: 2px solid #1e3c72;
    display: inline-block;
    padding-bottom: 5px;
}

/* Card styling */
.card {
    background: #ffffff;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

/* Hover effect for the card */
.card:hover {
    transform: scale(1.03);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

/* Card body styling */
.card-body {
    padding: 20px;
}

/* Booking details (left side) */
.card-body h5 {
    font-size: 22px;
    color: #1e3c72;
    margin-bottom: 10px;
    font-weight: bold;
}

.card-body p {
    font-size: 16px;
    color: #555;
    margin: 5px 0;
}

.card-body p strong {
    color: #1e3c72;
    font-weight: bold;
}

/* Movie poster (right side) */
.movie-poster {
    max-height: 200px;
    border-radius: 10px;
    border: 2px solid #ddd;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    display: block;
    margin: 0 auto;
}

/* Badge styles */
.badge {
    padding: 5px 10px;
    font-size: 14px;
    border-radius: 8px;
    text-transform: capitalize;
}

.badge-success {
    background-color: #28a745;
    color: #fff;
}

.badge-danger {
    background-color: #dc3545;
    color: #fff;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

/* Row and column layout */
.row {
    margin: 0 -15px; /* Adjust spacing between columns */
}

.col-md-6 {
    padding: 0 15px;
    margin-bottom: 20px;
}

/* Responsive design */
@media (max-width: 768px) {
    .card {
        margin-bottom: 20px;
    }
    .movie-poster {
        max-height: 150px;
    }
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
                        <form class="search-form">
                            <input type="text" placeholder="Search..." class="search-input">
                            <button type="submit" class="search-button"><i class="fa fa-search"></i></button>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="navbar-right">
                <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_username'])): ?>
                    <span class="btn">Welcome, <?php echo htmlspecialchars($user_data['username']); ?>!</span>
                    <a href="user_dashboard.php" class="btn btn-primary">User Dashboard</a>
                    <a href="logout.php" class="btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn">Log In</a>
                    <a href="signup.php" class="btn">Sign Up</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    
    <!-- User Details Section -->
    <div class="container mt-5">
        <h1>User Dashboard</h1>
        <div class="card mb-4">
            <div class="card-body">
                <h3>User Details</h3>
                <p><strong>User ID:</strong> <?php echo htmlspecialchars($user_id); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user_data['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
            </div>
        </div>
        <hr>

        <!-- Booking details -->
<?php
$has_pending_booking = false;// Check if there are any pending bookings

$result_booking->data_seek(0); 
while ($booking = $result_booking->fetch_assoc()) {
    if ($booking['booking_status'] == 'pending') {
        $has_pending_booking = true;
        break;
    }
}

if ($has_pending_booking): ?>
    <h2>Pending Bookings</h2>
    <div class="row">
    <?php $result_booking->data_seek(0); ?>
    <?php while ($booking = $result_booking->fetch_assoc()): ?>
        <?php if ($booking['booking_status'] == 'pending'): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Left Side: Booking Details -->
                                <h5 class="card-title"><?php echo htmlspecialchars($booking['movie_title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($booking['movie_description']); ?></p>
                                <p><strong>Language:</strong> <?php echo htmlspecialchars($booking['movie_language']); ?></p>
                                    <p><strong>Showtime:</strong> <?php echo htmlspecialchars($booking['movie_showtime']); ?></p>
                                    <p><strong>Release Date:</strong> <?php echo htmlspecialchars($booking['movie_release_date']); ?></p>
                                    <p><strong>Seat no:</strong> <?php echo htmlspecialchars($booking['total_seats']); ?></p>
                                    <p><strong>Total Price:</strong> $<?php echo htmlspecialchars($booking['total_price']); ?></p>
                                    <p><strong>Category:</strong> <?php echo htmlspecialchars($booking['category']); ?></p>
                                    <p><strong>Status:</strong></p>
                                        <?php 
                                            if ($booking['booking_status'] == 'approved') {
                                                echo '<span class="badge badge-success">Approved</span>';
                                            } elseif ($booking['booking_status'] == 'rejected') {
                                                echo '<span class="badge badge-danger">Rejected</span>';
                                            } else {
                                                echo '<span class="badge badge-warning">Pending</span>';
                                            }
                                        ?>
                                <form action="cancel_booking.php" method="POST">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                    <button type="submit" class="btn btn-danger">Cancel Booking</button>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <?php if (!empty($booking['movie_poster'])): ?>
                                    <img src="http://localhost/mbs/uploads/<?php echo htmlspecialchars($booking['movie_poster']); ?>" 
                                         alt="Movie Poster" 
                                         class="img-fluid movie-poster" 
                                         style="max-height: 300px; border-radius: 20px;">
                                <?php else: ?>
                                    <p>No Poster Available</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endwhile; ?>
</div>
<?php else: ?>
    <h4>No pending bookings.</h4>
<?php endif; ?><hr>

<!-- ticket Section -->
<div class="container mt-5">
    <?php 
    $result_booking->data_seek(0); // Reset result pointer
    while ($booking = $result_booking->fetch_assoc()): 
        if ($booking['ticket_code']): ?>
        <h1>Tickets</h1>
            <div class="row ticket-card">
                <div class="col-md-8">
                    <h3>🎟 Ticket Details</h3>
                    <p><strong>Ticket Code:</strong> <?php echo htmlspecialchars($booking['ticket_code']); ?></p>
                    <p><strong>Ticket ID:</strong> <?php echo htmlspecialchars($booking['ticket_id']); ?></p>
                    <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($booking['booking_id']); ?></p>
                    <p><strong>Title:</strong> <?php echo htmlspecialchars($booking['movie_title']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($booking['category']); ?></p>
                    <p><strong>Showtime:</strong> <?php echo htmlspecialchars($booking['movie_showtime']); ?></p>
                    <p><strong>Total Price:</strong> $<?php echo htmlspecialchars($booking['total_price']); ?></p>
                    <p><strong>Seat no:</strong> <?php echo htmlspecialchars($booking['total_seats']); ?></p>
                    <p><strong>Message:</strong> Please show this copy ticket to the counter of theater.<br>Ensure your payment and get real ticket to secure your seats.
                <hr>Enjoy your day!!!</p>
                    <a href="download_ticket.php?ticket_code=<?php echo urlencode($booking['ticket_code']); ?>" class="btn btn-success">Get Your copy of Ticket</a>
                </div>
                <?php if (!empty($booking['movie_poster'])): ?>
    <img src="http://localhost/mbs/uploads/<?php echo htmlspecialchars($booking['movie_poster']); ?>" 
         alt="Movie Poster" 
         class="img-fluid movie-poster" style="max-height: 400px; border-radius: 20px;">
    <?php else: ?>
    <p>No Poster Available</p>
    <?php endif; ?>
            </div>
    <?php endif; endwhile; ?>
</div>
<hr>

    <!-- Footer -->
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
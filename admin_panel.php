<?php
session_start(); // Start the session at the top

// Check if the user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_username'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit;
}

include('header.php');
$conn = new mysqli('localhost', 'root', '', 'movie_booking');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the uploads directory exists
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle POST request for adding a movie
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $language = $_POST['language'];
    $showtimes = $_POST['showtimes']; // This is an array
    $poster = $_FILES['poster']['name'];
    $release_date = $_POST['release_date'];

    // Combine the showtimes array into a comma-separated string
    $showtimesString = implode(',', $showtimes);

    $uploadFile = $uploadDir . basename($poster);

    if (is_uploaded_file($_FILES['poster']['tmp_name'])) {
        if (move_uploaded_file($_FILES['poster']['tmp_name'], $uploadFile)) {
            $stmt = $conn->prepare("INSERT INTO movies (title, description, language, showtimes, poster, release_date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $title, $description, $language, $showtimesString, $poster, $release_date);

            if ($stmt->execute()) {
                echo "<p class='alert alert-success'>Movie added successfully!</p>";
            } else {
                echo "<p class='alert alert-danger'>Error adding movie: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p class='alert alert-danger'>Error moving uploaded file.</p>";
        }
    } else {
        echo "<p class='alert alert-danger'>No file uploaded or upload failed.</p>";
    }
}

// Determine the section to display based on the query parameter
$section = isset($_GET['section']) ? $_GET['section'] : 'add-movie';

// Fetch data needed for specific sections
if ($section === 'delete-movie') {
    $moviesResult = $conn->query("SELECT * FROM movies");
} elseif ($section === 'manage-bookings') {
    $limit = 5; // Show 5 bookings per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max($page, 1); // Ensure the page number is at least 1
    $offset = ($page - 1) * $limit;

    // Fetch total bookings count for pagination
    $totalBookingsResult = $conn->query("SELECT COUNT(*) AS total FROM bookings");
    $totalBookings = $totalBookingsResult->fetch_assoc()['total'];
    $totalPages = ceil($totalBookings / $limit);

    // Fetch approved bookings first, followed by others, with pagination
    $bookingsResult = $conn->query("
        SELECT b.*, m.title AS movie_title
        FROM bookings b
        LEFT JOIN movies m ON b.movie_id = m.id
        ORDER BY
            CASE
                WHEN b.status = 'pending' THEN 1
                WHEN b.status = 'approved' THEN 2
                WHEN b.status = 'rejected' THEN 3
            END
        LIMIT $limit OFFSET $offset
    ");
} elseif ($section === 'manage-users') {
    $usersResult = $conn->query("SELECT * FROM users");
}

if (isset($_GET['message'])) {
    $message = urldecode($_GET['message']);
} else {
    $message = "";
}
?>

<style>
/* Improved Sidebar Styles */
#sidebar {
    background-color: #1a1a1a;
    height: 100vh;
    padding-top: 20px;
    font-family: 'Arial', sans-serif;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    position: fixed;
    width: 250px;
}

#sidebar .nav-link {
    color: #b3b3b3;
    padding: 12px 20px;
    font-size: 16px;
    font-weight: 500;
    border-radius: 4px;
    transition: all 0.3s ease;
    margin: 5px 15px;
}

#sidebar .nav-link:hover {
    background-color: #333;
    color: #fff;
    transform: translateX(5px);
}

#sidebar .nav-item.active > .nav-link {
    background-color: #4a148c;
    color: #fff;
}

/* Main Content Area */
main {
    margin-left: 250px;
    padding: 30px;
    background-color: #f5f5f5;
    min-height: 100vh;
}

/* Improved Card Styles */
.card {
    background-color: #fff;
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.card-img-top {
    height: 200px;
    object-fit: cover;
    border-bottom: 1px solid #eee;
}

.card-body {
    padding: 20px;
}

.card-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

.card-text {
    font-size: 14px;
    color: #666;
    line-height: 1.6;
}

.card .btn {
    font-size: 14px;
    padding: 8px 16px;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.card .btn-primary {
    background-color: #4a148c;
    border: none;
}

.card .btn-primary:hover {
    background-color: #6a1b9a;
}

.card .btn-danger {
    background-color: #e53935;
    border: none;
}

.card .btn-danger:hover {
    background-color: #c62828;
}

/* Table Styles */
.table {
    background-color: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.table th {
    background-color: #4a148c;
    color: #fff;
    font-weight: 500;
}

.table td, .table th {
    padding: 12px 15px;
    vertical-align: middle;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: #f9f9f9;
}

/* Pagination Styles */
.pagination {
    margin-top: 20px;
}

.pagination .page-item.active .page-link {
    background-color: #4a148c;
    border-color: #4a148c;
}

.pagination .page-link {
    color: #4a148c;
}

.pagination .page-link:hover {
    color: #6a1b9a;
}

/* Notification Styles */
.notification {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    background-color: #4a148c;
    color: #fff;
    text-align: center;
}
</style>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar" id="sidebar">
            <div class="sidebar-sticky">
                <ul class="nav flex-column" id="nav-list">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $section === 'add-movie' ? 'active' : ''; ?>" href="?section=add-movie">
                            Add Movies
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $section === 'delete-movie' ? 'active' : ''; ?>" href="?section=delete-movie">
                            Manage Movies
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $section === 'movie-stats' ? 'active' : ''; ?>" href="?section=movie-stats">
                            Movie Statistics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $section === 'manage-bookings' ? 'active' : ''; ?>" href="?section=manage-bookings">
                            Manage Bookings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $section === 'manage-users' ? 'active' : ''; ?>" href="?section=manage-users">
                            Manage Users
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <h1 class="mt-5">Admin Panel</h1>
            <hr>

            <!-- Generate PDF Button -->
            <a href="generate_pdf.php" class="generate-pdf-btn">Generate PDF</a>

            <!-- Add Movie Section -->
            <?php if ($section === 'add-movie') { ?>
                <div id="add-movie" class="section">
                    <h3>Add Movie</h3>
                    <form action="admin_panel.php" method="POST" enctype="multipart/form-data" class="add-movie-form">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="language">Language</label>
                            <select class="form-control" name="language" required>
                                <option value="Nepali">Nepali</option>
                                <option value="Hindi">Hindi</option>
                                <option value="English">English</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="showtimes">Showtimes (Comma separated)</label>
                            <input type="time" class="form-control" name="showtimes[]" multiple required>
                        </div>
                        <div class="form-group">
                            <label for="poster">Poster</label>
                            <input type="file" class="form-control" name="poster" required>
                        </div>
                        <div class="form-group">
                            <label for="release_date">Release Date</label>
                            <input type="date" class="form-control" name="release_date" id="release_date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Movie</button>
                    </form>
                </div>
            <?php } ?>

         <!-- Delete and Edit Movies Section -->
<?php if ($section === 'delete-movie') { ?>
<div id="delete-movie" class="section">
    <h3>Delete or Edit Movies</h3>
    <div class="row">
        <?php 
        $limit = 6; // Movies per page
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $moviesResult = $conn->query("SELECT * FROM movies LIMIT $limit OFFSET $offset");
        $totalMoviesResult = $conn->query("SELECT COUNT(*) AS total FROM movies");
        $totalMovies = $totalMoviesResult->fetch_assoc()['total'];
        $totalPages = ceil($totalMovies / $limit);

        while ($movie = $moviesResult->fetch_assoc()) { ?>
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <img src="uploads/<?php echo $movie['poster']; ?>" class="card-img-top" alt="<?php echo $movie['title']; ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $movie['title']; ?></h5>
                        <p class="card-text"><?php echo $movie['description']; ?></p>
                        <p><strong>Release Date:</strong> <?php echo $movie['release_date']; ?></p>
                        <div class="d-flex justify-content-between">
                            <a href="edit_movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>


    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?section=delete-movie&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php } ?>
        </ul>
    </nav>
</div>
<?php } ?>

<?php
if ($section === 'movie-stats') {
    // Fetch movie details with calculated booking stats, including seat numbers
    $moviesResult = $conn->query("
        SELECT 
            m.id AS movie_id,
            m.title,
            m.price,
            m.showtimes,
            m.release_date,
            GROUP_CONCAT(b.seats) AS booked_seats,  /* Concatenate seat numbers for this movie */
            COALESCE(SUM(b.seats), 0) AS total_booked_seats,
                      COALESCE(SUM(b.total_price), 0) AS total_collection  /* Calculate total collection from bookings */
        FROM movies m
        LEFT JOIN bookings b ON m.id = b.movie_id AND b.status = 'approved'
        GROUP BY m.id
    ");
    
    // Check if the query was successful
    if ($moviesResult === false) {
        die("Error executing query: " . $conn->error);
    }
    ?>
    <div id="movie-stats" class="section">
        <h3>Movie Statistics</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Movie ID</th>
                    <th>Title</th>
                    <th>Showtimes</th>
                    <th>Release Date</th>
                    <th>Booked Seats</th>
                    <th>Total Collection</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($movie = $moviesResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $movie['movie_id']; ?></td>
                        <td><?php echo $movie['title']; ?></td>
                        <td><?php echo $movie['showtimes']; ?></td>
                        <td><?php echo $movie['release_date']; ?></td>
                        <td>
                            <?php
                            // Display the seat numbers (booked seats) as a comma-separated list
                            echo $movie['booked_seats'] ? $movie['booked_seats'] : "No bookings yet";
                            ?>
                        </td>
                        <td>Rs<?php echo number_format($movie['total_collection'], 2); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>


            <!-- Manage Bookings Section -->
<?php if (!empty($message)): ?>
    <div class="notification">
        <?php echo $message; ?>
    </div>
<?php endif; ?>
            <?php if ($section === 'manage-bookings') { ?>
                <div id="manage-bookings" class="section">
                    <h3>Manage Bookings</h3>
                    <table class="table table-striped">
                    <thead>
    <tr>
        <th>Booking ID</th>
        <th>Movie Title</th>
        <th>Category</th>
        <th>Seats</th>
        <th>Total Price</th>
        <th>Email</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
<?php while ($booking = $bookingsResult->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $booking['id']; ?></td>
        <td><?php echo $booking['movie_title']; ?></td>
        <td><?php echo $booking['category']; ?></td>
        <td><?php echo $booking['seats']; ?></td>
        <td><?php echo $booking['total_price']; ?></td>
        <td><?php echo $booking['email']; ?></td>
        <td>
            <?php 
            if ($booking['status'] === 'approved') {
                echo "<span class='badge badge-success'>Approved</span>";
            } elseif ($booking['status'] === 'rejected') {
                echo "<span class='badge badge-danger'>Rejected</span>";
            } elseif ($booking['status'] === 'cancelled') {
                echo "<span class='badge badge-secondary'>Cancelled</span>";
            } else {
                echo "<span class='badge badge-warning'>Pending</span>";
            }
            ?>
        </td>
        <td>
            <?php if ($booking['status'] === 'pending') { ?>
                <a href="approve_booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-info btn-sm">Approve</a>
                <a href="reject_booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
            <?php } elseif ($booking['status'] === 'cancelled') { ?>
                <span class="btn btn-secondary btn-sm disabled">No Action</span>
            <?php } else { ?>
                <span class="btn btn-secondary btn-sm disabled">No Action</span>
            <?php } ?>
        </td>
    </tr>
<?php } ?>

</tbody>
</table>
                    <!-- Pagination Controls -->
                    <nav>
                        <ul class="pagination">
                            <?php if ($page > 1) { ?>
                                <li class="page-item">
                                    <a class="page-link" href="?section=manage-bookings&page=<?php echo $page - 1; ?>">Previous</a>
                                </li>
                            <?php } ?>
                            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?section=manage-bookings&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php } ?>
                            <?php if ($page < $totalPages) { ?>
                                <li class="page-item">
                                    <a class="page-link" href="?section=manage-bookings&page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </nav>
                </div>
            <?php } ?>

            <!-- Manage Users Section -->
            <?php if ($section === 'manage-users') { ?>
                <div id="manage-users" class="section">
                    <h3>Manage Users</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $usersResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td>
                                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </main>
    </div>
</div>
        </main>
    </div>
</div>

<script>
    // Set the minimum release date to today
    document.getElementById('release_date').setAttribute('min', new Date().toISOString().split('T')[0]);

    // Handle notifications
    const notification = document.querySelector('.notification');
    if (notification) {
        notification.style.display = 'block'; // Ensure the notification is shown
        setTimeout(() => {
            notification.style.display = 'none'; // Hide it after 5 seconds
        }, 5000);
    }
</script>

<?php
include('footer.php');
?>
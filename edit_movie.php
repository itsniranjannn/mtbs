<?php
include 'db.php'; // Include your database connection

// Check if the movie ID is provided
if (!isset($_GET['id'])) {
    die('Movie ID not provided.');
}

$movieId = (int)$_GET['id'];

// Fetch movie details
$movieQuery = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$movieQuery->bind_param('i', $movieId);
$movieQuery->execute();
$result = $movieQuery->get_result();

if ($result->num_rows === 0) {
    die('Movie not found.');
}

$movie = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $releaseDate = $_POST['release_date'];
    $showtime = $_POST['showtime']; // Added Showtime field

    // Handle file upload if a new poster is provided
    if (!empty($_FILES['poster']['name'])) {
        $targetDir = 'uploads/';
        $targetFile = $targetDir . basename($_FILES['poster']['name']);
        move_uploaded_file($_FILES['poster']['tmp_name'], $targetFile);
        $poster = $_FILES['poster']['name'];
    } else {
        $poster = $movie['poster'];
    }

    // Update the movie in the database
    $updateQuery = $conn->prepare("UPDATE movies SET title = ?, description = ?, release_date = ?, showtimes = ?, poster = ? WHERE id = ?");
    $updateQuery->bind_param('sssssi', $title, $description, $releaseDate, $showtime, $poster, $movieId);

    if ($updateQuery->execute()) {
        echo '<script>alert("Movie updated successfully."); window.location.href = "admin_panel.php?section=delete-movie";</script>';
    } else {
        echo '<script>alert("Error updating movie.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('nk.png?text=Movies+Background'); /* Replace with your movie-themed image */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #fff;
        }
        .form-container {
            background: rgba(26, 25, 25, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        .form-container h3 {
            color:rgb(255, 251, 251);
        }
        .btn-primary {
            background-color:rgb(23, 0, 78);
            border-color:rgb(0, 0, 0);
        }
        .btn-primary:hover {
            background-color:rgba(255, 0, 0, 0.92);
        }
        .btn-secondary {
            background-color: #34495e;
            border-color:rgb(255, 255, 255);
        }
        .btn-secondary:hover {
            background-color:rgb(0, 0, 0);
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 form-container">
            <h3 class="text-center">Edit Movie</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $movie['title']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $movie['description']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="release_date">Release Date</label>
                    <input type="date" class="form-control" id="release_date" name="release_date" value="<?php echo $movie['release_date']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="showtime">Showtime</label>
                    <input type="time" class="form-control" id="showtime" name="showtime" value="<?php echo $movie['showtimes']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="poster">Poster</label>
                    <input type="file" class="form-control-file" id="poster" name="poster">
                    <small>Current Poster: <strong><?php echo $movie['poster']; ?></strong></small>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update Movie</button>
                <a href="admin_panel.php?section=delete-movie" class="btn btn-secondary btn-block">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
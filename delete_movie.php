<?php
$conn = new mysqli('localhost', 'root', '', 'try');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin_panel.php?message=Movie deleted successfully");
    } else {
        echo "Error deleting movie.";
    }
}
?>

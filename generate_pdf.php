<?php

require 'vendor/fpdf/src/Fpdf/Fpdf.php';

use Fpdf\Fpdf;

$pdf = new FPDF();
$pdf->AddPage();
$pdf->Image('logo.png', 20, 10, 30); 


// Database connection
$conn = new mysqli('localhost', 'root', '', 'try');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total bookings
$totalBookingsResult = $conn->query("SELECT COUNT(*) AS total FROM bookings");
if (!$totalBookingsResult) {
    die("Query Error (Total Bookings): " . $conn->error);
}
$totalBookings = $totalBookingsResult->fetch_assoc()['total'] ?? 0;

// Fetch total approvals
$totalApprovedResult = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE status = 'approved'");
if (!$totalApprovedResult) {
    die("Query Error (Total Approvals): " . $conn->error);
}
$totalApproved = $totalApprovedResult->fetch_assoc()['total'] ?? 0;

// Fetch total rejections
$totalRejectedResult = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE status = 'rejected'");
if (!$totalRejectedResult) {
    die("Query Error (Total Rejections): " . $conn->error);
}
$totalRejected = $totalRejectedResult->fetch_assoc()['total'] ?? 0;

// Fetch total registered users
$totalUsersResult = $conn->query("SELECT COUNT(*) AS total FROM users");
if (!$totalUsersResult) {
    die("Query Error (Total Users): " . $conn->error);
}
$totalUsers = $totalUsersResult->fetch_assoc()['total'] ?? 0;

// Set fonts and styles
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(0, 0, 0); // Set text color to black

// Title: Admin Panel Report
$pdf->Cell(0, 10, 'NK Movies & Theater Report', 0, 1, 'C');
$pdf->Ln(10);

// Registered Users Table Section
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Registered Users:', 0, 1);
$pdf->SetFont('Arial', '', 10);

// Table Header for Users
$pdf->Cell(40, 10, 'User ID', 1, 0, 'C');
$pdf->Cell(80, 10, 'Email', 1, 0, 'C');
$pdf->Cell(40, 10, 'Username', 1, 1, 'C');
$pdf->SetFont('Arial', '', 10);

// Fetch and display user data
$usersResult = $conn->query("SELECT id, email, username FROM users");
while ($user = $usersResult->fetch_assoc()) {
    $pdf->Cell(40, 10, $user['id'], 1, 0, 'C');
    $pdf->Cell(80, 10, $user['email'], 1, 0, 'C');
    $pdf->Cell(40, 10, $user['username'], 1, 1, 'C');
}

// Add space
$pdf->Ln(10);

// Booking Information
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Total Bookings: $totalBookings", 0, 1);
$pdf->Cell(0, 10, "Total Approvals: $totalApproved", 0, 1);
$pdf->Cell(0, 10, "Total Rejections: $totalRejected", 0, 1);
$pdf->Cell(0, 10, "Total Registered Users: $totalUsers", 0, 1);

// Add space
$pdf->Ln(10);

// Now Showing Section
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Now Showing in NK Movies & Theatre:', 0, 1);

// Movies Table Header
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 10, 'Movie Title', 1, 0, 'C');
$pdf->Cell(60, 10, 'Release Date', 1, 0, 'C');
$pdf->Cell(60, 10, 'Language', 1, 1, 'C');

// Fetch and display movie data
$pdf->SetFont('Arial', '', 10);
$moviesResult = $conn->query("SELECT title, release_date, language FROM movies");
while ($movie = $moviesResult->fetch_assoc()) {
    $pdf->Cell(60, 10, $movie['title'], 1, 0, 'C');
    $pdf->Cell(60, 10, $movie['release_date'], 1, 0, 'C');
    $pdf->Cell(60, 10, $movie['language'], 1, 1, 'C');
}

// Output the PDF
$pdf->Output('D', 'nkmovies&theater_report.pdf');

// Close the database connection
$conn->close();
?>

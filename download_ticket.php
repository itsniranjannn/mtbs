<?php
require 'vendor/fpdf/src/Fpdf/Fpdf.php';

use Fpdf\Fpdf;


// Database connection
$conn = new mysqli('localhost', 'root', '', 'try');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Generate the PDF ticket with design elements
function generateTicketPDF($ticket_id, $ticket_code, $movie_title, $category, $movie_showtime, $total_price, $seats, $movie_poster_path) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->Image('logo.png', 20, 10, 30); 
    // Ticket Title
    $pdf->SetFont('Arial', 'B', 24);
    $pdf->Cell(0, 10, 'MOVIE TICKET', 0, 1, 'C');
    $pdf->Ln(10);
    
    // Draw a line
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(5);
    
    // Ticket Number Section
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(40, 10, 'Ticket No: ' . $ticket_code, 0, 1);
    
    // Movie Details Section
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'Movie: ' . $movie_title, 0, 1);
    
    // Theater and Seat Information
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(40, 10, 'Theater: 1', 0, 1); // Add dynamic theater number if available
    $pdf->Cell(40, 10, 'Seat: ' . $seats, 0, 1);
    $pdf->Cell(40, 10, 'Category: ' . $category, 0, 1);
    $pdf->Cell(40, 10, 'Showtime: ' . $movie_showtime, 0, 1);
    $pdf->Cell(40, 10, 'Date: ' . date('Y/m/d'), 0, 1);
    
    // Total Price Section
    $pdf->Ln(5);
    $pdf->Cell(40, 10, 'Total Price: $' . $total_price, 0, 1);
    

    
    // Place the movie poster image on the right side
    if (file_exists($movie_poster_path)) {
        // Adjust the position to place the image on the right side
        $pdf->SetXY(120, $pdf->GetY()); // X position is adjusted for the right side
        $pdf->Image($movie_poster_path, 140, 50, 60, 90); // Adjust the position and size of the image
    }
    
    // Footer (Date)
    $pdf->Ln(30);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, 'Date: ' . date('Y/m/d'), 0, 1, 'C');

    // Output PDF
    $pdf->Output('D', 'ticket_' . $ticket_code . '.pdf');
}

// Fetch the ticket details from the database based on the ticket code
if (isset($_GET['ticket_code'])) {
    $ticket_code = $_GET['ticket_code'];

    // Assuming you have a function to fetch ticket details
    $query_ticket = "
        SELECT 
            t.id AS ticket_id,
            t.ticket_code,
            b.movie_id,
            b.seats,
            b.total_price,
            b.category,
            m.title AS movie_title,
            m.showtimes AS movie_showtime,
            m.poster AS movie_poster
        FROM tickets t
        JOIN bookings b ON t.booking_id = b.id
        JOIN movies m ON b.movie_id = m.id
        WHERE t.ticket_code = ?
    ";

    $stmt_ticket = $conn->prepare($query_ticket);
    $stmt_ticket->bind_param("s", $ticket_code);
    $stmt_ticket->execute();
    $result_ticket = $stmt_ticket->get_result();

    if ($result_ticket->num_rows > 0) {
        $ticket = $result_ticket->fetch_assoc();
        
        // Get the movie poster path
        $movie_poster_path = 'uploads/' . $ticket['movie_poster'];
        
        // Call the function to generate PDF
        generateTicketPDF(
            $ticket['ticket_id'],
            $ticket['ticket_code'],
            $ticket['movie_title'],
            $ticket['category'],
            $ticket['movie_showtime'],
            $ticket['total_price'],
            $ticket['seats'],
            $movie_poster_path
        );
    } else {
        die("Ticket not found.");
    }
} else {
    die("Invalid request.");
}

$conn->close();
?>

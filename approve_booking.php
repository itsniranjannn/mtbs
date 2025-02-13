<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer
include('db.php'); // Database connection
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// Email function
function sendEmail($recipient, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Update with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'nkmoviestheater@gmail.com'; // Update with your email
        $mail->Password = 'clao qnfn wyfl tlmp'; // Update with your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient details
        $mail->setFrom('nkmoviestheater@gmail.com', 'NK Theatre');
        $mail->addAddress($recipient);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        error_log("Email error: {$mail->ErrorInfo}");
        throw new Exception("Email sending failed: {$mail->ErrorInfo}");
    }
}

// Approve booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = filter_var($_POST['booking_id'], FILTER_SANITIZE_NUMBER_INT); // Sanitize input

    // Fetch the booking details from the database
    $stmt = $conn->prepare("SELECT b.*, u.email, m.title FROM bookings b 
                            JOIN users u ON b.user_id = u.id
                            JOIN movies m ON b.movie_id = m.id
                            WHERE b.id = ? AND b.user_id IS NOT NULL");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $booking = $result->fetch_assoc();

        if ($booking['status'] === 'approved') {
            $message = "This booking has already been approved.";
        } elseif ($booking['status'] === 'rejected') {
            $message = "This booking has already been rejected.";
        } else {
            // Update booking status to 'approved'
            $update_stmt = $conn->prepare("UPDATE bookings SET status = 'approved' WHERE id = ?");
            $update_stmt->bind_param("i", $booking_id);

            try {
                if ($update_stmt->execute()) {
                    // Generate ticket code (example)
                    $ticket_code = strtoupper(bin2hex(random_bytes(4))); // Random 8-character ticket code
                    
                    // Send confirmation email to the user
                    $subject = "Your Booking Has Been Approved!";
                    $body = "
                        <div style='font-family: Arial, sans-serif; color: #333; background-color: #f4f4f4; padding: 20px;'>
                            <table style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px;'>
                                <thead>
                                    <tr>
                                        <th style='background-color: #007BFF; color: #ffffff; padding: 20px; text-align: center;'>
                                            <h1 style='margin: 0;'>🎥 NK Movies & Theatre</h1>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style='padding: 20px;'>
                                            <p style='font-size: 18px;'>Dear Beloved Customer,</p>
                                            <p style='font-size: 16px;'>Your booking with ID <strong>{$booking_id}</strong> has been approved!</p>
                                            <p style='font-size: 16px;'>Here are your booking details:</p>
                                            <ul style='font-size: 16px;'>
                                                <li><strong>Movie Title:</strong> " . htmlspecialchars($booking['title']) . "</li>
                                                <li><strong>Booking Id:</strong> " . htmlspecialchars($booking['id']) . "</li>
                                                <li><strong>Booking Time:</strong> " . htmlspecialchars($booking['booking_time']) . "</li>
                                                <li><strong>Total Price:</strong> $" . htmlspecialchars($booking['total_price']) . "</li>
                                                <li><strong>Seats:</strong> " . htmlspecialchars($booking['seats']) . "</li>
                                                <li><strong>Email:</strong> " . htmlspecialchars($booking['email']) . "</li>
                                                <li><strong>Ticket Code:</strong> " . $ticket_code . "</li>
                                            </ul>
                                            <p style='font-size: 16px;'>Please show this email or go to user dashboard and download your ticket copy as proof at the cinema hall. Enjoy the movie!</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='background-color: #f4f4f4; text-align: center; padding: 20px;'>
                                            <p style='font-size: 14px; color: #777;'>This email was sent from the NK Theatre system. Please do not reply to this email.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    ";
                    sendEmail($booking['email'], $subject, $body);
                    $message = "Booking approved and email sent.";
                } else {
                    $message = "Error: Unable to approve booking.";
                }
            } catch (Exception $e) {
                $message = "Booking approved, but email sending failed. Error: {$e->getMessage()}";
            }
        }
    } else {
        $message = "Booking not found or user_id is NULL.";
    }

    // Redirect with success message
    header("Location: admin_panel.php?section=manage-bookings&message=" . urlencode($message));
    exit;
}

// Fetch pending bookings with movie titles (excluding NULL user_id)
$query = "
    SELECT b.*, u.email, m.title 
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN movies m ON b.movie_id = m.id
    WHERE b.status = 'pending' AND b.user_id IS NOT NULL
";
$result = $conn->query($query);
if (!$result) {
    die("Error fetching bookings: " . $conn->error);  // Debug if query fails
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Booking - NK MOVIES & THEATER</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('a.png?text=Bookings+Background');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #fff;
        }
        h1{
            color: silver;
        }
        .form-container {
            background: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            max-width: 900px;
        }
        .form-container h3 {
            color: #f8c471;
        }
        .btn-primary {
            background-color: rgba(196, 2, 83, 0.8);
            border-color:rgb(0, 0, 0);
        }
        .btn-primary:hover {
            background-color: rgb(153, 0, 0);
        }
        .btn-secondary {
            background-color: #34495e;
            border-color:rgb(0, 0, 0);
        }
        .btn-secondary:hover {
            background-color:rgb(0, 0, 0);
        }
        .centered-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
    </style>
</head>
<body>

<div class="container centered-container">
    <div class="row justify-content-center">
        <div class="col-md-10 form-container"> <!-- Increased container width -->
            <h1 class="text-center">NK MOVIES & THEATRE</h1>
            <h3 class="text-center">Approve Booking</h3>

            <?php if (!empty($message)): ?>
                <div class="alert alert-info"> <?php echo $message; ?> </div>
            <?php endif; ?>

            <form action="approve_booking.php" method="POST">
                <div class="form-group">
                    <label for="booking_id">Select Booking to Approve:</label>
                    <select class="form-control" id="booking_id" name="booking_id" required>
                        <option value="">Select a booking</option>
                        <?php
                        if ($result->num_rows > 0): 
                            while ($booking = $result->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $booking['id']; ?>">
                            Booking ID: <?php echo $booking['id']; ?> -  Movie: <?php echo htmlspecialchars($booking['title']); ?> - Price: $<?php echo htmlspecialchars($booking['total_price']); ?>
                            </option>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <option value="">No pending bookings available</option>
                        <?php endif; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Approve Booking</button>
                <a href="admin_panel.php?section=manage-bookings" class="btn btn-secondary btn-block">Cancel</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>


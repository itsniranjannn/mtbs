<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer

include('db.php'); // Database connection
session_start(); // Start session for user tracking

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if booking_id is provided
if (isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID

    // Update the booking status to 'cancelled' for the user
    $query_cancel_booking = "UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ?";
    $stmt_cancel_booking = $conn->prepare($query_cancel_booking);
    $stmt_cancel_booking->bind_param("ii", $booking_id, $user_id);
    $stmt_cancel_booking->execute();

    if ($stmt_cancel_booking->affected_rows > 0) {
        // Fetch user email
        $query = "SELECT email FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_email = $user['email'];

            // Send cancellation email
            $subject = "You Have Cancelled Your Booking";
            $body = "
            <div style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
                <table style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden;'>
                    <thead>
                        <tr>
                            <th style='background-color:rgb(50, 0, 54); color: #ffffff; padding: 20px; text-align: center;'>
                                <h1 style='margin: 0;'>🎥 NK Movies & Theatre</h1>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style='padding: 20px;'>
                                <p style='font-size: 18px;'>Dear Valued Customer,</p>
                                <p style='font-size: 16px;'>We regret to inform you that you have <strong>cancelled</strong> your booking with ID <strong>{$booking_id}</strong></p>
                                <p style='font-size: 16px;'>If you have any questions, please contact our support team at <a href='mailto:nkmoviestheatre@gmail.com'>nkmoviestheatre@gmail.com</a>.</p>
                                <p style='font-size: 16px;'>Thank you for choosing NK Movies & Theatre.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding: 20px; text-align: center; background-color: #f4f4f4;'>
                                <p style='font-size: 14px; color:rgb(0, 0, 0);'>This email was sent from the NK Movies & Theatre system. Please do not reply to this email.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            ";

            // Send email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'nkmoviestheater@gmail.com';
                $mail->Password = 'clao qnfn wyfl tlmp';  // Consider using environment variables for security
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('nkmoviestheater@gmail.com', 'NK Theatre');
                $mail->addAddress($user_email);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $body;

                $mail->send();
            } catch (Exception $e) {
                error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
                // Optionally display error message if email fails
                echo "There was an issue sending the cancellation email. Please try again later.";
            }

        } else {
            echo "User not found.";
        }

        // Redirect back to the user dashboard after successful cancellation
        header("Location: user_dashboard.php");
        exit;

    } else {
        // If no rows were updated, it means the booking wasn't found or isn't cancellable
        echo "Error: Unable to cancel the booking.";
    }
} else {
    echo "Error: Booking ID is missing.";
}
?>
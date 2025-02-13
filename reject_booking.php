<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'db.php';
session_start();

if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit;
}

$message = "";

function sendEmail($recipient, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'nkmoviestheater@gmail.com';
        $mail->Password = 'clao qnfn wyfl tlmp';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('nkmoviestheater@gmail.com', 'NK Theatre');
        $mail->addAddress($recipient);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        throw new Exception("Email sending failed: {$e->getMessage()}");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = filter_var($_POST['booking_id'], FILTER_SANITIZE_NUMBER_INT);

    if ($booking_id) {
        $stmt = $conn->prepare("SELECT b.*, u.email FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $booking = $result->fetch_assoc();

            if ($booking['status'] === 'rejected') {
                $message = "This booking has already been rejected.";
            } elseif ($booking['status'] === 'approved') {
                $message = "This booking has already been approved.";
            } else {
                $update_stmt = $conn->prepare("UPDATE bookings SET status = 'rejected' WHERE id = ?");
                $update_stmt->bind_param("i", $booking_id);

                if ($update_stmt->execute()) {
                    $subject = "Your Booking Has Been Rejected";
                    $body = "
                    <div style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
                        <table style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden;'>
                            <thead>
                                <tr>
                                    <th style='background-color: #FF0000; color: #ffffff; padding: 20px; text-align: center;'>
                                        <h1 style='margin: 0;'>🎥 NK Movies & Theatre</h1>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style='padding: 20px;'>
                                        <p style='font-size: 18px;'>Dear Beloved Customer,</p>
                                        <p style='font-size: 16px;'>We regret to inform you that your booking with ID <strong>{$booking_id}</strong> has been <strong>rejected</strong>.</p>
                                        <p style='font-size: 16px;'>If you have any questions, please contact our support team at <a href='mailto:nkmoviestheatre@gmail.com'>nkmoviestheatre@gmail.com</a>.</p>
                                        <p style='font-size: 16px;'>Thank you for choosing NK Movies & Theatre.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='padding: 20px; text-align: center; background-color: #f4f4f4;'>
                                        <p style='font-size: 14px; color: #777777;'>This email was sent from the NK Movies & Theatre system. Please do not reply to this email.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    ";

                    try {
                        sendEmail($booking['email'], $subject, $body);
                        $message = "Booking rejected and email sent.";
                    } catch (Exception $e) {
                        $message = "Booking rejected, but email sending failed. Error: {$e->getMessage()}";
                    }
                } else {
                    $message = "Error rejecting the booking. Please try again.";
                }
            }
        } else {
            $message = "Booking not found or invalid.";
        }
    } else {
        $message = "Invalid booking ID.";
    }

    header("Location: admin_panel.php?section=manage-bookings&message=" . urlencode($message));
    exit;
}

$query = "SELECT b.id, b.total_price, b.category, m.title AS movie_title FROM bookings b JOIN movies m ON b.movie_id = m.id WHERE b.status = 'pending' AND b.user_id IS NOT NULL";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching bookings: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reject Booking</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        background-image: url('r.png?text=Bookings+Background');
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
        padding: 40px; /* Increased padding for a bigger container */
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        max-width: 900px; /* Increased max width */
    }
    .form-container h3 {
        color: #f8c471;
    }
    .btn-primary {
        background-color: rgb(97, 18, 243);
        border-color:rgb(255, 255, 255);
    }
    .btn-primary:hover {
        background-color: rgb(218, 33, 0);
    }
    .btn-secondary {
        background-color: #34495e;
        border-color:rgb(255, 255, 255);
    }
    .btn-secondary:hover {
        background-color:rgb(10, 10, 10);
    }
    .centered-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
</style>

<div class="container centered-container">
    <div class="row justify-content-center">
        <div class="col-md-10 form-container"> <!-- Changed to col-md-10 for bigger width -->
            <h1 class="text-center">NK MOVIES & THEATRE</h1>
            <h3 class="text-center">Reject Booking</h3>
            <?php if (!empty($message)): ?>
                <div class="alert alert-info"> <?php echo $message; ?> </div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="booking_id">Select Booking to Reject:</label>
                    <select class="form-control" id="booking_id" name="booking_id" required>
                        <option value="">Select a booking</option>
                        <?php
                        if ($result->num_rows > 0): 
                            while ($booking = $result->fetch_assoc()): 
                        ?>
                                <option value="<?php echo $booking['id']; ?>">
                                    Booking ID: <?php echo $booking['id']; ?> - Movie: <?php echo $booking['movie_title']; ?> - Price: $<?php echo $booking['total_price']; ?>
                                </option>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <option value="">No pending bookings available</option>
                        <?php endif; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Reject Booking</button>
                <a href="admin_panel.php?section=manage-bookings" class="btn btn-secondary btn-block">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>

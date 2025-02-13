<?php
include('db.php'); // Include the database connection
session_start(); // Start session for tracking

$message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the username contains only alphabets
    if (!preg_match("/^[a-zA-Z]+$/", $username)) {
        $message = "Username must contain only alphabets.";
    } 
    // Check if the password is at least 8 characters long
    elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
    }
    // Hash the password if all validations pass
    else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the email is in a valid format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format. Please enter a valid email address.";
        } else {
            // Check if the username or email already exists
            $check_query = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $check_query->bind_param("ss", $username, $email);
            $check_query->execute();
            $result = $check_query->get_result();

            if ($result->num_rows > 0) {
                $message = "User already exists. Please <a href='login.php'>login here</a>.";
            } else {
                // Insert the new user into the database
                $insert_query = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $insert_query->bind_param("sss", $username, $email, $hashed_password);

                if ($insert_query->execute()) {
                    $message = "Registration successful! You can now <a href='login.php'>login here</a>.";
                } else {
                    $message = "Error: Unable to register. Please try again later.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Movie Ticket Booking</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <img src="sbg.jpg" alt="Cinema" class="background-image">
        <div class="login-box">
            <h1>NK MOVIES & THEATER</h1>
            <h2>Sign Up</h2>
            <?php if (!empty($message)): ?>
                <p style="color: green;"><?php echo $message; ?></p>
            <?php endif; ?>
            <form action="signup.php" method="POST">
                <div class="textbox">
                    <input type="text" placeholder="Username" name="username" required>
                </div>
                <div class="textbox">
                    <input type="email" placeholder="Email" name="email" required>
                </div>
                <div class="textbox">
                    <input type="password" placeholder="Password" name="password" id="password" required>
                </div>
                <div class="show-password">
                    <input type="checkbox" id="show-password-checkbox" onclick="togglePassword()">
                    <label for="show-password-checkbox">Show Password</label>
                </div>
                <button type="submit" class="btn signup-btn">Sign Up</button>
            </form>
            <h3>Already a user?</h3>
            <button class="btn login-btn" onclick="window.location.href='login.php'">Login</button>
        </div>
    </div>
    <script>
        // JavaScript to toggle password visibility
        function togglePassword() {
            const passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>

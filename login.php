<?php
include('db.php'); // Include the database connection
session_start(); // Start session for login tracking

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check for an admin
    $query_admin = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $query_admin->bind_param("s", $username);
    $query_admin->execute();
    $result_admin = $query_admin->get_result();

    if ($result_admin->num_rows === 1) {
        $admin = $result_admin->fetch_assoc();
        // Verify the hashed password
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: admin_panel.php"); // Redirect to admin dashboard
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }

    // Check for a regular user
    $query_user = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $query_user->bind_param("s", $username);
    $query_user->execute();
    $result_user = $query_user->get_result();

    if ($result_user->num_rows === 1) {
        $user = $result_user->fetch_assoc();
        // Verify the hashed password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: home.php"); // Redirect to user home page
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }

    // If neither admin nor user credentials match
    if (!isset($error)) {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NK MOVIES & THEATER </title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <img src="bg.jpg" alt="Cinema" class="background-image">
        <div class="login-box">
            <h1>NK MOVIES & THEATER</h1>
            <h2>Login</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="textbox">
                    <input type="text" placeholder="Username" name="username" required autofocus>
                </div>
                <div class="textbox">
                    <input type="password" placeholder="Password" name="password" id="password" required>
                </div>
                <div class="show-password">
                    <input type="checkbox" id="show-password-checkbox" onclick="togglePassword()">
                    <label for="show-password-checkbox">Show Password</label>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
            <h3>Don't have an account?</h3>
            <button class="btn signup-btn" onclick="window.location.href='signup.php'">Sign Up</button>
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

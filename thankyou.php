<?php
include('header.php');

$message = isset($_GET['message']) 
    ? htmlspecialchars($_GET['message']) 
    : "Thank you for booking! Your request is being processed. Please wait....";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - NK Theater & Movies</title>
    <link rel="stylesheet" href="n.css">
    <link rel="icon" type="image/x-icon" href="n.png">
    <style>
       
body {
    
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, purple, red);
    color: #ffffff;
    text-align: center;
}

/* Heading Styling */
h1, h2 {
    margin: 20px 0;
    font-weight: 600;
}

/* Container Styling */
.container {
    margin-top: 10%;
    padding: 20px;
    text-align: center;
}

/* Message Box */
.message {
    font-size: 1.5rem;
    color: #ffffff;
    background-color: rgba(0, 0, 0, 0.7);
    padding: 30px;
    border-radius: 12px;
    display: inline-block;
    max-width: 80%;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .container {
        margin-top: 15%;
        padding: 10px;
    }

    .message {
        font-size: 1.2rem;
        padding: 20px;
    }

}

    </style>
    </head>
<body>
    <div class="container">
        <h2>We're here for your entertainment...</h2>
        <h2>We recieved your booking!</h2>
        <div class="message">
            <h1><?php echo $message; ?></h1>
            <h2>Please go through userdashboard or check your registerd email! If its approved you will recieve a copy ticket. just download it<hr>  <br>Make sure to collect real tickets by doing payment in counter in theater to secure your seats.</p>
            <h2>Enjoy your movie! <hr>HAVE A GOOD DAY*_*</h2><hr>
            <a href="home.php">Go to Home</a>
        </div>
       
    </div>

    <footer>
        <div class="foot">
            <a href="home.php">Home</a>
            <a href="movie.php">Book Here</a>
            <a href="upcoming.php">Shows</a>
        </div>
        <hr>
        <p>&copy; 2024 NK Theater And Movies | All Rights Reserved</p>
        <img src="logo.png" alt="logo" width="15%">
    </footer>
</body>
</html>
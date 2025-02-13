<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'try');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Restrict access for admins
if (isset($_SESSION['admin_username'])) {
    header("Location: admin_panel.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $movie = $result->fetch_assoc();
    } else {
        echo "<p>Movie not found.</p>";
        exit;
    }
} else {
    echo "<p>No movie selected.</p>";
    exit;
}

// Fetch occupied, pending, and approved seats
$occupiedSeats = [];
$pendingSeats = [];
$seatQuery = $conn->prepare("SELECT seats, status FROM bookings WHERE movie_id = ?");
$seatQuery->bind_param("i", $id);
$seatQuery->execute();
$seatResult = $seatQuery->get_result();

while ($row = $seatResult->fetch_assoc()) {
    $seats = explode(',', $row['seats']);
    if ($row['status'] === 'approved') {
        $occupiedSeats = array_merge($occupiedSeats, $seats);
    } elseif ($row['status'] === 'pending') {
        $pendingSeats = array_merge($pendingSeats, $seats);
    }
}

// Booking submission handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = $id;
    $category = $_POST['category'];
    $seats = is_array($_POST['seats']) ? implode(',', $_POST['seats']) : $_POST['seats'];
    $total_price = $_POST['total_price'];
    $email = $_POST['email'];

    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        echo "<p style='text-align: center; color: red;'>Admins are not allowed to book tickets.</p>";
        exit;
    }

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $registeredEmail = $user['email'];

            if ($email !== $registeredEmail) {
                echo "<p style='text-align: center; color: red;'>The email entered does not match your registered email.</p>";
                exit;
            }
        } else {
            echo "<p style='text-align: center; color: red;'>User not found.</p>";
            exit;
        }
    }

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id']; // Get user_id from session
        $stmt = $conn->prepare("INSERT INTO bookings (movie_id, category, seats, total_price, email, status, user_id) VALUES (?, ?, ?, ?, ?, 'pending', ?)");
        $stmt->bind_param("issisi", $movie_id, $category, $seats, $total_price, $email, $user_id);
    } else {
        echo "<p style='text-align: center; color: red;'>You need to log in to book tickets.</p>";
        exit;
    }
    if ($stmt->execute()) {
        header("Location: thankyou.php?message=Booking has been sent for approval! Thank you for choosing us.");
        exit;
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="n.png">
    <title>Booking - <?php echo htmlspecialchars($movie['title']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: gainsboro;
            color: #333;
        }
        .foot a:hover {
    color: #ffffff;
}
        .seat {
            width: 30px;
            height: 30px;
            margin: 5px;
            background-color: #ddd;
            border: 1px solid #999;
            border-radius: 5px;
            cursor: pointer;
        }

        .seat.selected {
            background-color: #6c5ce7;
        }

        .seat.occupied {
            background-color: #ff7675;
            cursor: not-allowed;
        }

        .seat.pending {
            background-color: #f39c12;
            cursor: not-allowed;
        }

        .screen {
            background-color: peru;
            color: white;
            text-align: center;
            padding: 5px;
            margin: 10px 0;
        }

        #seats {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin: 0 auto;
            max-width: 300px;
        }

        #legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 15px 0;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border: 1px solid #000;
        }

        .available {
            background-color: antiquewhite;
        }

        .reserved {
            background-color: #ff7675;
        }

        .pending {
            background-color: #f39c12;
        }

        .selected {
            background-color: #6c5ce7;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center"><?php echo htmlspecialchars($movie['title']); ?></h1>
        <div class="text-center">
            <img src="uploads/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" style="height: 400px; object-fit: cover;">
        </div>
        <p class="mt-3 text-center text-secondary"><?php echo htmlspecialchars($movie['description']); ?></p>
        <p class="text-center"><strong>Showtimes:</strong> <?php echo htmlspecialchars($movie['showtimes']); ?></p>

        <form method="POST" id="bookingForm">
            <div class="categories text-center">
                <h3>Select Category:</h3>
                <div style="display: inline-block; margin: 10px;">
                    <img src="g.jpg" alt="Gold" onclick="selectCategory('Gold')" title="Gold (Rs. 500)"><hr>
                    <span>Gold</span>
                </div>
                <div style="display: inline-block; margin: 10px;">
                    <img src="d.jpg" alt="Diamond" onclick="selectCategory('Diamond')" title="Diamond (Rs. 800)"><hr>
                    <span>Diamond</span>
                </div>
            </div>
            <p id="categoryDisplay"></p>
            <div id="seatSection" style="display: none;">
                <h3 class="text-center">Choose Your Seats:</h3>
                <div class="screen">Screen Here</div>
                <div id="seats"></div>
                <p class="mt-3 text-center"><strong>Selected Seats:</strong> <span id="selectedSeats">None</span></p>
                <p class="text-center"><strong>Total Price:</strong> Rs. <span id="totalPrice">0</span></p>

                <input type="hidden" name="category" id="category">
                <input type="hidden" name="seats" id="hiddenSeats">
                <input type="hidden" name="total_price" id="hiddenTotalPrice">

                <div class="form-group mt-3">
                    <label for="email">Enter Your Email for Confirmation:</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="example@domain.com" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Confirm Booking</button>
            </div>
        </form>

        <div id="legend">
            <div class="legend-item"><div class="legend-color available"></div> Available Seats</div>
            <div class="legend-item"><div class="legend-color reserved"></div> Reserved Seats</div>
            <div class="legend-item"><div class="legend-color pending"></div> Pending Approval</div>
            <div class="legend-item"><div class="legend-color selected"></div> Selected Seats</div>
        </div>
    </div>

    <script>
        const occupiedSeats = <?php echo json_encode($occupiedSeats); ?>;
        const pendingSeats = <?php echo json_encode($pendingSeats); ?>;
        const seatsContainer = document.getElementById("seats");

        function renderSeats() {
            for (let i = 1; i <= 25; i++) {
                const seat = document.createElement("div");
                seat.classList.add("seat");
                seat.dataset.seatId = i;

                if (occupiedSeats.includes(i.toString())) {
                    seat.classList.add("occupied");
                } else if (pendingSeats.includes(i.toString())) {
                    seat.classList.add("pending");
                }

                seatsContainer.appendChild(seat);
            }
        }

        renderSeats();

        const seats = document.querySelectorAll(".seat:not(.occupied):not(.pending)");
        const selectedSeatsElement = document.getElementById("selectedSeats");
        const totalPriceElement = document.getElementById("totalPrice");
        const hiddenSeats = document.getElementById("hiddenSeats");
        const hiddenTotalPrice = document.getElementById("hiddenTotalPrice");

        let selectedSeats = [];
        let ticketPrice = 0;

        function selectCategory(category) {
            const seatSection = document.getElementById("seatSection");
            const categoryDisplay = document.getElementById("categoryDisplay");
            const categoryInput = document.getElementById("category");

            categoryDisplay.innerHTML = `<strong>Selected Category:</strong> ${category}`;
            categoryInput.value = category;
            seatSection.style.display = "block";

            ticketPrice = category === "Gold" ? 500 : 800;

            totalPriceElement.textContent = selectedSeats.length * ticketPrice;
            hiddenTotalPrice.value = selectedSeats.length * ticketPrice;
        }

        seats.forEach(seat => {
            seat.addEventListener("click", () => {
                const seatId = seat.dataset.seatId;

                if (selectedSeats.includes(seatId)) {
                    selectedSeats = selectedSeats.filter(id => id !== seatId);
                    seat.classList.remove("selected");
                } else {
                    selectedSeats.push(seatId);
                    seat.classList.add("selected");
                }

                selectedSeatsElement.textContent = selectedSeats.length > 0 ? selectedSeats.join(", ") : "None";
                totalPriceElement.textContent = selectedSeats.length * ticketPrice;
                hiddenSeats.value = selectedSeats.join(",");
                hiddenTotalPrice.value = selectedSeats.length * ticketPrice;
            });
        });
    </script>
</body>
</html>

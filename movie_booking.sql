-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2025 at 11:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `movie_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `password`) VALUES
(1, 'niranjan', 'katwalniranjan7@gmail.com', '$2y$10$2l0tNCeEPpn59dpWvX9yGealvPwORC0aDbrHpgmd7SCXW/vPSTMaW');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `seats` text NOT NULL,
  `total_price` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `booking_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `admin_id` int(11) DEFAULT NULL,
  `status_changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `movie_id`, `category`, `seats`, `total_price`, `email`, `booking_time`, `status`, `admin_id`, `status_changed_at`, `user_id`) VALUES
(67, 18, 'Gold', '1', 500, 'katwalniranjan40@gmail.com', '2024-12-03 08:24:10', 'approved', NULL, '2024-12-03 08:24:10', 12),
(68, 19, 'Gold', '2', 500, 'katwalniranjan40@gmail.com', '2024-12-03 08:24:21', 'approved', NULL, '2024-12-03 08:24:21', 12),
(69, 22, 'Diamond', '3', 800, 'katwalniranjan40@gmail.com', '2024-12-03 08:38:24', 'rejected', NULL, '2024-12-03 08:38:24', 12),
(70, 18, 'Gold', '9', 500, 'katwalniranjan40@gmail.com', '2024-12-03 08:44:48', 'approved', NULL, '2024-12-03 08:44:48', 12),
(71, 24, 'Diamond', '2', 800, 'katwalniranjan40@gmail.com', '2024-12-03 08:44:56', 'rejected', NULL, '2024-12-03 08:44:56', 12),
(72, 19, 'Gold', '8', 500, 'katwalrambdr7@gmail.com', '2024-12-16 10:08:11', 'approved', NULL, '2024-12-16 10:08:11', 9),
(73, 21, 'Diamond', '2,3,4,5,6,7,11,17,14,19,24,23,22,16,1,8,15,9,10,12,13,20,25,18,21', 20000, 'katwalniranjan40@gmail.com', '2024-12-16 11:18:12', 'rejected', NULL, '2024-12-16 11:18:12', 12),
(75, 18, 'Gold', '2,3,4', 1500, 'katwalniranjan40@gmail.com', '2024-12-20 07:51:32', 'rejected', NULL, '2024-12-20 07:51:32', 12),
(77, 18, 'Gold', '17', 500, 'xetriniruta121@gmail.com', '2024-12-22 04:50:13', 'approved', NULL, '2024-12-22 04:50:13', NULL),
(78, 21, 'Gold', '1', 500, 'xetriniruta121@gmail.com', '2024-12-22 04:50:31', 'rejected', NULL, '2024-12-22 04:50:31', NULL),
(79, 18, 'Gold', '15', 500, 'katwalniranjan40@gmail.com', '2024-12-22 04:59:49', 'cancelled', NULL, '2024-12-22 04:59:49', 12),
(80, 18, 'Gold', '8', 500, 'katwalniranjan40@gmail.com', '2024-12-22 08:27:54', 'cancelled', NULL, '2024-12-22 08:27:54', 12),
(81, 21, 'Gold', '1', 500, 'katwalniranjan40@gmail.com', '2024-12-22 08:46:50', 'cancelled', NULL, '2024-12-22 08:46:50', 12);

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `language` enum('Nepali','Hindi','English') NOT NULL,
  `showtimes` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `release_date` date NOT NULL,
  `poster` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `description`, `language`, `showtimes`, `price`, `release_date`, `poster`) VALUES
(18, 'Smile 2', 'About to embark on a new world tour, global pop sensation Skye Riley begins to experience increasingly terrifying and inexplicable events. ', 'English', '10:00', NULL, '2024-12-07', 'e2.jpg'),
(19, 'Vettaiyan', 'Ruthless criminal activity is met with force from a maverick police officer with a disregard for the rules as he confronts an outbreak of lawlessness.', 'Hindi', '10:00', NULL, '2024-12-07', 'h6.jpg'),
(21, 'Venom 3: the last dance', 'Eddie and Venom, on the run, face pursuit from both worlds. As circumstances tighten, they\'re compelled to make a heart-wrenching choice that could mark the end ...', 'English', '12:00', NULL, '2024-12-20', 'e5.jpg'),
(22, 'EKLO', 'In 2090, the last human survivor lives in Nepal\'s mountains. When two astronauts return to Earth, they meet this sole survivor and unravel the mystery of ..', 'Nepali', '10:45', NULL, '2024-12-11', 'n4.jpg'),
(24, 'Gunyo Cholo', ' Gulabi, a trans-woman is groomed by his patriarchal father to join the military. His father disowns him, leading him to live a life of .', 'Nepali', '16:30', NULL, '2024-12-12', 'n6.jpg'),
(25, 'Kraven the hunter', 'Kraven\'s complex relationship with his ruthless father starts him down a path of vengeance, motivating him to become not only the greatest hunter in the world, but also one of its most feared.', 'English', '11:00', NULL, '2024-12-10', 'e1.jpg'),
(27, 'Purna Bahadur Ko sarangi', '\"Purna Bahadur Ko Sarangi\" is a beautifully crafted film that delves deep into the struggles and triumphs of an impoverished father navigating life\'s challenges ..', 'Nepali', '17:30', NULL, '2024-12-25', 'n8.jpg'),
(28, 'Baby John', 'Varun plays a police officer and single father, portraying a character who is not afraid to confront adversaries head-on. It also ...', 'Hindi', '09:00', NULL, '2024-12-24', 'download.jfif'),
(29, 'Puspa 2', 'The clash is on as Pushpa and Bhanwar Singh continue their rivalry in this epic conclusion to the two-parted action drama.', 'Hindi', '11:30', NULL, '2024-12-05', 'h2.jpg'),
(30, 'Jurassic World rebirth', 'A woman and a family get stranded on an island that\'s home to ferocious dinosaurs.', 'English', '13:30', NULL, '2025-01-10', 'e6.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `ticket_code` varchar(50) NOT NULL,
  `seat_number` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `booking_id`, `ticket_code`, `seat_number`) VALUES
(39, 72, 'TICKET-67600B3BB566A', '8'),
(40, 67, 'TICKET-67600C25D73BD', '1'),
(41, 68, 'TICKET-67600C25D8EA8', '2'),
(42, 70, 'TICKET-67600C25DC6EC', '9');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `is_verified`, `verification_code`, `created_at`) VALUES
(9, 'ram', 'katwalrambdr7@gmail.com', '$2y$10$lhOeOiyYHYqBpjMC3Yf23OhAB8x7L.zRhJYcoDmrXq0SaAhXA31o2', 0, NULL, '2024-11-29 07:53:53'),
(12, 'ronan', 'katwalniranjan40@gmail.com', '$2y$10$u/8UgnJoGm3iD0Yh9cjK4O3q9Y3ZSfBYSx.OjT00h1QEKjKYgQV.W', 0, NULL, '2024-11-29 12:36:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_code` (`ticket_code`),
  ADD UNIQUE KEY `ticket_code_2` (`ticket_code`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

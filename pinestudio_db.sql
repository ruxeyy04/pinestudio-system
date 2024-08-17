-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 15, 2024 at 10:01 AM
-- Server version: 10.11.8-MariaDB-cll-lve
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pinestudio_db`
--
CREATE DATABASE IF NOT EXISTS `pinestudio_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pinestudio_db`;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `serviceid` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `userid`, `serviceid`, `type`, `address`, `startdate`, `enddate`, `status`) VALUES
(9, 14, 6, 'Party', 'Pantaon Ozamiz City', '2024-07-05 08:00:00', '2024-07-12 19:03:00', 'Rejected'),
(14, 16, 3, 'Wedding', 'Loretos', '2024-07-13 15:00:00', '2024-07-13 23:00:00', 'Accepted'),
(24, 41, 3, 'Party', 'Pantaon', '2024-08-23 16:07:00', '2024-08-29 21:07:00', 'Rejected'),
(25, 41, 19, 'Wedding ', 'Pantaon', '2024-07-14 16:33:00', '2024-07-16 21:33:00', 'Accepted'),
(26, 41, 2, 'Birthday ', 'Pantaon', '2024-06-16 04:35:00', '2024-06-16 09:35:00', 'Cancelled'),
(27, 41, 3, 'reqwfw', 'frweag', '2024-06-20 04:57:00', '2024-06-20 21:57:00', 'Rejected'),
(28, 41, 6, 'gretag', 'rgegre', '2024-06-21 04:58:00', '2024-07-04 16:58:00', 'Rejected'),
(29, 41, 3, 'Whxbxj', ' Znsksm', '2024-06-14 05:11:00', '2024-06-15 17:11:00', 'Accepted'),
(31, 14, 5, 'Ywywy', 'Yeywyw', '2024-06-17 09:34:00', '2024-06-21 14:34:00', 'Cancelled'),
(32, 14, 2, 'Samp', 'Samp', '2024-06-19 08:33:00', '2024-06-20 20:33:00', 'Cancelled'),
(33, 14, 3, 'Samp2', 'Samp 2', '2024-06-22 08:34:00', '2024-06-26 13:38:00', 'Accepted'),
(34, 14, 3, 'Sample 3', 'Sample 3', '2024-06-17 08:57:00', '2024-06-18 13:57:00', 'Pending'),
(35, 14, 3, 'sample 4', 'sample 4', '2024-06-27 01:57:00', '2024-06-28 06:57:00', 'Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `serviceid` int(11) NOT NULL,
  `image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `name`, `serviceid`, `image`) VALUES
(2, 'Vernisa @ 18', 3, '222.jpg'),
(4, 'Honoring Altar Servers Month with a Meaningful Team Building\r\n                  Event at St. Therese Parish Banadero Ozamiz City', 5, '444.jpg'),
(9, 'Ramil @ 21', 5, '../../img/gallery/9.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` double NOT NULL,
  `image` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `type`, `description`, `price`, `image`) VALUES
(2, 'Same Day Edits', 'Photo', 'This service will do same day edit on photo only. yeah', 17999, 'samedayeditphoto.jpg'),
(3, 'Same Day Edit', 'Video', 'This service will do same day edit on video only.', 18000, 'samedayeditvideo.jpg'),
(5, 'Photo', 'Highlights', 'This service will do highlight\'s on photo only.', 19999, 'p.jpg'),
(6, 'Video', 'Highlights', 'This service will do highlight\'s on video only.', 17999, 'vd.jpg'),
(19, 'Same Day Edit Photo and Video', 'Photo & Video', 'photo and video SDE', 25000, '19.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `age` int(2) DEFAULT NULL,
  `password` text NOT NULL,
  `image` text DEFAULT NULL,
  `usertype` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `gender`, `address`, `age`, `password`, `image`, `usertype`) VALUES
(1, 'Jeffersonst', 'Canamoost', 'jeffadmin@gmail.com', 'Male', 'admin', 21, 'admin', '1.jpg', 'Admin'),
(2, 'inchargee', 'met', 'employee@gmail.com', 'Female', 'shudaada', 21, 'employee', '2.jpg', 'Incharge'),
(14, 'Jeffersonsst', 'Canamosst', 'client@gmail.com', 'Prefer not to say', 'Pantaon Ozamiz City', 22, 'client', '14.jpg', 'Client'),
(16, 'Jakes', 'Cañamos', 'jake@gmail.com', 'Male', 'Pantaon', 12, 'jake', '16.jpg', 'Client'),
(41, 'Joel', 'Canamo', 'joel@gmail.com', 'Male', 'pantaon', NULL, 'joel', NULL, 'Client'),
(42, 'Jajaj', 'Jajaj', 'Hajajq@gmail.com', 'Male', 'Jzjdksk', NULL, '123', NULL, 'Client'),
(43, 'wew', 'wew', 'wew', 'Others', 'wew', NULL, 'wew', NULL, 'Client');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `serviceid` (`serviceid`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `serviceid` (`serviceid`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`serviceid`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gallery`
--
ALTER TABLE `gallery`
  ADD CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`serviceid`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

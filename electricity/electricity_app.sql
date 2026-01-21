-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2026 at 12:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `electricity_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` int(11) NOT NULL,
  `registration_number` varchar(50) NOT NULL,
  `units_used` int(11) NOT NULL,
  `bill_amount` decimal(10,2) NOT NULL,
  `bill_date` date NOT NULL,
  `month` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`id`, `registration_number`, `units_used`, `bill_amount`, `bill_date`, `month`) VALUES
(1, '1001', 150, 600.00, '2026-01-19', 'January 2026'),
(2, '1001', 123, 438.00, '2026-01-19', 'December 2025');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `registration_number` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `dob` date NOT NULL,
  `connection_date` date NOT NULL,
  `address` text NOT NULL,
  `role` enum('user','administrator','reader') DEFAULT 'user',
  `connection_type` enum('home','corporate','industrial','staff') DEFAULT 'home'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`registration_number`, `name`, `password`, `dob`, `connection_date`, `address`, `role`, `connection_type`) VALUES
('admin', 'System Admin', 'admin123', '1990-01-01', '2020-01-01', 'HQ, Admin Block, City, State - 100001', 'administrator', 'staff'),
('reader', 'Meter Reader', 'reader123', '1995-05-15', '2021-06-01', 'Substation 1, Ind Area, City, State - 100002', 'reader', 'staff'),
('1001', 'Alice Home', NULL, '1985-08-20', '2023-01-10', '123, Rose St, Garden Town, State - 100003', 'user', 'home'),
('1002', 'Bob Corp', NULL, '1980-03-12', '2023-02-15', '456, Biz Hub, Tech Park, State - 100004', 'user', 'corporate'),
('1003', 'Charlie Factory', NULL, '1975-11-30', '2023-03-20', '789, Ind Zone, Factory Rd, State - 100005', 'user', 'industrial');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registration_number` (`registration_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`registration_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_ibfk_1` FOREIGN KEY (`registration_number`) REFERENCES `users` (`registration_number`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

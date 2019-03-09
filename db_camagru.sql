-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3308
-- Generation Time: Mar 09, 2019 at 12:10 PM
-- Server version: 5.7.24
-- PHP Version: 7.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_camagru`
--

-- --------------------------------------------------------

--
-- Table structure for table `picture`
--

CREATE TABLE `picture` (
  `id_img` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `picture`
--

INSERT INTO `picture` (`id_img`, `id_user`, `img`, `date`) VALUES
(21, 5, 'public/upload/20190308162149.png', '2019-03-08 16:21:49'),
(22, 5, 'public/upload/20190308162150.png', '2019-03-08 16:21:50'),
(23, 5, 'public/upload/20190308162152.png', '2019-03-08 16:21:52'),
(24, 5, 'public/upload/20190308162154.png', '2019-03-08 16:21:54'),
(35, 5, 'public/upload/20190308170210.png', '2019-03-08 17:02:10'),
(36, 5, 'public/upload/20190308170211.png', '2019-03-08 17:02:11'),
(37, 5, 'public/upload/20190308170213.png', '2019-03-08 17:02:13'),
(38, 5, 'public/upload/20190308170215.png', '2019-03-08 17:02:15'),
(39, 5, 'public/upload/20190309175605.png', '2019-03-09 17:56:05'),
(40, 5, 'public/upload/20190309184343.png', '2019-03-09 18:43:43'),
(41, 5, 'public/upload/20190309184402.png', '2019-03-09 18:44:02'),
(42, 5, 'public/upload/20190309184407.png', '2019-03-09 18:44:07'),
(43, 5, 'public/upload/20190309184433.png', '2019-03-09 18:44:33'),
(44, 5, 'public/upload/20190309184436.png', '2019-03-09 18:44:36'),
(45, 5, 'public/upload/20190309184440.png', '2019-03-09 18:44:40'),
(46, 5, 'public/upload/20190309184444.png', '2019-03-09 18:44:44'),
(47, 5, 'public/upload/20190309194612.png', '2019-03-09 19:46:13'),
(48, 5, 'public/upload/20190309194614.png', '2019-03-09 19:46:14'),
(49, 5, 'public/upload/20190309194615.png', '2019-03-09 19:46:15'),
(50, 5, 'public/upload/20190309194616.png', '2019-03-09 19:46:16'),
(51, 5, 'public/upload/20190309194617.png', '2019-03-09 19:46:17'),
(52, 5, 'public/upload/20190309194618.png', '2019-03-09 19:46:18'),
(53, 5, 'public/upload/20190309194619.png', '2019-03-09 19:46:19'),
(54, 5, 'public/upload/20190309194620.png', '2019-03-09 19:46:20'),
(55, 5, 'public/upload/20190309194621.png', '2019-03-09 19:46:21'),
(56, 5, 'public/upload/20190309194622.png', '2019-03-09 19:46:22'),
(57, 5, 'public/upload/20190309194623.png', '2019-03-09 19:46:23'),
(58, 5, 'public/upload/20190309194624.png', '2019-03-09 19:46:24'),
(59, 5, 'public/upload/20190309202959.png', '2019-03-09 20:29:59');

-- --------------------------------------------------------

--
-- Table structure for table `sticker`
--

CREATE TABLE `sticker` (
  `id_sticker` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `img_sticker` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sticker`
--

INSERT INTO `sticker` (`id_sticker`, `name`, `img_sticker`) VALUES
(1, 'poop', './public/stickers/poop.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(500) NOT NULL,
  `activation_code` varchar(500) NOT NULL,
  `user_status` varchar(50) NOT NULL DEFAULT 'not verified',
  `token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `activation_code`, `user_status`, `token`) VALUES
(5, 'gabriele', 'gkuraite@student.42.fr', '$2y$10$feP2ieW7hKT6RS912qK7s.qx9wjElFANK.ZbnwIcevHPn13NnISdO', '7f1de29e6da19d22b51c68001e7e0e54', 'verified', 'e9e77503467423e0b755b0355701701d'),
(10, 'gabydu13', 'gkuraite@student.42.fr', '$2y$10$feP2ieW7hKT6RS912qK7s.qx9wjElFANK.ZbnwIcevHPn13NnISdO', '182be0c5cdcd5072bb1864cdee4d3d6e', 'not verified', '60efbf5c3057a75ebc93863a964e6648'),
(11, 'lisa', 'gkuraite@student.42.fr', '$2y$10$fS6V2xCqFnvwjDzD2AZhk.brRXj1pVQlq9YZHhe95cBIkbd8ApjUS', 'c74d97b01eae257e44aa9d5bade97baf', 'verified', '30c49f4500d16d097ee4536fe366c82e');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `picture`
--
ALTER TABLE `picture`
  ADD PRIMARY KEY (`id_img`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `picture`
--
ALTER TABLE `picture`
  MODIFY `id_img` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

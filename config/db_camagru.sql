-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3308
-- Generation Time: Apr 03, 2019 at 10:24 AM
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
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id_comment` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_img` int(11) NOT NULL,
  `comment` text NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id_comment`, `id_user`, `id_img`, `comment`, `date`) VALUES
(39, 14, 274, 'WOW my husband is so beautiful', '2019-04-02 17:01:06'),
(40, 14, 278, 'always handsome darling', '2019-04-02 17:01:30'),
(41, 14, 277, 'Why so sad Noel ?', '2019-04-02 17:01:44'),
(42, 13, 278, 'thank you Honey &lt;3', '2019-04-02 17:02:39'),
(43, 13, 277, 'because you weren\'t next to me', '2019-04-02 17:02:58'),
(44, 13, 274, 'YASSSSSSSSSS QUEEEN', '2019-04-02 17:03:12'),
(45, 13, 285, 'My wife is the most beautiful cat in the world', '2019-04-02 17:03:33'),
(46, 13, 285, 'I love you baby', '2019-04-02 17:03:39'),
(47, 13, 281, 'hahhahahaha you are so lazy', '2019-04-02 17:03:57'),
(48, 13, 284, 'Can\'t believe that this cat is the mother of my 3 children', '2019-04-02 17:04:41'),
(49, 13, 284, 'You are stunning', '2019-04-02 17:04:46'),
(50, 13, 283, 'Don\'t be mad Suri !', '2019-04-02 17:05:05'),
(51, 13, 276, 'hehehe i look cute', '2019-04-02 17:05:21'),
(52, 13, 288, 'fetetert', '2019-04-03 17:25:55'),
(53, 13, 288, 'etetetewte', '2019-04-03 17:25:59'),
(54, 13, 288, 'erwtwtewt', '2019-04-03 17:26:02'),
(55, 13, 288, 'etewtewtewte', '2019-04-03 17:26:05'),
(56, 13, 288, 'ertretre', '2019-04-03 17:26:50'),
(57, 13, 288, 'rtret', '2019-04-03 17:26:53');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id_like` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_img` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id_like`, `id_user`, `id_img`, `date`) VALUES
(91, 14, 274, '2019-04-02 17:01:08'),
(92, 14, 278, '2019-04-02 17:01:23'),
(93, 14, 277, '2019-04-02 17:01:47'),
(94, 13, 278, '2019-04-02 17:02:40'),
(95, 13, 277, '2019-04-02 17:03:00'),
(96, 13, 274, '2019-04-02 17:03:13'),
(97, 13, 285, '2019-04-02 17:03:21'),
(98, 13, 281, '2019-04-02 17:03:58'),
(99, 13, 284, '2019-04-02 17:04:47'),
(100, 13, 283, '2019-04-02 17:04:55'),
(102, 15, 285, '2019-04-02 17:27:07'),
(104, 13, 282, '2019-04-02 18:47:47');

-- --------------------------------------------------------

--
-- Table structure for table `picture`
--

CREATE TABLE `picture` (
  `id_img` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `likes` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `picture`
--

INSERT INTO `picture` (`id_img`, `id_user`, `img`, `date`, `likes`) VALUES
(274, 13, 'public/upload/20190402165225.png', '2019-04-02 16:52:25', 2),
(275, 13, 'public/upload/20190402165235.png', '2019-04-02 16:52:36', 0),
(276, 13, 'public/upload/20190402165250.png', '2019-04-02 16:52:50', 0),
(277, 13, 'public/upload/20190402165259.png', '2019-04-02 16:52:59', 2),
(278, 13, 'public/upload/20190402165307.png', '2019-04-02 17:22:08', 2),
(279, 13, 'public/upload/20190402165316.png', '2019-04-02 16:53:16', 0),
(280, 14, 'public/upload/20190402170000.png', '2019-04-02 17:00:00', 0),
(281, 14, 'public/upload/20190402170009.png', '2019-04-02 17:00:09', 1),
(282, 14, 'public/upload/20190402170017.png', '2019-04-02 17:00:17', 1),
(283, 14, 'public/upload/20190402170025.png', '2019-04-02 17:00:25', 1),
(284, 14, 'public/upload/20190402170033.png', '2019-04-02 17:00:33', 1),
(285, 14, 'public/upload/20190402170042.png', '2019-04-02 17:20:42', 2),
(286, 15, 'public/upload/20190402171923.png', '2019-04-02 17:19:24', 0),
(288, 15, 'public/upload/20190402171944.png', '2019-04-02 17:19:44', 0),
(289, 15, 'public/upload/20190402171958.png', '2019-04-02 16:20:28', 0),
(290, 15, 'public/upload/20190402172008.png', '2019-04-02 17:20:08', 0),
(291, 15, 'public/upload/20190402172017.png', '2019-04-02 17:20:17', 0),
(292, 15, 'public/upload/20190402172028.png', '2019-04-02 17:19:58', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(500) NOT NULL,
  `activation_code` varchar(500) NOT NULL,
  `user_status` varchar(50) NOT NULL DEFAULT 'not verified',
  `token` varchar(255) NOT NULL,
  `notif` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `activation_code`, `user_status`, `token`, `notif`) VALUES
(13, 'noel', 'noel@camagru.fr', '$2y$10$IGfnk5bC5g9My5ToHgKikuwaiiIkUQnvVwBso0yo2ElZGkxz1LGXS', '1f4477bad7af3616c1f933a02bfabe4e', 'verified', '', 1),
(14, 'suri', 'suri@camagru.fr', '$2y$10$p6oFhWwWChV0WKb2spB2BuYed5/UNMDVcf4ZvXaoVrI/cX/PBYeP2', '6aca97005c68f1206823815f66102863', 'verified', '', 1),
(15, 'iz', 'iz@camagru.fr', '$2y$10$xGdQ4uIv2NwR8qSDAhAqc.C/jTGCNC/o3raOL3fHPF5qseiU2qBqK', 'f033ab37c30201f73f142449d037028d', 'verified', '', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id_comment`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id_like`);

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
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id_comment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id_like` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `picture`
--
ALTER TABLE `picture`
  MODIFY `id_img` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=300;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

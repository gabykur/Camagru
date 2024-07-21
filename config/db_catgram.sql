-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3308
-- Generation Time: Apr 04, 2019 at 01:40 AM
-- Server version: 5.7.24
-- PHP Version: 7.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: `db_catgram`
CREATE DATABASE IF NOT EXISTS `db_catgram` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `db_catgram`;

-- --------------------------------------------------------
-- Drop tables if they exist
-- --------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `login_attempts`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `likes`;
DROP TABLE IF EXISTS `pictures`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `stickers`;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(500) NOT NULL,
  `activation_code` varchar(500) NOT NULL,
  `user_status` varchar(50) NOT NULL DEFAULT 'not verified',
  `token` varchar(255) NOT NULL,
  `notif` int(11) NOT NULL,
  `account_locked` TINYINT(1) NOT NULL DEFAULT 0,
  `account_locked_until` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `username`, `email`, `password`, `activation_code`, `user_status`, `token`, `notif`, `account_locked`) VALUES
(13, 'noel', 'noel@catgram.fr', '$2y$10$IGfnk5bC5g9My5ToHgKikuwaiiIkUQnvVwBso0yo2ElZGkxz1LGXS', '1f4477bad7af3616c1f933a02bfabe4e', 'verified', '', 1, 0),
(14, 'suri', 'suri@catgram.fr', '$2y$10$p6oFhWwWChV0WKb2spB2BuYed5/UNMDVcf4ZvXaoVrI/cX/PBYeP2', '6aca97005c68f1206823815f66102863', 'verified', '', 1, 0),
(15, 'iz', 'iz@catgram.fr', '$2y$10$xGdQ4uIv2NwR8qSDAhAqc.C/jTGCNC/o3raOL3fHPF5qseiU2qBqK', 'f033ab37c30201f73f142449d037028d', 'verified', '', 1, 0);

-- --------------------------------------------------------
-- Table structure for table `pictures`
-- --------------------------------------------------------

CREATE TABLE `pictures` (
  `id_img` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `likes` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_img`),
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table `pictures`
INSERT INTO `pictures` (`id_img`, `id_user`, `img`, `date`, `likes`) VALUES
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
-- Table structure for table `comments`
-- --------------------------------------------------------

CREATE TABLE `comments` (
  `id_comment` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_img` int(11) NOT NULL,
  `comment` text NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_comment`),
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_img`) REFERENCES `pictures`(`id_img`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table `comments`
INSERT INTO `comments` (`id_comment`, `id_user`, `id_img`, `comment`, `date`) VALUES
(39, 14, 274, 'WOW my husband is so beautiful', '2019-04-02 17:01:06'),
(40, 14, 278, 'always handsome darling', '2019-04-02 17:01:30'),
(41, 14, 277, 'Why so sad Noel ?', '2019-04-02 17:01:44'),
(42, 13, 278, 'thank you Honey &lt;3', '2019-04-02 17:02:39'),
(43, 13, 277, 'because you werent next to me', '2019-04-02 17:02:58'),
(44, 13, 274, 'YASSSSSSSSSS QUEEEN', '2019-04-02 17:03:12'),
(45, 13, 285, 'My wife is the most beautiful cat in the world', '2019-04-02 17:03:33'),
(46, 13, 285, 'I love you baby', '2019-04-02 17:03:39'),
(47, 13, 281, 'hahhahahaha you are so lazy', '2019-04-02 17:03:57'),
(48, 13, 284, 'Cant believe that this cat is the mother of my 3 children', '2019-04-02 17:04:41'),
(49, 13, 284, 'You are stunning', '2019-04-02 17:04:46'),
(50, 13, 283, 'Dont be mad Suri !', '2019-04-02 17:05:05'),
(51, 13, 276, 'hehehe i look cute', '2019-04-02 17:05:21'),
(52, 13, 288, 'fetetert', '2019-04-03 17:25:55'),
(53, 13, 288, 'etetetewte', '2019-04-03 17:25:59'),
(54, 13, 288, 'erwtwtewt', '2019-04-03 17:26:02'),
(55, 13, 288, 'etewtewtewte', '2019-04-03 17:26:05'),
(56, 13, 288, 'ertretre', '2019-04-03 17:26:50'),
(57, 13, 288, 'rtret', '2019-04-03 17:26:53');

-- --------------------------------------------------------
-- Table structure for table `likes`
-- --------------------------------------------------------

CREATE TABLE `likes` (
  `id_like` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_img` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_like`),
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_img`) REFERENCES `pictures`(`id_img`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table `likes`
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
-- Table structure for table `stickers`
-- --------------------------------------------------------

CREATE TABLE `stickers` (
  `id_sticker` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY (`id_sticker`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table `stickers`
INSERT INTO `stickers` (`id_sticker`, `name`, `path`) VALUES
(1, 'Poop', 'public/stickers/poop.png'),
(2, 'Peach', 'public/stickers/peach.png'),
(3, 'Watermelon', 'public/stickers/watermelon.png'),
(4, 'Pig', 'public/stickers/pig.png'),
(5, 'Call Me', 'public/stickers/callme.png');

-- --------------------------------------------------------
-- Table structure for table `login_attempts`
-- --------------------------------------------------------

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `attempt_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- AUTO_INCREMENT for dumped tables

ALTER TABLE `comments`
  MODIFY `id_comment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

ALTER TABLE `likes`
  MODIFY `id_like` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

ALTER TABLE `pictures`
  MODIFY `id_img` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=293;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `stickers`
  MODIFY `id_sticker` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

COMMIT;

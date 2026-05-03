-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2026 at 03:31 PM
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
-- Database: `chat_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `is_seen` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_edited` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `file_path`, `is_seen`, `created_at`, `is_edited`) VALUES
(1, 4, 5, 'რას შვრები როგორ ხარ?', NULL, 1, '2026-05-01 09:32:52', 0),
(3, 5, 4, '', '1777628391_IMG_4321.jpeg', 1, '2026-05-01 09:39:51', 0),
(5, 4, 5, 'jfeiojijwieofj iwjf iwejiwfj fiwjf oijwiojf wjfi jwijfiw jfiwejf iejfiwjfejwfeijwfeijefoijfiojefijei ifopwefiejfieojfiellllllllllllllllllllllllllllllllllllllll', NULL, 1, '2026-05-01 09:47:08', 1),
(6, 4, 5, 'ერგერგ', NULL, 1, '2026-05-01 09:57:19', 0),
(7, 5, 4, 'ეწფწეფ სვფერფწფ წფეწფ', NULL, 1, '2026-05-01 10:07:33', 1),
(8, 4, 5, '', '1777632090_Gemini_Generated_Image_b0die2b0die2b0di.png', 1, '2026-05-01 10:41:30', 0),
(9, 4, 5, 'რჰგერტგრტგ', NULL, 1, '2026-05-01 10:45:20', 0),
(10, 4, 5, 'დტგჰდტგჰდტგ', NULL, 1, '2026-05-01 10:45:23', 0);

-- --------------------------------------------------------

--
-- Table structure for table `message_reactions`
--

CREATE TABLE `message_reactions` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reaction_type` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `message_reactions`
--

INSERT INTO `message_reactions` (`id`, `message_id`, `user_id`, `reaction_type`, `created_at`) VALUES
(2, 7, 4, 'heart', '2026-05-01 13:29:14'),
(3, 3, 4, 'heart', '2026-05-01 13:30:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT 'default.png',
  `status` enum('online','offline') DEFAULT 'offline',
  `is_typing` int(11) DEFAULT 0,
  `last_seen` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pin` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `phone`, `password`, `avatar`, `status`, `is_typing`, `last_seen`, `created_at`, `pin`) VALUES
(4, 'achikod', 'არჩილ', 'დიასამიძე', '+995555402383', '$2y$10$1F93njkbaS8Byvk7r7rXtO7fb35ZgtvBHJXKIxR/lbwSsni9A3/x.', '1777635227_unnamed.jpg', 'online', 0, '2026-05-01 10:24:30', '2026-05-01 09:17:25', '$2y$10$BAZldlbuU5061DElo/qZZOeT0qibwKS56kD/xJwPOd5T6nA15zDbq'),
(5, 'test', 'გიორგი', 'აბულაძე', '+995555555555', '$2y$10$bvdSUsan6OJnWCg/Qc1kSuGk7qe5pKAfieJuDGkeg94lITS9UjTdi', 'default.png', 'offline', 0, '2026-05-01 10:13:36', '2026-05-01 09:22:54', NULL),
(8, 'test2', 'დათო', 'დევაძე', '+995555123456', '$2y$10$izAwWVQEIkMyuf25qJvxO.ELlutaVgxn8t/hHUg5wBet7ee00qbze', '1777641651_accounting-pie-graph-cwjojcbmhkjgqpvz.jpg', 'offline', 0, '2026-05-01 13:20:51', '2026-05-01 13:20:51', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `message_reactions`
--
ALTER TABLE `message_reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_reaction` (`message_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `message_reactions`
--
ALTER TABLE `message_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `message_reactions`
--
ALTER TABLE `message_reactions`
  ADD CONSTRAINT `message_reactions_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_reactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

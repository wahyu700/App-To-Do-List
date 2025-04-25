-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 25, 2025 at 04:33 AM
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
-- Database: `todo_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `status` enum('belum','selesai') NOT NULL DEFAULT 'belum',
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `judul`, `status`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 'membuat web', 'belum', 2, '2025-04-09 16:19:46', '2025-04-11 09:57:33'),
(3, 'membuat mobil', 'selesai', 4, '2025-04-10 16:11:55', '2025-04-11 15:42:44'),
(7, 'mudik ke kampung halaman', 'selesai', 2, '2025-04-11 05:46:55', '2025-04-11 06:09:42'),
(14, 'mudik ke kuningan', 'selesai', 5, '2025-04-11 08:49:12', '2025-04-18 10:50:58'),
(18, 'beli mobil', 'belum', 4, '2025-04-16 07:43:56', '2025-04-16 07:43:56'),
(19, 'balikk', 'belum', 4, '2025-04-16 07:44:54', '2025-04-16 16:01:15'),
(20, 'tesssss', 'belum', NULL, '2025-04-16 16:15:46', '2025-04-17 04:57:14');

-- --------------------------------------------------------

--
-- Table structure for table `tasks_assignments`
--

CREATE TABLE `tasks_assignments` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks_assignments`
--

INSERT INTO `tasks_assignments` (`id`, `task_id`, `user_id`, `assigned_at`) VALUES
(52, 20, 4, '2025-04-17 08:50:29'),
(53, 19, 4, '2025-04-17 08:50:38'),
(54, 18, 4, '2025-04-17 08:50:46'),
(56, 7, 4, '2025-04-17 08:51:02'),
(57, 3, 4, '2025-04-17 08:51:13'),
(61, 14, 11, '2025-04-18 10:19:49'),
(62, 2, 11, '2025-04-18 10:19:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'admin', NULL, '$2y$10$pxVNVY3yt4aTPaj11NLKzuIjLF0zwOj9KIsTr126wvEio/4324xmq', 'admin', '2025-04-11 23:34:48'),
(3, 'wahyu', NULL, '$2y$10$Ln6Q06QnLbt65tlqU1.Ct.UEm6WskJTfmID.y7ztRWDPUZZtj2fSG', 'admin', '2025-04-11 23:34:48'),
(4, 'tonjen', 'hafiz@gmail.com', '$2y$10$ExlYyc2mfnshA8AT.Oc0Au2kMWcWjTihbL.lqKOjWr0H/bze.DpHG', 'user', '2025-04-11 23:34:48'),
(5, 'zaki', 'zaki@gmail.com', '$2y$10$UXF2jAW2JNMLyn7U9rJWO.aWJ5jENhr46HuVRwENt7fBU4mRNxniy', 'user', '2025-04-11 23:34:48'),
(6, 'lukman', 'lukman@gmail.com', '$2y$10$JCBDqcUBE9aAsBGHnN9U3ea9pjeJT6oM1FCJPS32KvLOzuSvpS3W2', 'user', '2025-04-11 23:34:48'),
(7, 'rama', 'rama@gmail.com', '$2y$10$8ysNSqxi0KDYW7B8KmBVKeHSHpy9Y3eiFeC1bzUtDlyCS7fEZ7Rtu', 'user', '2025-04-16 17:54:35'),
(11, 'tes', 'tes@gmail.com', '$2y$10$D7BtW9iWaXQiDqYTGxcg.upb9NsxBibCPOQAD3ytx9mizxTJFBXra', 'user', '2025-04-18 17:18:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `tasks_assignments`
--
ALTER TABLE `tasks_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_task_assignment` (`task_id`),
  ADD KEY `fk_user_assignment` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tasks_assignments`
--
ALTER TABLE `tasks_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks_assignments`
--
ALTER TABLE `tasks_assignments`
  ADD CONSTRAINT `fk_task_assignment` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_assignment` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

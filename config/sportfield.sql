-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2026 at 06:53 PM
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
-- Database: `sportfield`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lapangan_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id`, `user_id`, `lapangan_id`, `tanggal`, `jam_mulai`, `jam_selesai`, `total_harga`, `status`, `created_at`) VALUES
(1, 2, 5, '2026-01-19', '07:00:00', '23:00:00', 1600000.00, 'pending', '2026-01-19 14:11:29'),
(2, 2, 9, '2026-01-19', '06:00:00', '10:00:00', 80000.00, 'pending', '2026-01-19 14:12:35'),
(3, 1, 5, '2026-01-20', '06:00:00', '09:00:00', 300000.00, 'pending', '2026-01-19 15:13:57');

-- --------------------------------------------------------

--
-- Table structure for table `fasilitas`
--

CREATE TABLE `fasilitas` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fasilitas`
--

INSERT INTO `fasilitas` (`id`, `nama`) VALUES
(1, 'AC'),
(2, 'Lighting'),
(3, 'Tribun'),
(4, 'Warung'),
(5, 'Fasilitas 5'),
(6, 'Fasilitas 6');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `lapangan_id` int(11) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jenis_olahraga`
--

CREATE TABLE `jenis_olahraga` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jenis_olahraga`
--

INSERT INTO `jenis_olahraga` (`id`, `nama`) VALUES
(2, 'Badminton'),
(4, 'Basket'),
(1, 'Futsal'),
(6, 'Pingpong'),
(5, 'Tenis'),
(3, 'Voli');

-- --------------------------------------------------------

--
-- Table structure for table `lapangan`
--

CREATE TABLE `lapangan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `harga_per_jam` decimal(10,2) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `status` enum('tersedia','maintenance') DEFAULT 'tersedia',
  `average_rating` decimal(2,1) DEFAULT 0.0,
  `total_rating` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lapangan`
--

INSERT INTO `lapangan` (`id`, `nama`, `jenis`, `harga_per_jam`, `deskripsi`, `gambar`, `status`, `average_rating`, `total_rating`, `created_at`, `updated_at`) VALUES
(5, 'Lapangan A', 'Futsal', 100000.00, 'Lapangan A Description', 'assets/img/lapangan/1768756295_Gajah_Kartu.png', 'tersedia', 2.0, 1, '2026-01-18 17:02:38', '2026-01-19 15:14:49'),
(7, 'Lapangan C', 'Voli', 90000.00, 'Lapangan C Description', 'assets/img/lapangan/1768756295_Gajah_Kartu.png', 'maintenance', 0.0, 0, '2026-01-18 17:02:38', '2026-01-18 17:11:35'),
(9, 'Badminton Court', 'Badminton', 20000.00, 'asdasdasx', 'assets/img/lapangan/1768756218_logo.jpeg', 'tersedia', 3.5, 2, '2026-01-18 17:10:18', '2026-01-19 15:00:07');

-- --------------------------------------------------------

--
-- Table structure for table `lapangan_fasilitas`
--

CREATE TABLE `lapangan_fasilitas` (
  `id` int(11) NOT NULL,
  `lapangan_id` int(11) NOT NULL,
  `fasilitas_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lapangan_fasilitas`
--

INSERT INTO `lapangan_fasilitas` (`id`, `lapangan_id`, `fasilitas_id`) VALUES
(30, 7, 2),
(31, 7, 3),
(35, 5, 1),
(36, 5, 2),
(37, 9, 2),
(38, 9, 5);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `metode` varchar(50) NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `status` enum('pending','success','failed','cancelled','refunded') DEFAULT 'pending',
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `refund_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `booking_id`, `metode`, `jumlah`, `status`, `bukti_bayar`, `created_at`, `refund_reason`) VALUES
(1, 1, 'qris', 1600000.00, 'pending', 'assets/img/bukti-pembayaran/1768831889_logo.jpeg', '2026-01-19 14:11:29', 'Diminta oleh admin'),
(2, 2, 'transfer', 80000.00, 'pending', 'assets/img/bukti-pembayaran/1768831955_Gajah_Kartu.png', '2026-01-19 14:12:35', 'Booking dibatalkan oleh admin'),
(3, 3, 'qris', 300000.00, 'pending', 'assets/img/bukti-pembayaran/1768835637_logo.jpeg', '2026-01-19 15:13:57', 'Booking dibatalkan oleh admin');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lapangan_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `lapangan_id`, `booking_id`, `rating`, `review`, `created_at`, `updated_at`) VALUES
(1, 2, 9, NULL, 2, 'bagus bgt', '2026-01-19 14:46:04', '2026-01-19 14:59:04'),
(2, 1, 9, NULL, 5, 'mantap bgt loh\r\n', '2026-01-19 15:00:07', '2026-01-19 15:00:07'),
(3, 1, 5, NULL, 2, '', '2026-01-19 15:02:35', '2026-01-19 15:14:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Cahyahadi', 'cahya@gmail.com', '$2y$10$hfY6m2CbOdrTEpaVpbLu3uSnomVp7.D7pgAMBjzhW66bT/XQ/BaUi', '081987653214', 'user', '2026-01-14 10:51:16', '2026-01-14 10:51:16'),
(2, 'Cah Admin', 'admin@gmail.com', '$2y$10$mTCdtbSoSzOm.h5HUJYhSOCnluA6KZGAElTpXsuY/9B9a5vTEnexe', '081987653214', 'admin', '2026-01-14 10:58:25', '2026-01-18 15:45:34'),
(3, 'user', 'user@user.com', '$2y$10$jac3axfoGo1y9za/m5AGQuJdWMn3PW9dhKYDbWk9pcZUGVTrguFl.', '081987653214', 'user', '2026-01-14 10:59:54', '2026-01-14 10:59:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `lapangan_id` (`lapangan_id`);

--
-- Indexes for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lapangan_id` (`lapangan_id`);

--
-- Indexes for table `jenis_olahraga`
--
ALTER TABLE `jenis_olahraga`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama` (`nama`);

--
-- Indexes for table `lapangan`
--
ALTER TABLE `lapangan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_nama` (`nama`);

--
-- Indexes for table `lapangan_fasilitas`
--
ALTER TABLE `lapangan_fasilitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lapangan_id` (`lapangan_id`),
  ADD KEY `fasilitas_id` (`fasilitas_id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `lapangan_id` (`lapangan_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fasilitas`
--
ALTER TABLE `fasilitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jenis_olahraga`
--
ALTER TABLE `jenis_olahraga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lapangan`
--
ALTER TABLE `lapangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lapangan_fasilitas`
--
ALTER TABLE `lapangan_fasilitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`lapangan_id`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`lapangan_id`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lapangan_fasilitas`
--
ALTER TABLE `lapangan_fasilitas`
  ADD CONSTRAINT `lapangan_fasilitas_ibfk_1` FOREIGN KEY (`lapangan_id`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lapangan_fasilitas_ibfk_2` FOREIGN KEY (`fasilitas_id`) REFERENCES `fasilitas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Update pembayaran enum to support 'cancelled' status
--
ALTER TABLE `pembayaran` MODIFY COLUMN `status` enum('pending','success','failed','cancelled','refunded') DEFAULT 'pending';

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`lapangan_id`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_3` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

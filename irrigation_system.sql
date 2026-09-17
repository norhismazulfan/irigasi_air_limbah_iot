-- phpMyAdmin SQL Dump
-- version 5.3.0-dev+20221012.46fdea0d0e
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 16 Jul 2025 pada 10.06
-- Versi server: 10.4.24-MariaDB-log
-- Versi PHP: 8.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `irrigation_system`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `sensor_data`
--

CREATE TABLE `sensor_data` (
  `id` int(11) NOT NULL,
  `ph` decimal(5,2) DEFAULT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `soil_moisture` decimal(5,2) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `sensor_data`
--

INSERT INTO `sensor_data` (`id`, `ph`, `temperature`, `soil_moisture`, `timestamp`) VALUES
(1, 6.80, 28.50, 75.00, '2025-06-02 19:21:50'),
(2, 7.20, 30.00, 65.00, '2025-06-02 19:21:50'),
(3, 7.00, 29.30, 70.00, '2025-06-02 19:21:50'),
(4, 6.50, 27.80, 80.00, '2025-06-02 19:21:50'),
(5, 6.90, 28.00, 72.00, '2025-06-02 19:21:50'),
(6, 7.10, 29.00, 68.00, '2025-06-02 19:21:50'),
(7, 6.70, 27.50, 85.00, '2025-06-02 19:21:50'),
(8, 7.30, 30.20, 60.00, '2025-06-02 19:21:50'),
(9, 6.80, 28.80, 78.00, '2025-06-02 19:21:50'),
(10, 7.00, 29.50, 66.00, '2025-06-02 19:21:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sensor_limits`
--

CREATE TABLE `sensor_limits` (
  `id` int(11) NOT NULL,
  `ph_limit` decimal(5,2) DEFAULT NULL,
  `moisture_limit` int(11) DEFAULT NULL,
  `temperature_limit` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `sensor_limits`
--

INSERT INTO `sensor_limits` (`id`, `ph_limit`, `moisture_limit`, `temperature_limit`) VALUES
(1, 7.50, 50, 28.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_control`
--

CREATE TABLE `tb_control` (
  `id` int(11) NOT NULL,
  `mode_control` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_control`
--

INSERT INTO `tb_control` (`id`, `mode_control`) VALUES
(1, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_pompa`
--

CREATE TABLE `tb_pompa` (
  `id` int(11) NOT NULL,
  `pump_status` tinyint(1) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_pompa`
--

INSERT INTO `tb_pompa` (`id`, `pump_status`, `timestamp`) VALUES
(1, 1, '2025-06-03 02:21:50'),
(2, 0, '2025-06-03 02:21:50'),
(3, 1, '2025-06-03 02:21:50'),
(4, 0, '2025-06-03 02:21:50'),
(5, 1, '2025-06-03 02:21:50'),
(6, 0, '2025-06-03 02:21:50'),
(7, 1, '2025-06-03 02:21:50'),
(8, 0, '2025-06-03 02:21:50'),
(9, 1, '2025-06-03 02:21:50'),
(10, 0, '2025-06-03 02:21:50'),
(16, 1, '2025-06-06 19:54:15'),
(17, 0, '2025-06-06 19:54:20'),
(18, 1, '2025-06-06 20:02:14'),
(19, 0, '2025-06-06 20:02:16'),
(20, 1, '2025-06-09 19:02:26'),
(21, 0, '2025-06-09 19:02:28'),
(22, 1, '2025-06-09 19:02:38'),
(23, 0, '2025-06-09 19:02:40'),
(24, 1, '2025-06-09 19:09:43'),
(25, 0, '2025-06-09 19:09:45'),
(26, 1, '2025-06-09 19:10:12'),
(27, 0, '2025-06-09 19:11:24'),
(28, 0, '2025-06-09 19:13:42'),
(29, 1, '2025-06-09 19:13:57'),
(30, 0, '2025-06-09 19:14:08'),
(31, 0, '2025-06-13 05:50:12'),
(32, 1, '2025-06-13 05:51:09'),
(33, 0, '2025-06-13 05:51:18'),
(34, 1, '2025-06-13 06:21:26'),
(35, 0, '2025-06-13 06:21:39'),
(36, 1, '2025-06-13 06:21:45'),
(37, 0, '2025-06-13 06:21:49'),
(38, 0, '2025-06-13 06:22:02'),
(39, 1, '2025-06-13 07:14:39'),
(40, 0, '2025-06-13 07:14:42'),
(41, 0, '2025-06-13 07:17:39'),
(42, 1, '2025-06-13 07:29:29'),
(43, 0, '2025-06-13 07:30:44'),
(44, 1, '2025-06-13 07:37:15'),
(45, 0, '2025-06-13 07:37:35'),
(46, 1, '2025-06-13 07:38:40'),
(47, 0, '2025-06-13 07:40:05'),
(48, 1, '2025-06-13 07:46:48'),
(49, 0, '2025-06-13 07:47:04'),
(51, 0, '2025-07-16 13:59:41'),
(53, 1, '2025-07-16 14:00:34'),
(55, 0, '2025-07-16 14:00:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', 'admin1', 'admin'),
(2, 'user', 'user_password', 'user'),
(3, 'yaya', '123123', 'user'),
(4, 'jaja', 'juju', 'user'),
(5, 'ok', 'ok', 'user');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `sensor_data`
--
ALTER TABLE `sensor_data`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sensor_limits`
--
ALTER TABLE `sensor_limits`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_control`
--
ALTER TABLE `tb_control`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_pompa`
--
ALTER TABLE `tb_pompa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `sensor_data`
--
ALTER TABLE `sensor_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `sensor_limits`
--
ALTER TABLE `sensor_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_pompa`
--
ALTER TABLE `tb_pompa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

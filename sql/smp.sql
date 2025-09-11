-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Sep 2025 pada 16.30
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smp`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `lesson_schedule`
--

CREATE TABLE `lesson_schedule` (
  `id` int(11) NOT NULL,
  `class` varchar(10) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `teacher` varchar(100) NOT NULL,
  `day` varchar(20) NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `lesson_schedule`
--

INSERT INTO `lesson_schedule` (`id`, `class`, `subject`, `teacher`, `day`, `time_start`, `time_end`) VALUES
(1, 'VII', 'Matematika', 'Bu Siti', 'Senin', '07:00:00', '08:30:00'),
(2, 'VII', 'Bahasa Indonesia', 'Pak Andi', 'Senin', '08:30:00', '10:00:00'),
(3, 'VII', 'IPA', 'Bu Rina', 'Selasa', '07:00:00', '08:30:00'),
(4, 'VII', 'IPS', 'Pak Budi', 'Selasa', '08:30:00', '10:00:00'),
(5, 'VII', 'Seni Budaya', 'Bu Nabila', 'Rabu', '10:00:00', '11:30:00'),
(6, 'VII', 'TIK', 'Pak Irsal', 'Kamis', '11:30:00', '13:00:00'),
(7, 'VIII', 'Matematika', 'Bu Siti', 'Senin', '07:00:00', '08:30:00'),
(8, 'VIII', 'Bahasa Inggris', 'Pak Joko', 'Senin', '08:30:00', '10:00:00'),
(9, 'VIII', 'IPA', 'Bu Rina', 'Rabu', '07:00:00', '08:30:00'),
(10, 'VIII', 'PPKN', 'Bu Dewi', 'Rabu', '08:30:00', '10:00:00'),
(11, 'VIII', 'Seni Budaya', 'Bu Nabila', 'Kamis', '10:00:00', '11:30:00'),
(12, 'VIII', 'TIK', 'Pak Irsal', 'Jumat', '11:30:00', '13:00:00'),
(13, 'IX', 'Matematika', 'Bu Siti', 'Kamis', '07:00:00', '08:30:00'),
(14, 'IX', 'Bahasa Indonesia', 'Pak Andi', 'Kamis', '08:30:00', '10:00:00'),
(15, 'IX', 'Bahasa Inggris', 'Pak Joko', 'Jumat', '07:00:00', '08:30:00'),
(16, 'IX', 'IPS', 'Pak Budi', 'Jumat', '08:30:00', '10:00:00'),
(17, 'IX', 'Seni Budaya', 'Bu Nabila', 'Jumat', '10:00:00', '11:30:00'),
(18, 'IX', 'TIK', 'Pak Irsal', 'Senin', '11:30:00', '13:00:00'),
(19, 'VII', 'Mandarin', 'Adyas', 'Senin', '10:45:00', '11:30:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `result`
--

CREATE TABLE `result` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class` varchar(10) NOT NULL,
  `month` varchar(2) NOT NULL,
  `week` tinyint(1) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `result`
--

INSERT INTO `result` (`id`, `student_id`, `teacher_id`, `class`, `month`, `week`, `status`, `created_at`, `updated_at`) VALUES
(4, 1, 14, 'VII', '09', 2, 'Hadir', '2025-09-08 08:50:18', '2025-09-08 08:50:18'),
(5, 2, 14, 'VII', '09', 2, 'Izin', '2025-09-08 08:50:22', '2025-09-08 08:50:22'),
(6, 3, 14, 'VII', '09', 2, 'Tidak Hadir', '2025-09-08 08:50:25', '2025-09-08 08:50:25');

-- --------------------------------------------------------

--
-- Struktur dari tabel `students_ix`
--

CREATE TABLE `students_ix` (
  `id` int(11) NOT NULL,
  `class` varchar(10) DEFAULT 'IX',
  `name` varchar(100) NOT NULL,
  `nim` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `students_ix`
--

INSERT INTO `students_ix` (`id`, `class`, `name`, `nim`) VALUES
(1, 'IX', 'Siti Rohmah', 'IX001'),
(2, 'IX', 'Doni Firmansyah', 'IX002'),
(3, 'IX', 'Lina Kartika', 'IX003');

-- --------------------------------------------------------

--
-- Struktur dari tabel `students_vii`
--

CREATE TABLE `students_vii` (
  `id` int(11) NOT NULL,
  `class` varchar(10) DEFAULT 'VII',
  `name` varchar(100) NOT NULL,
  `nim` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `students_vii`
--

INSERT INTO `students_vii` (`id`, `class`, `name`, `nim`) VALUES
(1, 'VII', 'Andi Setiawan', 'VII001'),
(2, 'VII', 'Rina Putri', 'VII002'),
(3, 'VII', 'Dewi Lestari', 'VII003'),
(5, 'VII', 'Ramadhan', 'VII005');

-- --------------------------------------------------------

--
-- Struktur dari tabel `students_viii`
--

CREATE TABLE `students_viii` (
  `id` int(11) NOT NULL,
  `class` varchar(10) DEFAULT 'VIII',
  `name` varchar(100) NOT NULL,
  `nim` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `students_viii`
--

INSERT INTO `students_viii` (`id`, `class`, `name`, `nim`) VALUES
(1, 'VIII', 'Agus Santoso', 'VIII001'),
(2, 'VIII', 'Bunga Cahaya', 'VIII002'),
(3, 'VIII', 'Fajar Pratama', 'VIII003');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('teacher','headmaster') NOT NULL DEFAULT 'teacher'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `subject`, `email`, `username`, `password`, `role`) VALUES
(14, 'Nabila', 'Seni Budaya', 'NabilasyifaAzzahra@gmail.com', 'Nabila', '$2y$10$x64wpO3IRf1XbB7N6JjfU.ewU3CI2XN.xQtaRjJ2F7aWtwOoFv/z.', 'teacher'),
(15, 'Irsal', 'TIK', 'Cadis0711@gmail.com', 'Irsal', '$2y$10$UnrnvmL4UXgA4q9VZyT8M.NzOpt8Hk/p3vAtj5.sr0YBNcruuqDya', 'teacher'),
(16, 'Siti', 'Matematika', 'siti.math@gmail.com', 'Siti', '$2y$10$abcdefghijk1234567890lmnopqrstuv', 'teacher'),
(17, 'Andi', 'Bahasa Indonesia', 'andi.bindo@gmail.com', 'Andi', '$2y$10$abcdefghijk1234567890lmnopqrstuv', 'teacher'),
(18, 'Rina', 'IPA', 'rina.ipa@gmail.com', 'Rina', '$2y$10$abcdefghijk1234567890lmnopqrstuv', 'teacher'),
(19, 'Budi', 'IPS', 'budi.ips@gmail.com', 'Budi', '$2y$10$abcdefghijk1234567890lmnopqrstuv', 'teacher'),
(20, 'Joko', 'Bahasa Inggris', 'joko.inggris@gmail.com', 'Joko', '$2y$10$abcdefghijk1234567890lmnopqrstuv', 'teacher'),
(21, 'Dewi', 'PPKN', 'dewi.ppkn@gmail.com', 'Dewi', '$2y$10$abcdefghijk1234567890lmnopqrstuv', 'teacher'),
(22, 'Mia', 'Kepsek', 'mia@gmail.com', 'Mia', '$2y$10$uSX6oV8RJ25VFrFQiW5uIumbNg.zaVa72NDyX2/KWct6U1fkx2/2C', 'headmaster');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `lesson_schedule`
--
ALTER TABLE `lesson_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `result`
--
ALTER TABLE `result`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indeks untuk tabel `students_ix`
--
ALTER TABLE `students_ix`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim` (`nim`);

--
-- Indeks untuk tabel `students_vii`
--
ALTER TABLE `students_vii`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim` (`nim`);

--
-- Indeks untuk tabel `students_viii`
--
ALTER TABLE `students_viii`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim` (`nim`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `lesson_schedule`
--
ALTER TABLE `lesson_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `result`
--
ALTER TABLE `result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `students_ix`
--
ALTER TABLE `students_ix`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `students_vii`
--
ALTER TABLE `students_vii`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `students_viii`
--
ALTER TABLE `students_viii`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

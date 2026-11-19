-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 19, 2026 at 03:30 AM
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
-- Database: `tp_mvc25`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(120) NOT NULL,
  `credits` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `lecturer_id` int(11) NOT NULL
) ;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `code`, `name`, `credits`, `lecturer_id`) VALUES
(2, 'IF101', 'Algoritma dan Pemrograman', 3, 2),
(3, 'IF102', 'Basis Data', 3, 3),
(4, 'IF103', 'Pemrograman Web', 3, 4),
(5, 'IF104', 'Rekayasa Perangkat Lunak', 4, 5),
(6, 'IF105', 'Jaringan Komputer', 3, 6);

-- --------------------------------------------------------

--
-- Table structure for table `course_materials`
--

CREATE TABLE `course_materials` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `resource_url` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_materials`
--

INSERT INTO `course_materials` (`id`, `course_id`, `title`, `description`, `resource_url`) VALUES
(1, 2, 'Pengenalan Algoritma', 'Konsep dasar algoritma dan pseudocode.', 'https://example.com/if101/algoritma'),
(2, 3, 'Model Data Relasional', 'Tabel, kunci utama, dan relasi antar tabel.', 'https://example.com/if102/model-relasional'),
(3, 4, 'Dasar HTML dan CSS', 'Struktur halaman web dan pengaturan tampilannya.', 'https://example.com/if103/html-css'),
(4, 5, 'Siklus Pengembangan Perangkat Lunak', 'Tahapan analisis, desain, implementasi, dan pemeliharaan.', 'https://example.com/if104/sdlc'),
(5, 6, 'Model OSI', 'Pengenalan tujuh lapisan pada model jaringan OSI.', 'https://example.com/if105/model-osi');

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `nidn` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `join_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`id`, `name`, `nidn`, `phone`, `join_date`) VALUES
(2, 'Andi Pratama', '00010001', '081234560001', '2020-01-15'),
(3, 'Budi Santoso', '00010002', '081234560002', '2019-07-01'),
(4, 'Citra Lestari', '00010003', '081234560003', '2021-03-10'),
(5, 'Dewi Anggraini', '00010004', '081234560004', '2018-08-20'),
(6, 'Eko Wibowo', '00010005', '081234560005', '2022-02-01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_courses_lecturers` (`lecturer_id`);

--
-- Indexes for table `course_materials`
--
ALTER TABLE `course_materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_material_course_title` (`course_id`,`title`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nidn` (`nidn`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_materials`
--
ALTER TABLE `course_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `fk_courses_lecturers` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `course_materials`
--
ALTER TABLE `course_materials`
  ADD CONSTRAINT `fk_materials_courses` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

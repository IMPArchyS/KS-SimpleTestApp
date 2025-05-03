-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: May 03, 2025 at 06:16 PM
-- Server version: 8.0.32
-- PHP Version: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `KSZadanie`
--

-- --------------------------------------------------------

--
-- Table structure for table `rozvrh`
--

CREATE TABLE `rozvrh` (
  `id` bigint NOT NULL,
  `den` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `cas_od` time NOT NULL,
  `cas_do` time NOT NULL,
  `typ_akcie` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nazov_akcie` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `miestnost` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `vyucujuci` varchar(150) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rozvrh`
--

INSERT INTO `rozvrh` (`id`, `den`, `cas_od`, `cas_do`, `typ_akcie`, `nazov_akcie`, `miestnost`, `vyucujuci`) VALUES
(34, 'Po', '08:00:00', '09:50:00', 'Prednáška', 'ADS', 'cd150', 'M. Vojvoda'),
(35, 'Po', '13:00:00', '14:50:00', 'Prednáška', 'PPDS', 'ab150', 'M. Nehéz'),
(36, 'Po', '15:00:00', '16:50:00', 'Prednáška', 'AFJ', 'ab150', 'V. Hromada'),
(37, 'Ut', '10:00:00', '11:50:00', 'Cvičenie', 'ADS', 'c117b', 'M. Nehéz'),
(38, 'Ut', '13:00:00', '14:50:00', 'Cvičenie', 'AFJ', 'de300', 'V. Hromada'),
(39, 'Ut', '15:00:00', '16:50:00', 'Prednáška', 'KS', 'de150', 'D. Chudá'),
(41, 'Ut', '17:00:00', '18:50:00', 'Cvičenie', 'KS', 'de150', 'P. Šebeš'),
(42, 'St', '08:00:00', '09:50:00', 'Cvičenie', 'PPDS', 'de35', 'M. Nehéz'),
(43, 'Stv', '08:00:00', '09:50:00', 'Prednáška', 'MBI', 'cd300', 'M. Šalmík'),
(44, 'Stv', '10:00:00', '11:50:00', 'Cvičenie', 'MBI', 'cd300', 'M. Pikula');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rozvrh`
--
ALTER TABLE `rozvrh`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rozvrh`
--
ALTER TABLE `rozvrh`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

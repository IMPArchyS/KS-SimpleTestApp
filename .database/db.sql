-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: mysql
-- Čas generovania: Po 18.Mar 2024, 17:19
-- Verzia serveru: 8.0.32
-- Verzia PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `webte2zadanie2`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `rozvrh`
--

CREATE TABLE `rozvrh` (
  `id` bigint NOT NULL,
  `den` varchar(50) NOT NULL,
  `cas_od` time NOT NULL,
  `cas_do` time NOT NULL,
  `typ_akcie` varchar(100) NOT NULL,
  `nazov_akcie` varchar(150) NOT NULL,
  `miestnost` varchar(150) NOT NULL,
  `vyucujuci` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `rozvrh`
--
ALTER TABLE `rozvrh`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `rozvrh`
--
ALTER TABLE `rozvrh`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- phpMyAdmin SQL
-- Server version: 5.5.42
-- PHP Version: 5.6.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `Charts`
--
CREATE DATABASE IF NOT EXISTS `Charts` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `Charts`;

-- --------------------------------------------------------

--
-- Table structure for table `t_IRR_Data`
--
CREATE TABLE IF NOT EXISTS `t_IRR_Data` (
  `Wavelength` decimal(15,7) NOT NULL,
  `Amplitude` decimal(15,7) NOT NULL,
  `NetColor` varchar(50) NOT NULL DEFAULT '',
  `Position` varchar(30) NOT NULL DEFAULT '',
  `MeasurementType` varchar(50) NOT NULL DEFAULT '',
  `SessionDate` varchar(30) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Indexes for table `t_IRR_Data`
--
ALTER TABLE `t_IRR_Data`
  ADD PRIMARY KEY (`Wavelength`,`NetColor`,`Position`,`MeasurementType`,`SessionDate`);

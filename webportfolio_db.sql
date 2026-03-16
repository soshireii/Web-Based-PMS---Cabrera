-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2026 at 12:10 PM
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
-- Database: `webportfolio_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `ContactID` int(11) NOT NULL,
  `ContactName` varchar(255) NOT NULL,
  `ContactEmail` varchar(255) NOT NULL,
  `ContactSubject` varchar(255) NOT NULL,
  `ContactMessage` text DEFAULT NULL,
  `CreatedTime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `experiencestbl`
--

CREATE TABLE `experiencestbl` (
  `ExpID` int(11) NOT NULL,
  `ProjectID` int(11) NOT NULL,
  `JobTitle` varchar(100) DEFAULT NULL,
  `Company` varchar(100) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projecttbl`
--

CREATE TABLE `projecttbl` (
  `ProjectID` int(11) NOT NULL,
  `ProjectTitle` varchar(100) NOT NULL,
  `ProfileImage` longblob DEFAULT NULL,
  `BackgroundImage` longblob DEFAULT NULL,
  `ProjectDescription` text DEFAULT NULL,
  `TechStack` varchar(200) DEFAULT NULL,
  `ProjectStatus` enum('Active','Archived','In Progress') DEFAULT 'Active',
  `CreatedTime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skillstbl`
--

CREATE TABLE `skillstbl` (
  `SkillID` int(11) NOT NULL,
  `ProjectID` int(11) NOT NULL,
  `SkillName` varchar(100) NOT NULL,
  `Category` varchar(100) DEFAULT NULL,
  `Proficiency` enum('beginner','intermediate','advanced','expert') DEFAULT 'intermediate',
  `CreatedTime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`ContactID`);

--
-- Indexes for table `experiencestbl`
--
ALTER TABLE `experiencestbl`
  ADD PRIMARY KEY (`ExpID`),
  ADD KEY `ProjectID` (`ProjectID`);

--
-- Indexes for table `projecttbl`
--
ALTER TABLE `projecttbl`
  ADD PRIMARY KEY (`ProjectID`);

--
-- Indexes for table `skillstbl`
--
ALTER TABLE `skillstbl`
  ADD PRIMARY KEY (`SkillID`),
  ADD KEY `ProjectID` (`ProjectID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `ContactID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `experiencestbl`
--
ALTER TABLE `experiencestbl`
  MODIFY `ExpID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projecttbl`
--
ALTER TABLE `projecttbl`
  MODIFY `ProjectID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `skillstbl`
--
ALTER TABLE `skillstbl`
  MODIFY `SkillID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `experiencestbl`
--
ALTER TABLE `experiencestbl`
  ADD CONSTRAINT `experiencestbl_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projecttbl` (`ProjectID`) ON DELETE CASCADE;

--
-- Constraints for table `skillstbl`
--
ALTER TABLE `skillstbl`
  ADD CONSTRAINT `skillstbl_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projecttbl` (`ProjectID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2024 at 12:16 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 5.6.39

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `AdminEmail` varchar(120) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `mobileNumber` varchar(20) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `FullName`, `AdminEmail`, `UserName`, `mobileNumber`, `Password`, `updationDate`) VALUES
(1, 'Ali', 'ali@gmail.com', 'ali22', '78990325', 'ac0c6c7dce2ed31e7eccf785ee3c43c9', '2024-05-12 13:24:23');

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `photos` varchar(200) NOT NULL,
  `is_deleted` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`id`, `name`, `photos`, `is_deleted`) VALUES
(1, 'faculty of engineering', 'assets/img/eng.png', 0),
(2, 'faculty of sciences and arts', 'assets/img/s&a.png', 0),
(3, 'faculty of public health', 'assets/img/ph.png', 0),
(4, 'faculty of law', 'assets/img/law.png', 0),
(5, 'faculty of tourism sciences', 'assets/img/ts.png', 0);


-- --------------------------------------------------------

--
-- Table structure for table `majors`
--

CREATE TABLE `majors` (
  `major_id` int(11) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `major` varchar(255) NOT NULL,
  `photos` varchar(10000) NOT NULL,
  `is_deleted` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `majors`
--

INSERT INTO `majors` (`major_id`, `faculty_id`, `major`, `photos`, `is_deleted`) VALUES
(1, 1, 'cce', 'assets\\img\\cce.jpeg', 0),
(2, 1, 'bme', 'assets\\img\\bme.png', 0),
(3, 1, 'civil', 'assets\\img\\civil.png', 0);


-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `major_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `description` text,
  `is_deleted` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `major_id`, `name`, `course_code`, `description`, `is_deleted`) VALUES
(1, 1, 'DataBase', '12wfgc', 'Common cce and bme', 0),
(2, 1, 'Data Communication', '12qws', 'nonee', 0),
(3, 2, 'sub of bme', '12qw6', 'noneeee', 0),
(4, 3, 'civil sub', '3413dq45', 'gjacuakjcbke', 0);

-- --------------------------------------------------------

--
-- Table structure for table `subject_materials`
--

CREATE TABLE `subject_materials` (
  `material_id` int(11) NOT NULL,
  `course_code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `description` text,
  `is_deleted` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


--
-- Table structure for table `tblstudents`
--

CREATE TABLE `tblstudents` (
  `id` int(11) NOT NULL,
  `StudentId` varchar(100) DEFAULT NULL,
  `FullName` varchar(120) NOT NULL,
  `EmailId` varchar(120) NOT NULL,
  `MobileNumber` char(11) NOT NULL,
  `Password` varchar(120) NOT NULL,
  `Status` int(1) NOT NULL DEFAULT '1',
  `RegDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblstudents`
--

INSERT INTO `tblstudents` (`id`, `StudentId`, `FullName`, `EmailId`, `MobileNumber`, `Password`, `Status`, `RegDate`, `UpdationDate`) VALUES
(1, '69669', 'amsts', 'amst@gmail.com', '71555444', 'ac0c6c7dce2ed31e7eccf785ee3c43c9', 1, '2024-05-10 22:01:57', '2024-05-18 11:23:08');


--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `majors`
--
ALTER TABLE `majors`
  ADD PRIMARY KEY (`major_id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `major_id` (`major_id`);

--
-- Indexes for table `subject_materials`
--
ALTER TABLE `subject_materials`
  ADD PRIMARY KEY (`material_id`);

--
-- Indexes for table `tblstudents`
--
ALTER TABLE `tblstudents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `StudentId` (`StudentId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `majors`
--
ALTER TABLE `majors`
  MODIFY `major_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subject_materials`
--
ALTER TABLE `subject_materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `tblstudents`
--
ALTER TABLE `tblstudents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `majors`
--
ALTER TABLE `majors`
  ADD CONSTRAINT `majors_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`major_id`) REFERENCES `majors` (`major_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2025 at 12:51 PM
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
-- Database: `barangay_health_center`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_trail`
--

CREATE TABLE `audit_trail` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `employee_id` varchar(255) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_trail`
--

INSERT INTO `audit_trail` (`id`, `user_id`, `employee_id`, `action`, `details`, `status`, `timestamp`) VALUES
(24, NULL, NULL, 'Login', 'Failed login attempt for username/email: nya@gmail.com', 'Failed', '2025-10-04 21:20:06'),
(25, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin_user', 'Failed', '2025-10-04 21:20:16'),
(30, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin_user', 'Failed', '2025-10-04 21:29:05'),
(46, NULL, NULL, 'Login', 'Failed login attempt for username/email: nya@gmail.com', 'Failed', '2025-10-04 23:30:33'),
(77, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin_user', 'Failed', '2025-10-05 16:32:35'),
(95, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin_user', 'Failed', '2025-10-06 10:53:39'),
(112, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin_user', 'Failed', '2025-10-06 19:07:10'),
(124, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin_user', 'Failed', '2025-10-09 17:48:49'),
(125, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin_user', 'Failed', '2025-10-09 17:48:49'),
(150, NULL, NULL, 'Deleted employee account: ', NULL, NULL, '2025-10-09 19:22:52'),
(165, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin_user', 'Failed', '2025-10-10 06:16:11'),
(170, NULL, NULL, 'Login', 'Failed login attempt for username/email: mami@gmail.com', 'Failed', '2025-10-10 09:17:30'),
(186, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin_user', 'Failed', '2025-10-12 14:09:38'),
(207, NULL, NULL, 'Login', 'Failed login attempt for username/email: brgyadmin@bagongsilang.gov.ph', 'Failed', '2025-10-13 09:05:04'),
(212, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin@bagongsilang.gov.ph', 'Failed', '2025-10-13 09:08:34'),
(213, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin@bagongsilang.gov.ph', 'Failed', '2025-10-13 09:11:33'),
(215, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin@bagongsilang.gov.ph', 'Failed', '2025-10-13 09:16:09'),
(217, NULL, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-13 09:18:36'),
(219, NULL, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-13 09:18:57'),
(220, NULL, NULL, 'Login', 'Failed login attempt for role:  (username/id: )', 'Failed', '2025-10-13 09:26:34'),
(221, NULL, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-13 09:26:46'),
(222, NULL, NULL, 'Login', 'Failed login attempt for role:  (username/id: )', 'Failed', '2025-10-13 09:27:04'),
(223, NULL, NULL, 'Login', 'Failed login attempt for role: admin (username/id: admin_user)', 'Failed', '2025-10-13 09:27:14'),
(224, NULL, NULL, 'Login', 'Failed login attempt for role: admin (username/id: admin_user)', 'Failed', '2025-10-13 09:27:24'),
(228, NULL, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-13 09:30:15'),
(229, NULL, NULL, 'Login', 'Failed login attempt for role:  (username/id: )', 'Failed', '2025-10-13 09:30:35'),
(230, NULL, NULL, 'Login', 'Failed login attempt for role: admin (username/id: admin_user)', 'Failed', '2025-10-13 09:31:17'),
(231, NULL, NULL, 'Login', 'Staff logged in successfully using Employee ID.', 'Success', '2025-10-13 09:31:29'),
(235, NULL, NULL, 'Login', 'Failed login attempt for username/email: adminbscenter', 'Failed', '2025-10-13 09:40:31'),
(236, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin@bagongsilang.gov.ph', 'Failed', '2025-10-13 09:41:22'),
(241, NULL, NULL, 'Login', 'Failed login attempt for username/email: bsadmin', 'Failed', '2025-10-13 09:47:12'),
(242, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin@bshealthcenter.gov.ph', 'Failed', '2025-10-13 09:47:51'),
(243, 21, NULL, 'Login', 'User logged in successfully.', 'Success', '2025-10-13 09:51:08'),
(244, 21, NULL, 'Deleted employee account', 'Removed employee record: Name: Mariel Ilao ', 'Success', '2025-10-13 09:51:15'),
(247, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin@bshealthcenter.gov.ph', 'Failed', '2025-10-13 09:59:46'),
(248, 21, NULL, 'Login', 'User logged in successfully.', 'Success', '2025-10-13 10:00:16'),
(249, 21, NULL, 'Add Inventory', 'Added new medicine: hehea (12 Tablet)', 'Success', '2025-10-13 10:29:45'),
(250, 21, NULL, 'Edit Inventory', 'Edited medicine: hehea, added 15 stock', 'Success', '2025-10-13 10:30:00'),
(251, 21, NULL, 'Logout', 'User  \'adminbagongsilang\' (admin) logged out successfully.', 'Success', '2025-10-13 10:31:02'),
(252, 21, NULL, 'Login', 'User logged in successfully.', 'Success', '2025-10-13 10:31:23'),
(253, 21, NULL, 'Dispense Medicine', 'Dispense 9 x boompow', 'Success', '2025-10-13 10:33:17'),
(254, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin@bagongsilang.gov.ph', 'Failed', '2025-10-15 08:51:50'),
(255, 21, NULL, 'Login', 'User logged in successfully.', 'Success', '2025-10-15 08:52:27'),
(256, 21, NULL, 'Dispense Medicine', 'Dispense 50 x boompow', 'Success', '2025-10-15 08:55:40'),
(257, 21, NULL, 'Added new patient', 'Patient: sasasadee3', 'Success', '2025-10-15 08:56:58'),
(258, 21, NULL, 'Edited patient', 'Updated record for patient: sasasadee3', 'Success', '2025-10-15 08:57:07'),
(259, 21, NULL, 'Add Medical Record', 'Added new medical record for Patient ID: 28 (Doctor: fsdsf, Diagnosis: gege)', 'Success', '2025-10-15 08:57:54'),
(260, 21, NULL, 'Add Medical Record', 'Added new medical record for Patient ID: 29 (Doctor: fgset, Diagnosis: sas)', 'Success', '2025-10-15 09:00:50'),
(261, 21, NULL, 'Edited patient', 'Updated record for patient: Jose Dela Cruz', 'Success', '2025-10-15 09:08:56'),
(262, 21, NULL, 'Add Medical Record', 'Added new medical record for Patient ID: 2 (Doctor: ads, Diagnosis: sasa)', 'Success', '2025-10-15 09:09:34'),
(263, 21, NULL, 'Logout', 'User  \'adminbagongsilang\' (admin) logged out successfully.', 'Success', '2025-10-15 09:22:52'),
(266, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin_user', 'Failed', '2025-10-15 09:30:37'),
(273, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin_user', 'Failed', '2025-10-24 15:39:59'),
(274, NULL, NULL, 'Login', 'Failed login attempt for username/email: admin_user', 'Failed', '2025-10-24 15:40:06'),
(288, NULL, NULL, 'Login', 'Failed login attempt for username/email: Trishami', 'Failed', '2025-10-24 16:09:53'),
(289, NULL, NULL, 'Login', 'Failed login attempt for username/email: admintrisha@gmail.com', 'Failed', '2025-10-24 16:10:21'),
(290, NULL, NULL, 'Login', 'Failed login: Incorrect password for username/email: admin_user', 'Failed', '2025-10-24 16:12:51'),
(291, 22, NULL, 'Login', 'User logged in successfully.', 'Success', '2025-10-24 16:13:07'),
(292, 22, NULL, 'Create Admin', 'Created new admin account (Email: adminmariel@gmail.com, Username: adminmariel)', 'Success', '2025-10-24 16:14:03'),
(293, 22, NULL, 'Logout', 'User  \'Trishami\' (admin) logged out successfully.', 'Success', '2025-10-24 16:15:51'),
(294, NULL, NULL, 'Login', 'Failed login: Incorrect password for username/email: admin_user', 'Failed', '2025-10-24 16:15:57'),
(295, 22, NULL, 'Login', 'User logged in successfully.', 'Success', '2025-10-24 16:19:25'),
(296, 22, NULL, 'Logout', 'User  \'Trishami\' (admin) logged out successfully.', 'Success', '2025-10-24 16:19:35'),
(297, NULL, NULL, 'Login', 'Failed login: Username/Email not found: mami@gmail.com', 'Failed', '2025-10-24 16:19:53'),
(298, NULL, NULL, 'Login', 'Failed login: Username/Email not found: mami@gmail.com', 'Failed', '2025-10-24 16:20:10'),
(299, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-24 16:22:51'),
(300, 22, NULL, 'Logout', 'User  \'Trishami\' (admin) logged out successfully.', 'Success', '2025-10-24 16:22:54'),
(304, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-24 16:29:50'),
(305, NULL, NULL, 'Login', 'Staff logged in successfully.', 'Success', '2025-10-24 16:35:23'),
(306, 22, NULL, 'Logout', 'User  \'trisha mae santos\' (staff) logged out successfully.', 'Success', '2025-10-24 16:35:40'),
(307, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-24 16:36:03'),
(308, 22, NULL, 'Logout', 'User  \'Trishami\' (admin) logged out successfully.', 'Success', '2025-10-24 16:36:08'),
(309, NULL, NULL, 'Login', 'Staff logged in successfully.', 'Success', '2025-10-24 16:36:15'),
(310, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-24 16:36:26'),
(311, 22, NULL, 'Create Employee', 'Added new employee (ID: EMP102, Email: taylorswift@gmail.com)', 'Success', '2025-10-25 12:23:16'),
(312, 22, NULL, 'Logout', 'User  \'Trishami\' (admin) logged out successfully.', 'Success', '2025-10-25 12:23:34'),
(313, NULL, NULL, 'Login', 'Staff logged in successfully.', 'Success', '2025-10-25 12:23:50'),
(314, NULL, NULL, 'Dispense Medicine', 'Dispense 12 x Amoxicillin 250mg', 'Success', '2025-10-25 12:27:04'),
(315, NULL, NULL, 'Added new patient', 'Patient: trisha mae santos', 'Success', '2025-10-25 12:32:24'),
(317, NULL, NULL, 'Login', 'Staff logged in successfully.', 'Success', '2025-10-25 12:38:56'),
(320, NULL, NULL, 'Login', 'Staff logged in successfully.', 'Success', '2025-10-25 12:39:37'),
(329, NULL, NULL, 'Login', 'Failed login attempt for username/email: mami@gmail.com', 'Failed', '2025-10-25 12:41:46'),
(330, NULL, NULL, 'Login', 'Failed login attempt for username/email: taylorswift@gmail.com', 'Failed', '2025-10-25 12:41:57'),
(331, NULL, NULL, 'Login', 'Failed login attempt for username/email: taylorswift@gmail.com', 'Failed', '2025-10-25 12:42:05'),
(332, NULL, NULL, 'Login', 'Staff logged in successfully.', 'Success', '2025-10-25 12:42:21'),
(333, NULL, NULL, 'Added new patient', 'Patient: nye', 'Success', '2025-10-25 12:45:36'),
(334, NULL, NULL, 'Edited patient', 'Updated record for patient: nye', 'Success', '2025-10-25 12:49:53'),
(335, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-25 13:01:13'),
(336, 22, NULL, 'Add Medical Record', 'Added new medical record for Patient ID: 3 (Doctor: ewqeq, Diagnosis: dsas)', 'Success', '2025-10-25 13:01:54'),
(337, NULL, NULL, 'Login', 'Staff logged in successfully.', 'Success', '2025-10-25 13:03:22'),
(338, 22, NULL, 'Add Inventory', 'Added new medicine: bebebe (200 Capsule)', 'Success', '2025-10-25 13:04:47'),
(339, NULL, NULL, 'Added new patient', 'Patient: Theodore De Villa', 'Success', '2025-10-25 13:05:28'),
(340, 22, NULL, 'Logout', 'User  \'Trishami\' (admin) logged out successfully.', 'Success', '2025-10-25 13:06:50'),
(341, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-25 13:07:27'),
(342, 22, NULL, 'Deleted inventory item', 'User \'Trishami\' deleted medicine: bebebe', 'Success', '2025-10-25 13:07:33'),
(343, 22, NULL, 'Edit Inventory', 'Edited medicine: boompow, added 0 stock', 'Success', '2025-10-25 13:28:28'),
(344, NULL, NULL, 'Added new patient', 'Patient: Theodore De Villa', 'Success', '2025-10-25 13:30:22'),
(345, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-25 13:44:46'),
(346, 22, NULL, 'Logout', 'User  \'Trishami\' (admin) logged out successfully.', 'Success', '2025-10-25 13:46:47'),
(347, NULL, NULL, 'Login', 'Failed login attempt for username/email: admintrisha@gmail.com', 'Failed', '2025-10-25 13:46:56'),
(348, NULL, NULL, 'Login', 'Failed login attempt for username/email: admintrisha@gmail.com', 'Failed', '2025-10-25 13:47:03'),
(349, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-25 13:47:19'),
(350, NULL, NULL, 'Added new patient', 'Patient: Taylor Swift', 'Success', '2025-10-25 14:03:30'),
(351, 21, NULL, 'Login', 'User logged in successfully.', 'Success', '2025-10-25 14:04:42'),
(352, NULL, NULL, 'Edited patient', 'Updated record for patient: Taylor Swift', 'Success', '2025-10-25 14:09:55'),
(353, NULL, 'EMP102', 'Dispense Medicine', 'Dispensed 12 x Amoxicillin 250mg', 'Success', '2025-10-25 14:11:43'),
(354, NULL, 'EMP102', 'Add Medical Record', 'Added new medical record for Patient ID: 7 (Doctor: sasad, Diagnosis: mamamatay na)', 'Success', '2025-10-25 14:13:34'),
(355, NULL, NULL, 'Added new patient', 'Patient: Taylor Swift', 'Success', '2025-10-25 14:13:59'),
(356, NULL, 'EMP102', 'Login', 'Staff logged in successfully.', 'Success', '2025-10-25 14:21:41'),
(357, NULL, 'EMP102', 'Logout', 'User \'taylor swift\' (staff) logged out successfully.', 'Success', '2025-10-25 14:21:49'),
(358, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-25 14:22:31'),
(359, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-10-25 14:23:09'),
(360, NULL, 'EMP102', 'Login', 'Staff logged in successfully.', 'Success', '2025-10-25 14:23:18'),
(361, NULL, NULL, 'Edited patient', 'Updated record for patient: Taylor Swift', 'Success', '2025-10-25 14:24:35'),
(362, NULL, NULL, 'Added new patient', 'Patient: Taylor Swift', 'Success', '2025-10-25 14:24:54'),
(363, NULL, NULL, 'Dispense Medicine', 'Dispense 12 x Amoxicillin 250mg', 'Success', '2025-10-25 14:25:12'),
(364, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-10-25 14:26:49'),
(365, 21, NULL, 'Login', 'User logged in successfully.', 'Success', '2025-10-28 14:37:00'),
(366, 21, NULL, 'Login', 'User logged in successfully.', 'Success', '2025-10-28 14:37:05'),
(367, 21, NULL, 'Login', 'User logged in successfully.', 'Success', '2025-10-28 14:37:05'),
(368, 21, NULL, 'Login', 'User logged in successfully.', 'Success', '2025-10-28 14:37:05'),
(369, 21, NULL, 'Login', 'User logged in successfully.', 'Success', '2025-10-28 14:37:05'),
(370, 21, NULL, 'Login', 'User logged in successfully.', 'Success', '2025-10-28 14:37:05'),
(371, 21, NULL, 'Login', 'User logged in successfully.', 'Success', '2025-10-28 14:37:05'),
(372, 21, NULL, 'Add Inventory', 'Added new medicine: biogesic (121 Capsule)', 'Success', '2025-10-28 14:44:33'),
(373, 21, NULL, 'Dispense Medicine', 'Dispense 21 x biogesic', 'Success', '2025-10-28 14:44:56'),
(374, 21, NULL, 'Logout', 'User \'adminbagongsilang\' (admin) logged out successfully.', 'Success', '2025-10-28 14:46:56'),
(375, NULL, 'EMP102', 'Login', 'Staff logged in successfully.', 'Success', '2025-10-28 14:47:12'),
(376, NULL, 'EMP102', 'Logout', 'User \'taylor swift\' (staff) logged out successfully.', 'Success', '2025-10-28 14:48:07'),
(381, 24, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-29 12:04:06'),
(382, 24, NULL, 'Logout', 'User \'shasha\' (admin) logged out successfully.', 'Success', '2025-10-29 12:04:14'),
(383, NULL, 'EMP103', 'Login', 'Staff logged in successfully.', 'Success', '2025-10-29 12:04:24'),
(384, NULL, 'EMP103', 'Logout', 'User \'shashey\' (staff) logged out successfully.', 'Success', '2025-10-29 12:04:30'),
(385, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-29 12:04:53'),
(386, 22, NULL, 'Edited employee account', 'Updated employee record: Name: shashey', 'Success', '2025-10-29 12:05:39'),
(387, NULL, 'EMP102', 'Login', 'Staff logged in successfully.', 'Success', '2025-10-29 15:26:48'),
(388, NULL, 'EMP102', 'Logout', 'User \'taylor swift\' (staff) logged out successfully.', 'Success', '2025-10-29 15:26:50'),
(389, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-29 15:27:11'),
(390, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-29 16:09:11'),
(391, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-10-29 16:09:23'),
(392, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-10-29 16:09:36'),
(393, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-29 16:11:16'),
(394, NULL, 'EMP102', 'Login', 'Staff logged in successfully.', 'Success', '2025-10-29 16:11:39'),
(395, NULL, NULL, 'Dispense Medicine', 'Dispense 20 x Amoxicillin 250mg', 'Success', '2025-10-29 16:13:14'),
(396, NULL, '0', 'Dispense Medicine', 'Dispensed 1 x Amoxicillin 250mg', 'Success', '2025-10-29 16:16:20'),
(397, NULL, NULL, 'Dispense Medicine', 'Dispense 1 x Amoxicillin 250mg', 'Success', '2025-10-29 16:24:18'),
(398, NULL, '0', 'Dispense Medicine', 'Dispensed 2 x Amoxicillin 250mg', 'Success', '2025-10-29 16:24:54'),
(399, NULL, 'EMP102', 'Add Medical Record', 'Added new medical record for Patient ID: 7 (Doctor: dsds, Diagnosis: mamamatay na)', 'Success', '2025-10-29 16:26:49'),
(400, NULL, NULL, 'Added new patient', 'Patient: selena gomez', 'Success', '2025-10-29 16:27:17'),
(401, NULL, NULL, 'Added new patient', 'Patient: selena gomez', 'Success', '2025-10-29 16:29:56'),
(402, 22, NULL, 'Dispense Medicine', 'Dispensed 2x Amoxicillin 250mg (Batch: BCH-2023-002)', 'Success', '2025-10-29 16:30:28'),
(403, NULL, '0', 'Dispense Medicine', 'Dispensed 2x Amoxicillin 250mg (Batch: BCH-2023-002)', 'Success', '2025-10-29 16:31:17'),
(404, NULL, NULL, 'Added new patient', 'Patient: selena gomez', 'Success', '2025-10-29 16:36:19'),
(405, NULL, NULL, 'Added new patient', 'Patient: selena gomez', 'Success', '2025-10-29 16:37:19'),
(406, NULL, 'EMP102', 'Add Medical Record', 'Added new medical record for Patient ID: 3 (Doctor: qwq, Diagnosis: mamamatay na)', 'Success', '2025-10-29 16:38:12'),
(407, NULL, 'EMP102', 'Dispense Medicine', 'Dispensed 1x Amoxicillin 250mg (Batch: BCH-2023-002) to Patient ID: 44', 'Success', '2025-10-29 16:39:53'),
(408, NULL, NULL, 'Added new patient', 'Patient: selena gomez', 'Success', '2025-10-29 16:41:53'),
(409, NULL, NULL, 'Added new patient', 'Patient: Aziel Flores', 'Success', '2025-10-29 16:43:12'),
(410, NULL, NULL, 'Added new patient', 'Patient: Aziel Flores', 'Success', '2025-10-29 16:44:39'),
(411, NULL, NULL, 'Added new patient', 'Patient: Aziel Flores', 'Success', '2025-10-29 16:46:29'),
(412, NULL, NULL, 'Added new patient', 'Patient: Aziel Flores', 'Success', '2025-10-29 16:47:40'),
(413, NULL, 'EMP102', 'Logout', 'User \'taylor swift\' (staff) logged out successfully.', 'Success', '2025-10-29 20:08:28'),
(414, NULL, 'EMP102', 'Login', 'Staff logged in successfully.', 'Success', '2025-10-29 20:08:37'),
(415, NULL, 'EMP102', 'Added new patient', 'Patient: sasasadee3', 'Success', '2025-10-29 20:08:45'),
(416, 22, NULL, 'Edited Patient', 'Updated record for patient: sasasadee3fdf (Patient ID: 71)', 'Success', '2025-10-29 20:10:04'),
(417, NULL, 'EMP102', 'Edited Patient', 'Updated record for patient: sasasadee3fdffsd (Patient ID: 71)', 'Success', '2025-10-29 20:10:19'),
(418, NULL, 'EMP102', 'Dispense Medicine', 'Dispensed 2x Amoxicillin 250mg (Batch: BCH-2023-002) to Patient ID: 39', 'Success', '2025-10-29 20:11:17'),
(419, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-10-29 20:37:47'),
(420, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-29 20:38:00'),
(421, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-10-29 20:38:08'),
(422, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-30 19:41:54'),
(423, 22, NULL, 'Add Medical Record', 'Added new medical record for Patient ID: 39 (Doctor: sas, Diagnosis: asa)', 'Success', '2025-10-30 19:58:15'),
(424, 22, NULL, 'Add Medical Record', 'Added new medical record for Patient ID: 3 (Doctor: sfsffff, Diagnosis: sfsf)', 'Success', '2025-10-30 19:59:30'),
(425, 22, NULL, 'Add Medical Record', 'Added new medical record for Patient ID: 3 (Doctor: ada, Diagnosis: mamamatay na)', 'Success', '2025-10-30 20:01:15'),
(426, 22, NULL, 'Add Medical Record', 'Added new medical record for Patient ID: 3 (Doctor: sasas, Diagnosis: mamamatay na)', 'Success', '2025-10-30 20:27:38'),
(427, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 45 (Doctor: dada, Diagnosis: sasa)', 'Success', '2025-10-30 20:32:24'),
(428, 22, NULL, 'Dispense Medicine', 'Dispensed 20 x Paracetamol to Patient ID: 3 (Record ID: 34)', 'Success', '2025-10-30 20:39:44'),
(429, 22, NULL, 'Dispense Medicine', 'Dispensed 10 x Multivitamins to Patient ID: 3 (Record ID: 34)', 'Success', '2025-10-30 20:39:44'),
(430, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 3 (Doctor: dada, Diagnosis: dada)', 'Success', '2025-10-30 20:39:44'),
(431, 22, NULL, 'Dispense Medicine', 'Dispensed 99 x biogesic to Patient ID: 3 (Record ID: 35)', 'Success', '2025-10-30 20:41:24'),
(432, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 3 (Doctor: dada, Diagnosis: asasa)', 'Success', '2025-10-30 20:41:24'),
(433, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 3 (Doctor: nuiser, Diagnosis: asasa)', 'Success', '2025-10-30 20:44:42'),
(434, 22, NULL, 'Dispense Medicine', 'Dispensed 10 x Salbutamol Syrup to Patient ID: 3 (Record ID: 37)', 'Success', '2025-10-30 20:47:04'),
(435, 22, NULL, 'Dispense Medicine', 'Dispensed 90 x Multivitamins to Patient ID: 3 (Record ID: 37)', 'Success', '2025-10-30 20:47:04'),
(436, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 3 (Doctor: sfsf, Diagnosis: dasf)', 'Success', '2025-10-30 20:47:04'),
(437, 22, NULL, 'Dispense Medicine', 'Dispensed 10 x Salbutamol Syrup to Patient ID: 3 (Record ID: 38)', 'Success', '2025-10-30 21:09:01'),
(438, 22, NULL, 'Dispense Medicine', 'Dispensed 100 x Multivitamins to Patient ID: 3 (Record ID: 38)', 'Success', '2025-10-30 21:09:01'),
(439, 22, NULL, 'Dispense Medicine', 'Dispensed 20 x ORS (Oral Rehydration Salts) to Patient ID: 3 (Record ID: 38)', 'Success', '2025-10-30 21:09:01'),
(440, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 3 (Doctor: dada, Diagnosis: dadad)', 'Success', '2025-10-30 21:09:01'),
(441, 22, NULL, 'Dispense Medicine', 'Dispensed 21x ORS (Oral Rehydration Salts) to Patient ID: 3 (Record ID: 39)', 'Success', '2025-10-30 21:24:42'),
(442, 22, NULL, 'Dispense Medicine', 'Dispensed 50x Paracetamol 500mg to Patient ID: 3 (Record ID: 39)', 'Success', '2025-10-30 21:24:42'),
(443, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 3 (Doctor: rer, Diagnosis: re)', 'Success', '2025-10-30 21:24:42'),
(444, 22, NULL, 'Dispense Medicine', 'Dispensed 100x Paracetamol 500mg to Patient ID: 3 (Record ID: 40)', 'Success', '2025-10-30 21:49:30'),
(445, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 3 (Doctor: dsad, Diagnosis: dsada)', 'Success', '2025-10-30 21:49:30'),
(446, NULL, 'EMP102', 'Login', 'Staff logged in successfully.', 'Success', '2025-10-30 21:50:20'),
(447, NULL, 'EMP102', 'Dispense Medicine', 'Dispensed 20x Paracetamol to Patient ID: 3 (Record ID: 41)', 'Success', '2025-10-30 21:50:46'),
(448, NULL, 'EMP102', 'Dispense Medicine', 'Dispensed 20x hehea to Patient ID: 3 (Record ID: 41)', 'Success', '2025-10-30 21:50:46'),
(449, NULL, 'EMP102', 'Add Medical Record', 'Added medical record for Patient ID: 3 (Doctor: sfdsafds, Diagnosis: sasa)', 'Success', '2025-10-30 21:50:46'),
(450, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-10-30 21:52:26'),
(451, NULL, 'EMP102', 'Login', 'Staff logged in successfully.', 'Success', '2025-10-30 21:57:57'),
(452, NULL, 'EMP102', 'Logout', 'User \'taylor swift\' (staff) logged out successfully.', 'Success', '2025-10-30 21:59:13'),
(453, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-31 10:14:03'),
(454, 22, NULL, 'Dispense Medicine', 'Dispensed 20x Paracetamol 500mg to Patient ID: 39 (Record ID: 42)', 'Success', '2025-10-31 10:15:12'),
(455, 22, NULL, 'Dispense Medicine', 'Dispensed 60x Paracetamol to Patient ID: 39 (Record ID: 42)', 'Success', '2025-10-31 10:15:12'),
(456, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 39 (Doctor: sasas, Diagnosis: mamamatay na)', 'Success', '2025-10-31 10:15:12'),
(457, 22, NULL, 'Added new patient', 'Patient: Hatdog Santos', 'Success', '2025-10-31 10:19:34'),
(458, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-31 10:29:33'),
(459, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-31 10:29:39'),
(460, 22, NULL, 'Dispense Medicine', 'Dispensed 20x Multivitamins to Patient ID: 72 (Record ID: 43)', 'Success', '2025-10-31 10:30:33'),
(461, 22, NULL, 'Dispense Medicine', 'Dispensed 20x Paracetamol 500mg to Patient ID: 72 (Record ID: 43)', 'Success', '2025-10-31 10:30:33'),
(462, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 72 (Doctor: dadad, Diagnosis: sasa)', 'Success', '2025-10-31 10:30:33'),
(463, 22, NULL, 'Edit Inventory', 'Edited medicine: biogesic, added 5 stock', 'Success', '2025-10-31 10:32:04'),
(464, 22, NULL, 'Edited employee account', 'Updated employee record: Name: trisha', 'Success', '2025-10-31 10:33:07'),
(465, 22, NULL, 'Deleted admin account', 'Removed admin: Admin1 (admin@example.com)', 'Success', '2025-10-31 10:58:32'),
(466, 22, NULL, 'Edit Admin', 'Updated admin account (ID: 21, Email: admin@bagongsilang.gov.ph)', 'Success', '2025-10-31 11:21:38'),
(467, 22, NULL, 'Edit Admin', 'Updated admin account (ID: 21, Email: admin@bagongsilang.gov.ph)', 'Success', '2025-10-31 11:27:45'),
(468, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-10-31 12:22:47'),
(469, 22, NULL, 'Edit Admin', 'Updated admin account (ID: 21, Email: admin@bagongsilang.gov.ph)', 'Success', '2025-10-31 12:26:57'),
(470, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-02 14:40:04'),
(471, 22, NULL, 'Create Employee', 'Added new employee (ID: EMP20256460JM, Email: rhitzeunice@gmail.com)', 'Success', '2025-11-02 14:48:11'),
(472, 22, NULL, 'Edited Patient', 'Updated record for patient: Hatdog Santos (Patient ID: 72)', 'Success', '2025-11-02 14:51:13'),
(473, 22, NULL, 'Added new patient', 'Patient: rhitz', 'Success', '2025-11-02 14:51:43'),
(474, 22, NULL, 'Dispense Medicine', 'Dispensed 10x Paracetamol to Patient ID: 73 (Record ID: 44)', 'Success', '2025-11-02 14:52:28'),
(475, 22, NULL, 'Dispense Medicine', 'Dispensed 9x ORS (Oral Rehydration Salts) to Patient ID: 73 (Record ID: 44)', 'Success', '2025-11-02 14:52:28'),
(476, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 73 (Doctor: Doc Mayki, Diagnosis: mamamatay na)', 'Success', '2025-11-02 14:52:28'),
(477, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-02 14:56:16'),
(478, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-02 14:56:21'),
(479, 22, NULL, 'Added new patient', 'Patient: mommy dionisia', 'Success', '2025-11-02 14:57:22'),
(480, NULL, 'EMP102', 'Login', 'Staff logged in successfully.', 'Success', '2025-11-02 14:58:00'),
(481, NULL, 'EMP102', 'Added new patient', 'Patient: sasasadeewaka', 'Success', '2025-11-02 14:58:19'),
(482, NULL, 'EMP102', 'Dispense Medicine', 'Dispensed 2x heghe to Patient ID: 75 (Record ID: 45)', 'Success', '2025-11-02 14:59:00'),
(483, NULL, 'EMP102', 'Dispense Medicine', 'Dispensed 100x boompow to Patient ID: 75 (Record ID: 45)', 'Success', '2025-11-02 14:59:00'),
(484, NULL, 'EMP102', 'Add Medical Record', 'Added medical record for Patient ID: 75 (Doctor: nyehe, Diagnosis: sasa)', 'Success', '2025-11-02 14:59:00'),
(485, NULL, 'EMP102', 'Logout', 'User \'taylor swift\' (staff) logged out successfully.', 'Success', '2025-11-02 15:05:20'),
(486, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-02 15:05:33'),
(487, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-02 15:05:34'),
(488, 22, NULL, 'Edited employee account', 'Updated employee record: Name: rhitz centenoooooo', 'Success', '2025-11-02 15:16:20'),
(489, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-11-02 15:26:38'),
(490, NULL, 'EMP20256460JM', 'Login', 'Staff logged in successfully.', 'Success', '2025-11-02 15:26:52'),
(491, NULL, 'EMP20256460JM', 'Added new patient', 'Patient: ma dongseok', 'Success', '2025-11-02 15:27:30'),
(492, NULL, 'EMP20256460JM', 'Dispense Medicine', 'Dispensed 10x Paracetamol to Patient ID: 76 (Record ID: 46)', 'Success', '2025-11-02 15:27:59'),
(493, NULL, 'EMP20256460JM', 'Dispense Medicine', 'Dispensed 10x Paracetamol 500mg to Patient ID: 76 (Record ID: 46)', 'Success', '2025-11-02 15:27:59'),
(494, NULL, 'EMP20256460JM', 'Add Medical Record', 'Added medical record for Patient ID: 76 (Doctor: doc bang, Diagnosis: sasa)', 'Success', '2025-11-02 15:27:59'),
(495, NULL, 'EMP20256460JM', 'Logout', 'User \'rhitz centenoooooo\' (staff) logged out successfully.', 'Success', '2025-11-02 15:39:03'),
(496, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-02 15:39:23'),
(497, 22, NULL, 'Add Inventory', 'Added new medicine: lomotil (200 Tablet)', 'Success', '2025-11-02 15:44:23'),
(498, 22, NULL, 'Add Inventory', 'Added new medicine: dada (800 Tablet)', 'Success', '2025-11-02 15:45:29'),
(499, 22, NULL, 'Add Inventory', 'Added new medicine: bombep (20 Capsule)', 'Success', '2025-11-02 15:47:12'),
(500, 22, NULL, 'Create Employee', 'Added new employee (ID: EMP20252023RO, Email: tayas@gmail.com)', 'Success', '2025-11-02 15:47:35'),
(501, 22, NULL, 'Create Employee', 'Added new employee (ID: EMP20254038AP, Email: santostrish@yahoo.com)', 'Success', '2025-11-02 15:50:04'),
(502, 22, NULL, 'Create Employee', 'Added new employee (ID: EMP20251009JC, Email: santostrishae@yahoo.com)', 'Success', '2025-11-02 15:50:15'),
(503, 22, NULL, 'Deleted employee account', 'Removed employee record: Name: TRISHA MAE SANTOS ', 'Success', '2025-11-02 15:50:18'),
(504, 22, NULL, 'Create Employee', 'Added new employee (ID: EMP20250620SE, Email: adminaaa@gmail.com)', 'Success', '2025-11-02 15:51:15'),
(505, 22, NULL, 'Create Employee', 'Added new employee (ID: EMP20251226JY, Email: tay@gmail.com)', 'Success', '2025-11-02 15:51:56'),
(506, 22, NULL, 'Deleted employee account', 'Removed employee record: Name: TRISHA MAE SANTOS ', 'Success', '2025-11-02 15:52:00'),
(507, 22, NULL, 'Create Employee', 'Added new employee (ID: EMP20255676DJ, Email: dhs@gmail.com)', 'Success', '2025-11-02 15:52:32'),
(508, 22, NULL, 'Edited employee account', 'Updated employee record: Name: shah', 'Success', '2025-11-02 15:52:46'),
(509, 22, NULL, 'Dispense Medicine', 'Dispensed 5x biogesic (Batch: BATCH-2025-001) to Patient ID: 39', 'Success', '2025-11-02 15:53:15'),
(510, 22, NULL, 'Dispense Medicine', 'Dispensed 1x biogesic (Batch: BATCH-2025-001) to Patient ID: 44', 'Success', '2025-11-02 15:55:13'),
(511, 22, NULL, 'Added new patient', 'Patient: briella', 'Success', '2025-11-02 15:57:15'),
(512, 22, NULL, 'Dispense Medicine', 'Dispensed 10x hhj to Patient ID: 39 (Record ID: 47)', 'Success', '2025-11-02 15:57:49'),
(513, 22, NULL, 'Dispense Medicine', 'Dispensed 10x junjun to Patient ID: 39 (Record ID: 47)', 'Success', '2025-11-02 15:57:49'),
(514, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 39 (Doctor: thess, Diagnosis: mamamatay na)', 'Success', '2025-11-02 15:57:49'),
(515, 22, NULL, 'Dispense Medicine', 'Dispensed 200x dada to Patient ID: 3 (Record ID: 48)', 'Success', '2025-11-02 15:59:44'),
(516, 22, NULL, 'Dispense Medicine', 'Dispensed 10x bombep to Patient ID: 3 (Record ID: 48)', 'Success', '2025-11-02 15:59:44'),
(517, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 3 (Doctor: e54df, Diagnosis: sasa)', 'Success', '2025-11-02 15:59:44'),
(518, 22, NULL, 'Deleted inventory item', 'User \'Trishami\' deleted medicine: biogesic', 'Success', '2025-11-02 16:15:20'),
(519, 22, NULL, 'Edit Inventory', 'Edited medicine: bombepi, added 0 stock', 'Success', '2025-11-02 16:35:48'),
(520, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-02 21:43:43'),
(521, 22, NULL, 'Edit Admin', 'Updated admin account (ID: 23, Email: adminmariel@gmail.com)', 'Success', '2025-11-02 21:45:05'),
(522, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-11-02 21:45:26'),
(523, NULL, 'EMP102', 'Login', 'Staff logged in successfully.', 'Success', '2025-11-03 11:14:29'),
(524, NULL, 'EMP102', 'Logout', 'User \'taylor swift\' (staff) logged out successfully.', 'Success', '2025-11-03 11:19:07'),
(525, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-03 11:19:20'),
(526, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-03 11:29:22'),
(527, NULL, NULL, 'Login', 'Failed login: Username/Email not found: trishamae', 'Failed', '2025-11-03 14:32:49'),
(528, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-03 14:33:01'),
(529, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-03 14:33:44'),
(530, 22, NULL, 'Add Medical Record', 'Failed to add record for Patient ID: 70 (Not enough stock for hhj (Available: 2))', 'Failed', '2025-11-03 15:03:23'),
(531, 22, NULL, 'Dispense Medicine', 'Dispensed 2x hhj to Patient ID: 70 (Record ID: 50)', 'Success', '2025-11-03 15:03:30'),
(532, 22, NULL, 'Dispense Medicine', 'Dispensed 5x lomotil to Patient ID: 70 (Record ID: 50)', 'Success', '2025-11-03 15:03:30'),
(533, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 70 (Doctor: mamamo, Diagnosis: dsadsa)', 'Success', '2025-11-03 15:03:30'),
(534, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-03 16:07:43'),
(535, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-03 16:07:48'),
(536, 22, NULL, 'Add Inventory', 'Added new medicine: hsahshashahsa (500 Tablet)', 'Success', '2025-11-03 16:14:15'),
(537, 22, NULL, 'Dispense Medicine', 'Dispensed 100x hsahshashahsa to Patient ID: 39 (Record ID: 51)', 'Success', '2025-11-03 16:22:44'),
(538, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 39 (Doctor: dsa, Diagnosis: mamamatay na)', 'Success', '2025-11-03 16:22:44'),
(539, 22, NULL, 'Add Inventory', 'Added new medicine: hatdogcheesedog (200 Tablet)', 'Success', '2025-11-03 16:24:21'),
(540, 22, NULL, 'Dispense Medicine', 'Dispensed 50x hatdogcheesedog to Patient ID: 39 (Record ID: 52)', 'Success', '2025-11-03 16:24:53'),
(541, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 39 (Doctor: sas, Diagnosis: sas)', 'Success', '2025-11-03 16:24:53'),
(542, 22, NULL, 'Dispense Medicine', 'Dispensed 50x hatdogcheesedog to Patient ID: 67 (Record ID: 53)', 'Success', '2025-11-03 16:26:13'),
(543, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 67 (Doctor: fdfdf, Diagnosis: sdfd)', 'Success', '2025-11-03 16:26:13'),
(544, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-11-03 16:59:30'),
(545, NULL, 'EMP102', 'Login', 'Staff logged in successfully.', 'Success', '2025-11-03 16:59:40'),
(546, NULL, 'EMP102', 'Logout', 'User \'taylor swift\' (staff) logged out successfully.', 'Success', '2025-11-03 16:59:46'),
(547, NULL, NULL, 'Login', 'Failed login: Incorrect password for username/email: trishami', 'Failed', '2025-11-03 17:14:36'),
(548, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-03 17:14:46'),
(549, NULL, 'EMP20256460JM', 'Login', 'Staff logged in successfully.', 'Success', '2025-11-03 17:15:02'),
(550, NULL, 'EMP20256460JM', 'Logout', 'User \'rhitz centenoooooo\' (staff) logged out successfully.', 'Success', '2025-11-03 17:15:34'),
(551, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-03 19:55:19'),
(552, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-03 19:56:00'),
(553, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-11-03 20:16:15'),
(554, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-04 19:43:38'),
(555, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-11-04 19:57:12'),
(556, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-04 20:03:00'),
(557, 22, NULL, 'Add Inventory', 'Added new medicine: hoaysa (12 Tablet)', 'Success', '2025-11-04 20:29:05'),
(558, 22, NULL, 'Deleted inventory item', 'User \'Trishami\' deleted medicine: hoaysa', 'Success', '2025-11-04 20:29:26'),
(559, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-05 20:32:20'),
(560, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-05 20:32:39'),
(561, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-05 20:32:39'),
(562, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-05 20:32:39'),
(563, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-05 20:32:46'),
(564, 22, NULL, 'Deleted inventory item', 'User \'Trishami\' deleted medicine: bombepi', 'Success', '2025-11-05 20:36:19'),
(565, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-11-05 20:37:44'),
(566, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-11-05 20:37:44'),
(567, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-06 09:06:57'),
(568, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-06 09:07:14'),
(569, 22, NULL, 'Edited employee account', 'Updated employee record: Name: TRISHA MAE SANTOSsss', 'Success', '2025-11-06 09:30:36'),
(570, 22, NULL, 'Deleted employee account', 'Removed employee record: Name: shah ', 'Success', '2025-11-06 09:31:12'),
(571, 22, NULL, 'Create Admin', 'Created new admin account (Email: shamae@gmail.com, Username: shame)', 'Success', '2025-11-06 09:31:36'),
(572, 22, NULL, 'Create Employee', 'Added new employee (ID: EMP20250216GD, Email: jaja@gmail.com)', 'Success', '2025-11-06 09:43:48'),
(573, 22, NULL, 'Create Employee', 'Added new employee (Name: jajajajajaja)', 'Success', '2025-11-06 09:44:47'),
(574, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-11-06 09:47:10'),
(575, NULL, 'EMP20250216GD', 'Login', 'Staff logged in successfully.', 'Success', '2025-11-06 09:47:20'),
(576, NULL, 'EMP20250216GD', 'Edited Patient', 'Updated record for patient: briella jazz (Patient ID: 77)', 'Success', '2025-11-06 09:48:00'),
(577, NULL, 'EMP20250216GD', 'Logout', 'User \'jajajaj\' (staff) logged out successfully.', 'Success', '2025-11-06 09:49:40'),
(578, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-06 09:49:51'),
(579, 22, NULL, 'Create Employee', 'Added new employee (Name: aziel flores)', 'Success', '2025-11-06 09:50:19'),
(580, 22, NULL, 'Delete Admin Account', 'Deleted admin: sha (shamae@gmail.com) - Username: shame', 'Success', '2025-11-06 09:58:05'),
(581, 22, NULL, 'Deleted employee account', 'Removed employee record: Name: aziel flores ', 'Success', '2025-11-06 09:58:25'),
(582, 22, NULL, 'Deleted employee account', 'Removed employee record: Name: theo hehe ', 'Success', '2025-11-06 09:58:54'),
(583, 22, NULL, 'Delete Admin Account', 'Deleted admin: Mariel T. Ilao (adminmariel@gmail.com) - Username: adminmariely', 'Success', '2025-11-06 10:12:02'),
(584, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-06 10:12:24'),
(585, 22, NULL, 'Create Admin', 'Created new admin account (Email: jajaramos@gmail.com, Username: jajabels)', 'Success', '2025-11-06 10:33:35'),
(586, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-11-06 10:33:48'),
(587, 26, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-06 10:34:07'),
(588, 26, NULL, 'Create Employee', 'Added new employee (Name: tj monterde)', 'Success', '2025-11-06 10:38:31'),
(589, 26, NULL, 'Deleted employee account', 'Removed employee record: Name: tj monterde ', 'Success', '2025-11-06 10:43:25'),
(590, 26, NULL, 'Deleted employee account', 'Removed employee record: Name: jajajajajaja ', 'Success', '2025-11-06 10:55:39'),
(591, 26, NULL, 'Deleted employee account', 'Removed employee record: Name: jajajaj ', 'Success', '2025-11-06 10:55:45'),
(592, 26, NULL, 'Deleted employee account', 'Removed employee record: Name: reyyyy ', 'Success', '2025-11-06 10:55:48'),
(593, 26, NULL, 'Create Employee', 'Added new employee (Name: Theo Devilla)', 'Success', '2025-11-06 10:56:12'),
(594, 26, NULL, 'Deleted inventory item', 'User \'jajabels\' deleted medicine: hatdogcheesedog', 'Success', '2025-11-06 10:57:08'),
(595, 26, NULL, 'Deleted inventory item', 'User \'jajabels\' deleted medicine: hsahshashahsa', 'Success', '2025-11-06 10:57:12'),
(596, 26, NULL, 'Deleted inventory item', 'User \'jajabels\' deleted medicine: Amoxicillin', 'Success', '2025-11-06 10:57:20'),
(597, NULL, 'EMP20259478PT', 'Login', 'Staff logged in successfully.', 'Success', '2025-11-06 12:24:36'),
(598, NULL, 'EMP20259478PT', 'Logout', 'User \'Trisha Mae SAntos\' (staff) logged out successfully.', 'Success', '2025-11-06 12:25:00'),
(599, NULL, NULL, 'Login', 'Failed login: Incorrect password for username/email: trishami', 'Failed', '2025-11-06 12:25:09'),
(600, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-06 12:25:20'),
(601, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-06 12:25:30'),
(602, 22, NULL, 'Add Inventory', 'Added new medicine: gamot (123 Capsule)', 'Success', '2025-11-06 12:50:43'),
(603, 22, NULL, 'Add Inventory', 'Added new medicine: gamot (123 Capsule)', 'Success', '2025-11-06 12:50:58'),
(604, 22, NULL, 'Add Inventory', 'Added new medicine: bioflu (5 Capsule)', 'Success', '2025-11-06 13:32:29'),
(605, 22, NULL, 'Edit Inventory', 'Edited medicine: bioflu, added 0 stock', 'Success', '2025-11-06 13:32:44'),
(606, 22, NULL, 'Add Inventory', 'Added new medicine: boompowch (123 Tablet)', 'Success', '2025-11-06 13:32:59'),
(607, 22, NULL, 'Add Inventory', 'Added new medicine: boompowsasa (123 Capsule)', 'Success', '2025-11-06 13:35:34'),
(608, 22, NULL, 'Dispense Medicine', 'Dispensed 2x junjun to Patient ID: 36 (Record ID: 54)', 'Success', '2025-11-06 13:38:04'),
(609, 22, NULL, 'Dispense Medicine', 'Dispensed 5x lomotil to Patient ID: 36 (Record ID: 54)', 'Success', '2025-11-06 13:38:04'),
(610, 22, NULL, 'Add Medical Record', 'Added medical record for Patient ID: 36 (Doctor: mamiii, Diagnosis: sasas)', 'Success', '2025-11-06 13:38:04'),
(611, 22, NULL, 'Add Inventory', 'Added new medicine: what (122 Tablet)', 'Success', '2025-11-06 13:54:10'),
(612, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-06 16:48:48'),
(613, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-06 16:49:04'),
(614, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-06 16:49:04'),
(615, NULL, 'EMP20259478PT', 'Login', 'Staff logged in successfully.', 'Success', '2025-11-06 16:50:50'),
(616, NULL, 'EMP20259478PT', 'Edited Patient', 'Updated record for patient: sasasadeewakaWEWEW (Patient ID: 75)', 'Success', '2025-11-06 16:51:08'),
(617, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-06 16:58:00'),
(618, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-11-06 17:12:35'),
(619, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-07 17:40:10'),
(620, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-07 17:40:30'),
(621, 22, NULL, 'Add Inventory', 'Added new medicine: amoooosas (520 Capsule)', 'Success', '2025-11-07 17:41:12'),
(622, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-11-07 17:41:59'),
(623, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-07 17:58:43'),
(624, 22, NULL, 'Deleted employee account', 'Removed employee record: Name: reyyyy ', 'Success', '2025-11-07 17:59:04'),
(625, 22, NULL, 'Added new patient', 'Patient: angelcia', 'Success', '2025-11-07 18:07:09'),
(626, 22, NULL, 'Edited Patient', 'Updated record for patient: angelcia (Patient ID: 78)', 'Success', '2025-11-07 18:09:46'),
(627, 22, NULL, 'Added new patient', 'Patient: rhitz', 'Success', '2025-11-07 18:10:17'),
(628, 22, NULL, 'Deleted Patient', 'Removed patient record: selena gomez (Patient ID: 65)', 'Success', '2025-11-07 18:11:02'),
(629, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-07 18:27:03'),
(630, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-08 11:00:32'),
(631, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-08 11:00:58'),
(632, NULL, 'EMP20259478PT', 'Login', 'Staff logged in successfully.', 'Success', '2025-11-08 11:02:20'),
(633, NULL, 'EMP20259478PT', 'Edited Patient', 'Updated record for patient: rhitzy (Patient ID: 79)', 'Success', '2025-11-08 11:03:45'),
(634, 22, NULL, 'Added new patient', 'Patient: aziel bugok', 'Success', '2025-11-08 11:06:25'),
(635, 22, NULL, 'Create Employee', 'Added new employee (Name: aziel)', 'Success', '2025-11-08 11:10:51'),
(636, 22, NULL, 'Logout', 'User \'Trishami\' (admin) logged out successfully.', 'Success', '2025-11-08 11:10:59'),
(637, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-08 14:06:37'),
(638, 22, NULL, 'Edit Inventory', 'Edited medicine: Amoxicillin C., added 150 stock', 'Success', '2025-11-08 14:11:28'),
(639, 22, NULL, 'Edit Inventory', 'Edited medicine: Amoxicillin Syrup, added 150 stock', 'Success', '2025-11-08 14:11:52'),
(640, 22, NULL, 'Edit Inventory', 'Edited medicine: Cotrimoxazole Tablet, added 150 stock', 'Success', '2025-11-08 14:13:41'),
(641, 22, NULL, 'Edit Inventory', 'Edited medicine: Amoxicillin Capsule, added 0 stock', 'Success', '2025-11-08 14:17:25'),
(642, 22, NULL, 'Edit Inventory', 'Edited medicine: Ciprofloxacin, added 150 stock', 'Success', '2025-11-08 14:18:01'),
(643, 22, NULL, 'Edit Inventory', 'Edited medicine: Paracetamol Drop, added 150 stock', 'Success', '2025-11-08 14:18:38'),
(644, 22, NULL, 'Edit Inventory', 'Edited medicine: Cotrimoxazole Tablet, added 0 stock', 'Success', '2025-11-08 14:18:51'),
(645, 22, NULL, 'Edit Inventory', 'Edited medicine: Amoxicillin Syrup, added 0 stock', 'Success', '2025-11-08 14:18:59'),
(646, 22, NULL, 'Edit Inventory', 'Edited medicine: Ascorbic Acid Vitamin C Tablet, added 150 stock', 'Success', '2025-11-08 14:21:09'),
(647, 22, NULL, 'Edit Inventory', 'Edited medicine: Ascorbic Acid Vitamin C Drop, added 150 stock', 'Success', '2025-11-08 14:21:37'),
(648, 22, NULL, 'Edit Inventory', 'Edited medicine: Vitamins B Complex, added 150 stock', 'Success', '2025-11-08 14:33:50'),
(649, 22, NULL, 'Edit Inventory', 'Edited medicine: Ferrous Sulfate(Iron Supplement), added 150 stock', 'Success', '2025-11-08 14:34:48'),
(650, 22, NULL, 'Edit Inventory', 'Edited medicine: Betadine, added 0 stock', 'Success', '2025-11-08 14:35:21'),
(651, 22, NULL, 'Edit Inventory', 'Edited medicine: Ethyl Alcohol 70%, added 0 stock', 'Success', '2025-11-08 14:36:05'),
(652, 22, NULL, 'Edit Inventory', 'Edited medicine: BCG, added 0 stock', 'Success', '2025-11-08 14:39:10'),
(653, 22, NULL, 'Edit Inventory', 'Edited medicine: BCG, added 0 stock', 'Success', '2025-11-08 14:40:48'),
(654, 22, NULL, 'Edit Inventory', 'Edited medicine: BCG, added 0 stock', 'Success', '2025-11-08 14:41:16'),
(655, 22, NULL, 'Edit Inventory', 'Edited medicine: Measles, added 0 stock', 'Success', '2025-11-08 14:42:07'),
(656, 22, NULL, 'Edit Inventory', 'Edited medicine: Polio, added 0 stock', 'Success', '2025-11-08 14:42:35'),
(657, 22, NULL, 'Edit Inventory', 'Edited medicine: Flu, added 55 stock', 'Success', '2025-11-08 14:43:10'),
(658, 22, NULL, 'Edit Inventory', 'Edited medicine: Hepatitis B, added 150 stock', 'Success', '2025-11-08 14:43:40'),
(659, 22, NULL, 'Deleted inventory item', 'User \'Trishami\' deleted medicine: boompowsasa', 'Success', '2025-11-08 14:43:44'),
(660, 22, NULL, 'Deleted inventory item', 'User \'Trishami\' deleted medicine: amoooosas', 'Success', '2025-11-08 14:43:47'),
(661, 22, NULL, 'Deleted inventory item', 'User \'Trishami\' deleted medicine: what', 'Success', '2025-11-08 14:43:50'),
(662, 22, NULL, 'Login', 'Admin logged in successfully.', 'Success', '2025-11-08 18:53:52'),
(663, 22, NULL, 'Dispense Medicine', 'Dispensed 10x BCG (Batch: BATCH005) to Patient ID: 3', 'Success', '2025-11-08 19:03:49'),
(664, 22, NULL, 'Dispense Medicine', 'Dispensed 90x BCG (Batch: BATCH005) to Patient ID: 3', 'Success', '2025-11-08 19:15:51'),
(665, 22, NULL, 'Dispense Medicine', 'Dispensed 50x BCG (Batch: BATCH005) to Patient ID: 3', 'Success', '2025-11-08 19:19:33'),
(666, 22, NULL, 'Dispense Medicine', 'Dispensed 10x BCG (Batch: BATCH005) to Patient ID: 78', 'Success', '2025-11-08 19:19:48'),
(667, 22, NULL, 'Dispense Medicine', 'Dispensed 30x BCG (Batch: BATCH005) to Patient ID: 39', 'Success', '2025-11-08 19:19:59'),
(668, 22, NULL, 'Dispense Medicine', 'Dispensed 10x BCG (Batch: BATCH005) to Patient ID: 70', 'Success', '2025-11-08 19:20:09'),
(669, 22, NULL, 'Dispense Medicine', 'Dispensed 12x Amoxicillin Capsule (Batch: BATCH005) to Patient ID: 78', 'Success', '2025-11-08 19:27:02'),
(670, 22, NULL, 'Edit Inventory', 'Edited medicine: Amoxicillin 250mg, added 100 stock', 'Success', '2025-11-08 19:28:57'),
(671, 22, NULL, 'Dispense Medicine', 'Dispensed 10x Amoxicillin 250mg (Batch: BCH-2023-002) to Patient ID: 3', 'Success', '2025-11-08 19:29:29'),
(672, 22, NULL, 'Dispense Medicine', 'Dispensed 14x Amoxicillin Syrup (Batch: BATCH005) to Patient ID: 3', 'Success', '2025-11-08 19:31:09'),
(673, 22, NULL, 'Dispense Medicine', 'Dispensed 10x Amoxicillin Syrup (Batch: BATCH005) to Patient ID: 3', 'Success', '2025-11-08 19:32:56'),
(674, 22, NULL, 'Dispense Medicine', 'Dispensed 8x Ascorbic Acid Vitamin C Drop (Batch: BATCH005) to Patient ID: 3', 'Success', '2025-11-08 19:44:59');

-- --------------------------------------------------------

--
-- Table structure for table `employee_accounts`
--

CREATE TABLE `employee_accounts` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `fullname` varchar(150) NOT NULL,
  `position` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `password` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_id_viewed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_accounts`
--

INSERT INTO `employee_accounts` (`id`, `employee_id`, `fullname`, `position`, `email`, `role`, `status`, `password`, `last_login`, `created_at`, `is_id_viewed`) VALUES
(2, 'emp002', 'rhitz', 'nurse', 'nonit@asamsa', 'staff', 'active', NULL, NULL, '2025-10-01 10:35:51', 1),
(3, 'emp003', 'trisha', 'nurse', 'sza@gmail.com', 'staff', 'active', NULL, NULL, '2025-10-01 10:51:48', 1),
(4, 'emp004', 'sha', 'bhw', 'shasha@hehe', 'staff', 'active', NULL, NULL, '2025-10-01 11:03:55', 1),
(5, 'emp005', 'boom', 'nurse', 'panot@hehe', 'staff', 'active', NULL, NULL, '2025-10-01 11:05:13', 1),
(6, 'emp008', 'girlie', 'bhw', 'one@gmail', 'staff', 'active', NULL, NULL, '2025-10-01 11:27:16', 1),
(16, 'EMP100', 'trisha mae santos', 'BHW', 'mami@gmail.com', 'staff', 'active', NULL, NULL, '2025-10-04 13:29:40', 1),
(28, 'EMP101', 'trisha mae santossss', 'BHW', 'santostrishamae@yahoo.com', 'staff', 'active', NULL, NULL, '2025-10-24 07:55:31', 1),
(29, 'EMP102', 'taylor swift', 'BHW', 'taylorswift@gmail.com', 'staff', 'active', NULL, NULL, '2025-10-25 04:23:16', 1),
(30, 'EMP103', 'shashey', 'BHW', 'shasheyy@gmail.com', 'staff', 'active', NULL, NULL, '2025-10-29 04:03:33', 1),
(31, 'EMP20256460JM', 'rhitz centenoooooo', 'nurse', 'rhitzeunice@gmail.com', 'staff', 'active', NULL, NULL, '2025-11-02 06:48:11', 1),
(32, 'EMP20252023RO', 'Mariel Ilao', 'nurse', 'tayas@gmail.com', 'staff', 'active', NULL, NULL, '2025-11-02 07:47:35', 1),
(33, 'EMP20254038AP', 'TRISHA MAE SANTOS', 'Barangay Secretary', 'santostrish@yahoo.com', 'staff', 'active', NULL, NULL, '2025-11-02 07:50:04', 1),
(35, 'EMP20250620SE', 'rhitz', 'Barangay Secretary', 'adminaaa@gmail.com', 'staff', 'active', NULL, NULL, '2025-11-02 07:51:15', 1),
(38, 'EMP20252623YV', 'Trisha Mae SAntos', 'nurse', 'trish@gmail.com', 'staff', 'active', NULL, NULL, '2025-11-06 01:26:02', 1),
(39, 'EMP20259478PT', 'Trisha Mae SAntos', 'nurse', 'trishaaa@gmail.com', 'staff', 'active', NULL, NULL, '2025-11-06 01:29:03', 1),
(40, 'EMP20257919VD', 'TRISHA MAE SANTOSsss', 'nurse', 'tayasi@gmail.com', 'staff', 'active', NULL, NULL, '2025-11-06 01:30:11', 1),
(41, 'EMP20256861PN', 'rhitz', 'bhw', 'rhitzzz@gmail.com', 'staff', 'active', NULL, NULL, '2025-11-06 01:32:15', 1),
(42, 'EMP20250126JE', 'reycel villarisco', 'bhw', 'rey@gmail.com', 'staff', 'active', NULL, NULL, '2025-11-06 01:34:27', 1),
(50, 'EMP20258710RA', 'Theo Devilla', 'Barangay Secretary', 'theo@gmail.com', 'staff', 'active', NULL, NULL, '2025-11-06 02:56:12', 1),
(51, 'EMP20251247ZY', 'aziel', 'ho', 'aziel@gmail.com', 'staff', 'active', NULL, NULL, '2025-11-08 03:10:51', 1);

-- --------------------------------------------------------

--
-- Table structure for table `external_events`
--

CREATE TABLE `external_events` (
  `event_date` date NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `is_national_holiday` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `record_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor` varchar(100) DEFAULT NULL,
  `date` date NOT NULL,
  `complaint` varchar(255) NOT NULL,
  `diagnosis` varchar(255) DEFAULT NULL,
  `treatment` varchar(255) DEFAULT NULL,
  `medication` varchar(255) DEFAULT NULL,
  `bp` varchar(20) DEFAULT NULL,
  `temperature` varchar(20) DEFAULT NULL,
  `weight` varchar(20) DEFAULT NULL,
  `height` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`record_id`, `patient_id`, `doctor`, `date`, `complaint`, `diagnosis`, `treatment`, `medication`, `bp`, `temperature`, `weight`, `height`, `notes`, `created_at`) VALUES
(12, 28, 'fsdsf', '2025-10-09', 'masakit tyan', 'gege', 'dasdas', 'eqwqw', '150 over acting', 'sasas', 'sasa', 'dsaxz', '5445', '2025-10-15 00:57:54'),
(13, 29, 'fgset', '2025-06-18', 'masakit tyan', 'sas', 'sasa', 'neozep', '150 over acting', 'wqwqw', '4', '6', 'jkhj', '2025-10-15 01:00:49'),
(14, 2, 'ads', '2025-07-03', 'sdsad', 'sasa', 'dadad', 'eqwqw', '150 over acting', 'dsad', 'sasa', 'dsaxz', '6565', '2025-10-15 01:09:34'),
(15, 45, 'asdfa', '2025-10-30', 'asasas', 'fsfsafsf', '21545rfsd', 'rwerfdsfs', '12121', '1312', '12131', '31313', 'sdsaf', '2025-10-25 04:30:52'),
(16, 55, '21w12', '2025-10-25', 'asasas', 'fsfsafsf', '21545rfsd', 'rwerfdsfs', 'sad1221', '1312', '1212', '21321', '12321', '2025-10-25 04:33:08'),
(17, 55, '123', '2025-11-04', 'asasas', 'dsas', 'sdsdsa', 'sadsdas', 'adssd', 'sdads', 'dasdad', 'asdasds', 'asdasds', '2025-10-25 04:43:18'),
(18, 55, 'ree', '2025-11-01', 'asasas', 'dsas', '4343', 'rwerfdsfs', '12121', '4343', '12131', '4343', 'asdd', '2025-10-25 04:43:53'),
(19, 55, '21w12', '2025-11-06', 'asasas', 'dsas', 'sdsdsa', 'dada', 'dadad', 'dds', '12131', 'asdasds', 'tsrtsfg', '2025-10-25 04:47:53'),
(20, 55, '21w12', '2025-11-06', 'asasas', 'dsas', 'sdsdsa', 'dada', 'dadad', 'dds', '12131', 'asdasds', 'tsrtsfg', '2025-10-25 04:48:29'),
(21, 3, 'ewqeq', '2025-10-31', 'asasas', 'dsas', 'sdsdsa', 'rwerfdsfs', 'sad1221', 'sdads', '1212', '31313', 'weqew', '2025-10-25 05:01:54'),
(22, 7, 'saas', '2025-10-31', 'masakit tyan', 'mamamatay na', 'dasdas', 'neozep', '150 over acting', 'sasas', '122', '76', '121qwsasa', '2025-10-25 05:04:02'),
(23, 7, 'fgf', '2025-10-25', 'masakit tyan', 'mamamatay na', 'dasdas', 'neozep', '150 over acting', 'sasas', '122', '', 'dfgfddf', '2025-10-25 05:31:21'),
(24, 7, '', '2025-10-25', 'masakit tyan', 'mamamatay na', 'dasdas', 'neozep', '150 over acting', 'sasas', '122', '', '', '2025-10-25 05:37:32'),
(25, 7, 'qwwq', '2025-10-25', 'masakit tyan', 'mamamatay na', 'dasdas', 'neozep', '150 over acting', 'sasas', '122', '', 'sfdsfdsg', '2025-10-25 06:11:43'),
(26, 7, 'sasad', '2025-10-31', 'masakit tyan', 'mamamatay na', 'dasdas', 'neozep', '150 over acting', 'sasas', '122', '121', 'sasas', '2025-10-25 06:13:34'),
(27, 7, 'dsds', '2025-10-29', 'masakit tyan', 'mamamatay na', 'dasdas', 'neozep', '150 over acting', 'sasas', '122', '', 'sasas', '2025-10-29 08:26:49'),
(28, 3, 'qwq', '2025-10-29', 'masakit tyan', 'mamamatay na', 'dasdas', 'neozep', '150 over acting', 'sasas', '122', '', 'dsads', '2025-10-29 08:38:12'),
(29, 39, 'sas', '2025-10-30', 'sas', 'asa', 'sasa', NULL, 'sasa', 'sas', 'asa', 'sas', 'sas', '2025-10-30 11:58:15'),
(30, 3, 'sfsffff', '2025-10-30', 'sdsad', 'sfsf', 'fss', NULL, 'sfafa', 'fsfsf', 'safa', 'fsfsf', 'dasdaf', '2025-10-30 11:59:30'),
(31, 3, 'ada', '2025-10-30', 'masakit tyan', 'mamamatay na', 'mercy killing', NULL, 'we23', 'adda', 'rw3qes', 'saas', 'sas', '2025-10-30 12:01:15'),
(32, 3, 'sasas', '2025-10-30', 'masakit tyan', 'mamamatay na', 'dasdas', NULL, 'we23', '78', 'sas', 'sasaa', 'sas', '2025-10-30 12:27:38'),
(33, 45, 'dada', '2025-10-31', 'masakit tyan', 'sasa', 'asas', '', 'asa', 'sas', 'sas', 'sas', 'sas', '2025-10-30 12:32:24'),
(34, 3, 'dada', '2025-10-30', 'dada', 'dada', 'dad', 'MedID: 9 (Qty: 20), MedID: 40 (Qty: 10)', 'dada', 'dada', 'adada', 'dada', 'adda', '2025-10-30 12:39:44'),
(35, 3, 'dada', '2025-10-31', 'sasa', 'asasa', 'sasa', 'MedID: 44 (Qty: 99)', 'sas', 'sas', 'asas', 'sasa', 'sasa', '2025-10-30 12:41:24'),
(36, 3, 'nuiser', '2025-10-30', 'masakit tyan', 'asasa', 'dadaa', '', 'dad', 'dad', 'dad', 'dadad', 'dadad', '2025-10-30 12:44:42'),
(37, 3, 'sfsf', '2025-10-30', 'booom', 'dasf', 'sfsfsf', 'Salbutamol Syrup (10 pcs), Multivitamins (90 pcs)', 'asfsf', 'fasf', 'fasf', 'fsa', 'fsfs', '2025-10-30 12:47:04'),
(38, 3, 'dada', '2025-10-30', 'dd', 'dadad', '', 'Salbutamol Syrup (10 pcs), Multivitamins (100 pcs), ORS (Oral Rehydration Salts) (20 pcs)', 'dad', '', 'dada', '', 'dada', '2025-10-30 13:09:01'),
(39, 3, 'rer', '2025-10-30', 're', 're', 're', 'ORS (Oral Rehydration Salts) (Qty: 21), Paracetamol 500mg (Qty: 50)', 're', 'rer', '1223', 'rer', 'erer', '2025-10-30 13:24:42'),
(40, 3, 'dsad', '2025-10-30', 'masakit tyan', 'dsada', 'dasdasd', 'Paracetamol 500mg (Qty: 100)', 'dasd', 'sadsa', 'asda', 'asdd', 'adsd', '2025-10-30 13:49:30'),
(41, 3, 'sfdsafds', '2025-10-30', 'masakit tyan', 'sasa', '', 'Paracetamol (Qty: 20), hehea (Qty: 20)', 'dada', 'dad', 'addad', 'dad', 'adad', '2025-10-30 13:50:46'),
(42, 39, 'sasas', '2025-10-31', 'masakit tyan', 'mamamatay na', 'asa', 'Paracetamol 500mg (Qty: 20), Paracetamol (Qty: 60)', 's', 'sas', 'sasa', 'asas', 'sasas', '2025-10-31 02:15:12'),
(43, 72, 'dadad', '2025-10-31', 'masakit tyan', 'sasa', '2323', 'Multivitamins (Qty: 20), Paracetamol 500mg (Qty: 20)', 'dada', 'dad', 'addad', 'dad', '12112', '2025-10-31 02:30:33'),
(44, 73, 'Doc Mayki', '2025-11-02', 'booom', 'mamamatay na', '56', 'Paracetamol (Qty: 10), ORS (Oral Rehydration Salts) (Qty: 9)', '150 over acting', '2323', 'sasa', '323', 'sas', '2025-11-02 06:52:28'),
(45, 75, 'nyehe', '2025-11-02', 'masakit tyan', 'sasa', '2323', 'heghe (Qty: 2), boompow (Qty: 100)', 'dada', 'dad', 'addad', 'dad', 'assas', '2025-11-02 06:59:00'),
(46, 76, 'doc bang', '2025-11-02', 'masakit tyan', 'sasa', '2323', 'Paracetamol (Qty: 10), Paracetamol 500mg (Qty: 10)', 'dada', 'dad', 'addad', 'dad', 'sasa', '2025-11-02 07:27:59'),
(47, 39, 'thess', '2025-11-02', 'masakit tyan', 'mamamatay na', 'mercy killing', 'hhj (Qty: 10), junjun (Qty: 10)', 'we23', 'sas', 'sasa', 'dsaxz', 'sasa', '2025-11-02 07:57:49'),
(48, 3, 'e54df', '2025-11-02', 'masakit tyan', 'sasa', 'sasa', 'dada (Qty: 200), bombep (Qty: 10)', 'sasa', 'sas', 'sasas', '', 'sasa', '2025-11-02 07:59:44'),
(50, 70, 'mamamo', '2025-11-03', 'masakit tyan', 'dsadsa', 'ssada', 'hhj (Qty: 2), lomotil (Qty: 5)', 'dsds', 'dsd', 'dasd', 'dasda', 'dasasd', '2025-11-03 07:03:30'),
(51, 39, 'dsa', '2025-11-03', 'booom', 'mamamatay na', 'dsa', 'hsahshashahsa (Qty: 100)', 'dad', 'sda', 'sasa', 'dasd', 'dsad', '2025-11-03 08:22:44'),
(52, 39, 'sas', '2025-11-03', 'masakit tyan', 'sas', 'sasa', 'hatdogcheesedog (Qty: 50)', 'sas', 'sas', 'sas', 'sas', 'sas', '2025-11-03 08:24:53'),
(53, 67, 'fdfdf', '2025-11-03', 'masakit tyan', 'sdfd', 'gfhgf', 'hatdogcheesedog (Qty: 50)', 'fgd', 'fdd', 'fdfd', 'fdfd', 'dfdfd', '2025-11-03 08:26:13'),
(54, 36, 'mamiii', '2025-11-19', 'sasa', 'sasas', 'mercy killing', 'junjun (Qty: 2), lomotil (Qty: 5)', 'sasas', '78', 'dada', 'dsaxz', 'ddddd', '2025-11-06 05:38:04');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `med_id` int(11) NOT NULL,
  `med_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `unit` varchar(50) DEFAULT NULL,
  `batch_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `reorder_level` int(11) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`med_id`, `med_name`, `description`, `category`, `quantity`, `unit`, `batch_number`, `notes`, `expiry_date`, `date_added`, `reorder_level`) VALUES
(9, 'Paracetamol', 'Pain and fever reducer', 'Analgesic', 80, 'Tablet', 'BATCH002', 'Stock replenished', '2026-03-15', '2025-10-05 05:31:55', 10),
(10, 'Vitamin A', 'For child nutrition', 'Vitamin', 300, 'Capsule', 'BATCH003', 'For children', '2027-08-01', '2025-10-05 05:31:55', 10),
(11, 'Povidone-Iodine', 'Wound disinfectant', 'Antiseptic', 50, 'Bottle', 'BATCH004', 'First aid use', '2026-09-10', '2025-10-05 05:31:55', 10),
(12, 'Pentavalent Vaccine', 'Prevents DPT-HepB-Hib', 'Vaccine', 15, 'Vial', 'BATCH005', 'Cold storage', '2025-10-20', '2025-10-05 05:31:55', 10),
(26, 'Amoxicillin Capsule', NULL, 'Antibiotic', 140, 'Capsule', 'BATCH005', NULL, '2025-10-15', '2025-10-05 06:01:38', 10),
(27, 'Amoxicillin Syrup', NULL, 'Analgesic', 130, 'Syrup', 'BATCH005', NULL, '2025-10-15', '2025-10-05 06:01:45', 10),
(28, 'Cotrimoxazole Tablet', NULL, 'Antibiotic', 154, 'Tablet', 'BATCH005', NULL, '2025-10-15', '2025-10-05 06:03:40', 10),
(29, 'Ciprofloxacin', NULL, 'Antibiotic', 150, 'Tablet', 'BATCH005', NULL, '2025-10-08', '2025-10-05 06:05:41', 10),
(30, 'Paracetamol Drop', NULL, 'Analgesic', 150, 'Drop', 'BATCH005', NULL, '2025-10-21', '2025-10-05 06:07:29', 10),
(31, 'Ascorbic Acid Vitamin C Tablet', NULL, 'Vitamin', 152, 'Tablet', 'BATCH005', NULL, '2025-10-21', '2025-10-05 06:10:27', 10),
(32, 'Ascorbic Acid Vitamin C Drop', NULL, 'Vitamin', 150, 'Drop', 'BATCH005', NULL, '2025-10-09', '2025-10-05 08:45:11', 10),
(33, 'Vitamins B Complex', NULL, 'Vitamin', 180, 'Capsule', 'BATCH005', NULL, '2025-11-19', '2025-10-06 01:52:13', 10),
(34, 'Ferrous Sulfate(Iron Supplement)', NULL, 'Vitamin', 157, 'Capsule', 'BATCH005', NULL, '2025-10-22', '2025-10-13 02:29:45', 10),
(35, 'Paracetamol 500mg', 'Used to relieve mild to moderate pain and reduce fever.', 'Analgesic', 50, 'Tablets', 'BCH-2023-001', 'Store in a cool, dry place.', '2026-03-15', '2023-01-09 16:00:00', 10),
(36, 'Amoxicillin 250mg', 'Antibiotic for treating bacterial infections such as pneumonia and bronchitis.', 'antibiotic', 90, 'Capsule', 'BCH-2023-002', 'For oral use only.', '2026-05-22', '2023-01-31 16:00:00', 10),
(37, 'Cetirizine 10mg', 'Relieves allergy symptoms such as sneezing, itching, and runny nose.', 'Antihistamine', 300, 'Tablets', 'BCH-2024-003', 'Do not exceed recommended dose.', '2027-01-05', '2024-01-19 16:00:00', 10),
(38, 'Mefenamic Acid 500mg', 'Pain reliever and anti-inflammatory medication for headache and toothache.', 'Pain Reliever', 200, 'Capsules', 'BCH-2024-004', 'Take after meals.', '2026-12-30', '2024-05-14 16:00:00', 10),
(39, 'ORS (Oral Rehydration Salts)', 'Used to prevent or treat dehydration due to diarrhea or heat.', 'Rehydration', 70, 'Sachets', 'BCH-2025-005', 'Mix with clean, boiled water before use.', '2026-09-10', '2025-02-10 16:00:00', 10),
(40, 'Multivitamins', 'Daily supplement to support immune system and energy levels.', 'Vitamins', 180, 'Tablets', 'BCH-2025-006', 'Keep away from sunlight.', '2027-03-15', '2025-04-04 16:00:00', 10),
(41, 'Salbutamol Syrup', 'Used to relieve asthma and other conditions with breathing difficulty.', 'Bronchodilator', 70, 'Bottles', 'BCH-2025-007', 'Shake well before use.', '2026-11-20', '2025-07-11 16:00:00', 10),
(42, 'Betadine', NULL, 'Antiseptic', 4900, 'Bottle', 'BATCH005', NULL, '2025-10-31', '2025-10-23 11:32:29', 10),
(45, 'Ethyl Alcohol 70%', NULL, 'Antiseptic', 190, 'Bottle', 'BATCH005', NULL, '2026-11-07', '2025-11-02 07:44:23', 10),
(46, 'BCG', NULL, 'Vaccine', 400, 'Ampoule', 'BATCH005', NULL, '2026-07-03', '2025-11-02 07:45:29', 10),
(51, 'Measles', NULL, 'Vaccine', 123, 'Ampoule', 'BATCH005', NULL, '2025-11-14', '2025-11-06 04:50:43', 10),
(52, 'Polio', NULL, 'Vaccine', 123, 'Vial', 'BATCH005', NULL, '2025-11-14', '2025-11-06 04:50:58', 10),
(53, 'Flu', NULL, 'Vaccine', 60, 'Ampoule', 'BATCH005', NULL, '2026-08-28', '2025-11-06 05:32:29', 10),
(54, 'Hepatitis B', NULL, 'Vaccine', 273, 'Vial', 'BATCH005', NULL, '2025-11-12', '2025-11-06 05:32:59', 10);

-- --------------------------------------------------------

--
-- Table structure for table `medicine_assistance`
--

CREATE TABLE `medicine_assistance` (
  `assist_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `med_id` int(11) NOT NULL,
  `quantity_given` int(11) NOT NULL,
  `date_given` timestamp NOT NULL DEFAULT current_timestamp(),
  `given_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine_assistance`
--

INSERT INTO `medicine_assistance` (`assist_id`, `patient_id`, `record_id`, `med_id`, `quantity_given`, `date_given`, `given_by`) VALUES
(17, 33, NULL, 33, 9, '2025-10-13 02:33:17', 'sasa'),
(18, 28, NULL, 33, 50, '2025-10-15 00:55:40', 'asasa'),
(32, 29, NULL, 9, 7, '2023-06-17 16:00:00', 'Midwife Carla'),
(33, 30, NULL, 10, 3, '2024-01-24 16:00:00', 'Nurse Liza'),
(34, 31, NULL, 11, 4, '2024-07-08 16:00:00', 'Dr. Reyes'),
(35, 32, NULL, 12, 2, '2024-11-10 16:00:00', 'Nurse Liza'),
(36, 33, NULL, 26, 6, '2025-01-09 16:00:00', 'Dr. Reyes'),
(37, 28, NULL, 29, 4, '2025-03-14 16:00:00', 'Midwife Carla'),
(38, 29, NULL, 30, 5, '2025-05-19 16:00:00', 'Nurse Liza'),
(39, 30, NULL, 31, 3, '2025-07-01 16:00:00', 'Midwife Carla'),
(40, 31, NULL, 32, 2, '2025-09-07 16:00:00', 'Nurse Liza'),
(42, 29, NULL, 9, 2, '2023-02-17 16:00:00', 'Midwife Carla'),
(43, 30, NULL, 10, 4, '2023-03-19 16:00:00', 'Nurse Liza'),
(44, 31, NULL, 11, 3, '2023-06-04 16:00:00', 'Dr. Reyes'),
(45, 32, NULL, 12, 2, '2023-09-11 16:00:00', 'Nurse Liza'),
(46, 33, NULL, 26, 3, '2023-12-20 16:00:00', 'Midwife Carla'),
(48, 29, NULL, 9, 4, '2024-02-14 16:00:00', 'Midwife Carla'),
(49, 30, NULL, 10, 6, '2024-03-17 16:00:00', 'Nurse Liza'),
(50, 31, NULL, 11, 5, '2024-05-08 16:00:00', 'Dr. Reyes'),
(51, 32, NULL, 12, 5, '2024-07-13 16:00:00', 'Nurse Liza'),
(52, 33, NULL, 26, 7, '2024-09-22 16:00:00', 'Midwife Carla'),
(53, 28, NULL, 27, 6, '2024-11-18 16:00:00', 'Dr. Reyes'),
(54, 29, NULL, 28, 8, '2024-12-04 16:00:00', 'Nurse Liza'),
(56, 29, NULL, 9, 8, '2025-02-09 16:00:00', 'Midwife Carla'),
(57, 30, NULL, 10, 10, '2025-03-14 16:00:00', 'Dr. Reyes'),
(58, 31, NULL, 11, 9, '2025-04-19 16:00:00', 'Nurse Liza'),
(59, 32, NULL, 12, 11, '2025-05-21 16:00:00', 'Midwife Carla'),
(60, 33, NULL, 26, 12, '2025-06-24 16:00:00', 'Dr. Reyes'),
(61, 28, NULL, 27, 10, '2025-07-28 16:00:00', 'Nurse Liza'),
(62, 29, NULL, 28, 14, '2025-08-10 16:00:00', 'Midwife Carla'),
(63, 30, NULL, 29, 13, '2025-09-17 16:00:00', 'Dr. Reyes'),
(64, 31, NULL, 30, 15, '2025-10-24 16:00:00', 'Nurse Liza'),
(65, 32, NULL, 31, 16, '2025-11-07 16:00:00', 'Midwife Carla'),
(66, 33, NULL, 32, 18, '2025-12-18 16:00:00', 'Dr. Reyes'),
(67, 39, NULL, 36, 5, '2025-10-23 11:33:15', 'sweffr'),
(68, 44, NULL, 36, 120, '2025-10-24 07:43:13', 'hehe'),
(69, 39, NULL, 36, 12, '2025-10-25 04:27:04', 'koko'),
(70, 39, NULL, 36, 12, '2025-10-25 06:25:12', 'dfsdff'),
(72, 39, NULL, 36, 20, '2025-10-29 08:13:14', 'sasasasa'),
(73, 44, NULL, 36, 1, '2025-10-29 08:16:20', 'asasa'),
(74, 36, NULL, 36, 1, '2025-10-29 08:24:18', 'dfsdff'),
(75, 39, NULL, 36, 2, '2025-10-29 08:24:54', 'me'),
(76, 39, NULL, 36, 2, '2025-10-29 08:30:28', 'sasa'),
(77, 39, NULL, 36, 2, '2025-10-29 08:31:17', 'asasa'),
(78, 44, NULL, 36, 1, '2025-10-29 08:39:53', 'sas'),
(79, 39, NULL, 36, 2, '2025-10-29 12:11:17', 'me'),
(80, 3, 39, 39, 21, '2025-10-30 13:24:42', 'rer'),
(81, 3, 39, 35, 50, '2025-10-30 13:24:42', 'rer'),
(82, 3, 40, 35, 100, '2025-10-30 13:49:30', 'dsad'),
(83, 3, 41, 9, 20, '2025-10-30 13:50:46', 'sfdsafds'),
(84, 3, 41, 34, 20, '2025-10-30 13:50:46', 'sfdsafds'),
(85, 39, 42, 35, 20, '2025-10-31 02:15:12', 'sasas'),
(86, 39, 42, 9, 60, '2025-10-31 02:15:12', 'sasas'),
(87, 72, 43, 40, 20, '2025-10-31 02:30:33', 'dadad'),
(88, 72, 43, 35, 20, '2025-10-31 02:30:33', 'dadad'),
(89, 73, 44, 9, 10, '2025-11-02 06:52:28', 'Doc Mayki'),
(90, 73, 44, 39, 9, '2025-11-02 06:52:28', 'Doc Mayki'),
(91, 75, 45, 26, 2, '2025-11-02 06:59:00', 'nyehe'),
(92, 75, 45, 42, 100, '2025-11-02 06:59:00', 'nyehe'),
(93, 76, 46, 9, 10, '2025-11-02 07:27:59', 'doc bang'),
(94, 76, 46, 35, 10, '2025-11-02 07:27:59', 'doc bang'),
(97, 39, 47, 30, 10, '2025-11-02 07:57:49', 'thess'),
(98, 39, 47, 29, 10, '2025-11-02 07:57:49', 'thess'),
(99, 3, 48, 46, 200, '2025-11-02 07:59:44', 'e54df'),
(101, 70, 50, 30, 2, '2025-11-03 07:03:30', 'mamamo'),
(102, 70, 50, 45, 5, '2025-11-03 07:03:30', 'mamamo'),
(161, 2, NULL, 9, 2, '2022-02-12 03:15:00', 'Dr. Reyes'),
(162, 3, NULL, 10, 4, '2022-03-19 02:30:00', 'Midwife Carla'),
(163, 4, NULL, 11, 3, '2022-04-22 05:00:00', 'Dr. Santos'),
(164, 5, NULL, 12, 2, '2022-05-18 07:30:00', 'Nurse Liza'),
(166, 7, NULL, 9, 3, '2022-09-11 06:00:00', 'Dr. Reyes'),
(167, 8, NULL, 10, 5, '2022-11-16 01:30:00', 'Nurse Liza'),
(169, 10, NULL, 9, 6, '2023-02-18 03:40:00', 'Dr. Reyes'),
(170, 28, NULL, 10, 7, '2023-03-23 06:10:00', 'Midwife Carla'),
(171, 29, NULL, 11, 6, '2023-04-27 01:00:00', 'Dr. Santos'),
(172, 30, NULL, 12, 8, '2023-06-12 07:00:00', 'Nurse Liza'),
(173, 31, NULL, 26, 9, '2023-07-20 00:30:00', 'Midwife Carla'),
(174, 32, NULL, 27, 7, '2023-09-05 08:00:00', 'Dr. Reyes'),
(175, 33, NULL, 28, 10, '2023-11-25 05:45:00', 'Nurse Liza'),
(177, 36, NULL, 9, 10, '2024-02-14 03:10:00', 'Dr. Reyes'),
(178, 37, NULL, 10, 12, '2024-03-19 06:30:00', 'Midwife Carla'),
(179, 38, NULL, 11, 11, '2024-04-23 02:45:00', 'Dr. Santos'),
(180, 39, NULL, 12, 13, '2024-05-30 07:10:00', 'Nurse Liza'),
(181, 40, NULL, 26, 14, '2024-07-09 01:00:00', 'Midwife Carla'),
(182, 41, NULL, 27, 15, '2024-08-13 05:30:00', 'Dr. Reyes'),
(183, 42, NULL, 28, 16, '2024-09-20 00:40:00', 'Nurse Liza'),
(184, 43, NULL, 29, 17, '2024-10-18 08:00:00', 'Dr. Santos'),
(185, 44, NULL, 30, 18, '2024-11-21 02:00:00', 'Midwife Carla'),
(186, 45, NULL, 31, 19, '2024-12-10 03:30:00', 'Nurse Liza'),
(187, 36, 54, 29, 2, '2025-11-06 05:38:04', 'mamiii'),
(188, 36, 54, 45, 5, '2025-11-06 05:38:04', 'mamiii'),
(189, 3, NULL, 46, 10, '2025-11-08 11:03:49', 'JOSE RIZAL'),
(190, 3, NULL, 46, 90, '2025-11-08 11:15:51', 'JOSE RIZAL'),
(191, 3, NULL, 46, 50, '2025-11-08 11:19:33', 'JOSE RIZAL'),
(192, 78, NULL, 46, 10, '2025-11-08 11:19:48', 'JOSE RIZAL'),
(193, 39, NULL, 46, 30, '2025-11-08 11:19:59', 'JOSE RIZAL'),
(194, 70, NULL, 46, 10, '2025-11-08 11:20:09', 'JOSE RIZAL'),
(195, 78, NULL, 26, 12, '2025-11-08 11:27:02', 'JOSE RIZAL'),
(196, 3, NULL, 36, 10, '2025-11-08 11:29:29', 'JOSE RIZAL'),
(197, 3, NULL, 27, 14, '2025-11-08 11:31:09', 'JOSE RIZAL'),
(198, 3, NULL, 27, 10, '2025-11-08 11:32:56', 'JOSE RIZAL'),
(199, 3, NULL, 32, 8, '2025-11-08 11:44:59', 'JOSE RIZAL');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `fullname` varchar(150) NOT NULL,
  `dob` date NOT NULL,
  `age` varchar(20) DEFAULT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `purok` varchar(50) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `civil_status` enum('Single','Married','Widowed','Separated') DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `emergency_contact` varchar(150) DEFAULT NULL,
  `date_registered` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `fullname`, `dob`, `age`, `sex`, `blood_type`, `purok`, `contact`, `civil_status`, `occupation`, `emergency_contact`, `date_registered`) VALUES
(1, 'Maria Santos', '1989-04-12', '36', 'Female', 'O+', 'Purok 1', '09171234567', 'Married', 'Teacher', '09184561234', '2023-02-14 16:00:00'),
(2, 'Jose Dela Cruz', '1995-07-09', '30', 'Male', 'A+', 'Purok 2', '09182345678', 'Single', 'Tricycle Driver', '09271239876', '2023-07-20 16:00:00'),
(3, 'Angela Ramos', '2001-11-23', '23', 'Female', 'B+', 'Purok 3', '09987654321', 'Single', 'Student', '09193456789', '2023-10-01 16:00:00'),
(4, 'Carlo Mendoza', '1985-02-05', '40', 'Male', 'AB+', 'Purok 1', '09194561234', 'Married', 'Farmer', '09193456222', '2024-03-09 16:00:00'),
(5, 'Ella Bautista', '1999-06-18', '26', 'Female', 'O-', 'Purok 4', '09283456712', 'Single', 'Nurse', '09284567891', '2024-08-04 16:00:00'),
(6, 'Jerome Villanueva', '1992-09-30', '33', 'Male', 'B-', 'Purok 2', '09194567890', 'Married', 'Barangay Tanod', '09183456788', '2024-11-16 16:00:00'),
(7, 'Trisha Cruz', '2003-01-20', '22', 'Female', 'A+', 'Purok 5', '09193456721', 'Single', 'Student', '09191234567', '2025-03-11 16:00:00'),
(8, 'Nathaniel Lim', '1998-12-10', '26', 'Male', 'O+', 'Purok 3', '09991234567', 'Single', 'Graphic Designer', '09183456789', '2025-05-29 16:00:00'),
(9, 'Michelle Silungan', '1988-08-22', '37', 'Female', 'AB-', 'Purok 1', '09171231234', 'Married', 'Business Owner', '09283456721', '2025-07-09 16:00:00'),
(10, 'Heinriche Garcia', '1997-10-19', '28', 'Male', 'B+', 'Purok 4', '09184567890', 'Single', 'IT Support', '09193456777', '2025-09-01 16:00:00'),
(28, 'hehba', '2025-10-28', '3', 'Male', 'A+', 'Purok 2', '0913232323', 'Single', 'sasa', '03233356565', '2025-10-12 06:37:31'),
(29, 'ehejjjkkjjk', '2025-10-15', '3', 'Male', 'A+', 'Purok 2', '0913232323', 'Married', 'n/a', '3123233', '2025-10-12 06:39:05'),
(30, 'mae', '2025-10-22', '6', 'Female', 'A+', 'Purok 3', '2121212', 'Single', 'sdsds', '03233356565', '2025-10-12 06:41:35'),
(31, 'yoyo', '2025-10-08', '3', 'Male', 'A-', 'Purok 1', '0913232323', 'Married', 'sdsds', '2121212123', '2025-10-12 06:43:16'),
(32, 'mae', '2025-10-17', '6', 'Female', 'A+', 'Purok 3', '2121212', 'Single', 'sdsds', '03233356565', '2025-10-12 06:45:27'),
(33, 'jonojo', '2025-10-07', '6 months', 'Female', 'A+', 'Purok 3', '2121212', 'Single', 'sdsds', '03233356565', '2025-10-12 06:48:14'),
(34, 'sasasadee3', '2025-10-09', '45', 'Female', 'A-', 'Purok 3', '0913232323', 'Widowed', 'hsahsha', '3123233', '2025-10-15 00:56:58'),
(35, 'Lucia Rivera', '1984-09-11', '41', 'Female', 'O+', 'Purok 2', '09184567891', 'Married', 'Housewife', 'Carlos Rivera - 09182345678', '2023-03-19 16:00:00'),
(36, 'Benito Flores', '1975-05-02', '50', 'Male', 'A-', 'Purok 4', '09191239876', 'Married', 'Farmer', 'Leah Flores - 09183456789', '2023-06-13 16:00:00'),
(37, 'Julieta Ramos', '1999-10-18', '26', 'Female', 'B+', 'Purok 3', '09284567890', 'Single', 'Call Center Agent', 'Roberto Ramos - 09193456721', '2023-08-04 16:00:00'),
(38, 'Michael Torres', '1994-02-08', '31', 'Male', 'AB+', 'Purok 1', '09195678912', 'Single', 'Construction Worker', 'Maria Torres - 09192345671', '2023-11-11 16:00:00'),
(39, 'Arlene Dominguez', '1986-06-25', '39', 'Female', 'O-', 'Purok 5', '09194561234', 'Married', 'Vendor', 'Joel Dominguez - 09194567890', '2024-01-28 16:00:00'),
(40, 'Ryan Del Rosario', '1990-12-14', '34', 'Male', 'B-', 'Purok 3', '09283456123', 'Single', 'Delivery Rider', 'Rina Del Rosario - 09273456123', '2024-03-14 16:00:00'),
(41, 'Diana Bautista', '2000-04-07', '25', 'Female', 'A+', 'Purok 1', '09172345678', 'Single', 'Student', 'Luis Bautista - 09192345678', '2024-05-19 16:00:00'),
(42, 'Kevin Lopez', '1983-11-21', '41', 'Male', 'O+', 'Purok 2', '09983456122', 'Married', 'Barangay Kagawad', 'Jenny Lopez - 09182345612', '2024-07-08 16:00:00'),
(43, 'Martha De Vera', '1997-01-30', '28', 'Female', 'B-', 'Purok 4', '09193456788', 'Single', 'Nurse', 'Jose De Vera - 09184567899', '2024-09-30 16:00:00'),
(44, 'Arnold Villanueva', '1989-08-16', '36', 'Male', 'A+', 'Purok 5', '09274567890', 'Married', 'Electrician', 'Grace Villanueva - 09273456780', '2024-12-16 16:00:00'),
(45, 'Clarissa Mendoza', '2002-03-25', '23', 'Female', 'O-', 'Purok 1', '09183456790', 'Single', 'Cashier', 'Cecilia Mendoza - 09182345690', '2025-01-08 16:00:00'),
(46, 'Roberto Diaz', '1978-10-19', '47', 'Male', 'B+', 'Purok 2', '09294567811', 'Married', 'Mechanic', 'Angela Diaz - 09192345611', '2025-02-22 16:00:00'),
(47, 'Nicole Tan', '1999-07-02', '26', 'Female', 'A-', 'Purok 3', '09182345700', 'Single', 'Designer', 'Jasmine Tan - 09282345600', '2025-03-17 16:00:00'),
(48, 'Patrick Lim', '1987-05-17', '38', 'Male', 'O+', 'Purok 4', '09191234500', 'Married', 'Security Guard', 'Catherine Lim - 09193456700', '2025-04-26 16:00:00'),
(49, 'Jasmine Dela Vega', '2005-12-11', '19', 'Female', 'AB+', 'Purok 5', '09281234567', 'Single', 'Student', 'Nina Dela Vega - 09193456711', '2025-05-29 16:00:00'),
(50, 'Eduardo Soriano', '1969-09-09', '56', 'Male', 'B-', 'Purok 1', '09183456710', 'Married', 'Retired', 'Lourdes Soriano - 09182345611', '2025-06-13 16:00:00'),
(51, 'Lara Aquino', '1996-01-14', '29', 'Female', 'A+', 'Purok 2', '09991234512', 'Single', 'Pharmacist', 'Cathy Aquino - 09183456112', '2025-07-04 16:00:00'),
(52, 'Gabriel Cruz', '2004-08-09', '21', 'Male', 'O+', 'Purok 3', '09181234567', 'Single', 'Student', 'Trisha Cruz - 09191234568', '2025-08-21 16:00:00'),
(53, 'Monica Estrada', '1991-03-03', '34', 'Female', 'AB-', 'Purok 4', '09183456744', 'Married', 'Clerk', 'Jose Estrada - 09182345645', '2025-09-08 16:00:00'),
(54, 'Ramon Bautista', '1982-06-18', '43', 'Male', 'B+', 'Purok 5', '09181234512', 'Married', 'Barangay Captain', 'Nora Bautista - 09193456713', '2025-10-04 16:00:00'),
(55, 'trisha mae santos', '2001-09-12', '24', 'Female', '', 'Purok 6', 'sas2q', 'Single', 'n/a', '3232332', '2025-10-25 04:32:24'),
(56, 'nye', '2001-01-01', '24', 'Female', 'B-', 'Purok 2', 'sas2q', 'Single', 'eaad', '3232332', '2025-10-25 04:45:36'),
(57, 'Theodore De Villa', '2025-07-16', '3 months', 'Female', 'B-', 'Purok 3', '09120221322', '', 'sdsds', '3123233', '2025-10-25 05:05:28'),
(58, 'Theodore De Villa', '2025-10-31', '3 months', 'Female', 'B-', 'Purok 3', '09120221322', '', 'sdsds', '3123233', '2025-10-25 05:30:22'),
(59, 'Taylor Swift', '2025-05-15', '20', 'Female', 'B+', 'Purok 2', '21213', 'Single', '3232', 'dwew', '2025-10-25 06:03:30'),
(60, 'Taylor Swift', '2025-10-03', '20', 'Female', 'B+', 'Purok 2', '21213', 'Single', '3232', 'dwew', '2025-10-25 06:13:59'),
(61, 'Taylor Swift', '2025-09-25', '6 months', 'Female', 'B+', 'Purok 2', '21213', 'Single', '3232', 'dwew', '2025-10-25 06:24:54'),
(62, 'selena gomez', '2025-10-30', '6 months', 'Female', 'B+', 'Purok 2', '21213', 'Single', '3232', 'dwew', '2025-10-29 08:27:17'),
(63, 'selena gomez', '2025-10-02', '6 months', 'Female', 'B+', 'Purok 2', '21213', 'Single', '3232', 'dwew', '2025-10-29 08:29:56'),
(64, 'selena gomez', '2025-01-22', '6 months', 'Female', 'B+', 'Purok 2', '21213', 'Single', '3232', 'dwew', '2025-10-29 08:36:19'),
(66, 'selena gomez', '2025-06-13', '6 months', 'Female', 'B+', 'Purok 2', '21213', 'Single', '3232', 'dwew', '2025-10-29 08:41:53'),
(67, 'Aziel Flores', '2025-03-21', '6 months', 'Female', 'B+', 'Purok 2', '21213', 'Single', '3232', 'dwew', '2025-10-29 08:43:12'),
(68, 'Aziel Flores', '2025-06-18', '6 months', 'Female', 'B+', 'Purok 2', '21213', 'Single', '3232', 'dwew', '2025-10-29 08:44:39'),
(69, 'Aziel Flores', '2025-10-17', '6 months', 'Female', 'B+', 'Purok 2', '21213', 'Single', '3232', 'dwew', '2025-10-29 08:46:29'),
(70, 'Aziel Flores', '2025-10-03', '6 months', 'Female', 'B+', 'Purok 2', '21213', 'Single', '3232', 'dwew', '2025-10-29 08:47:40'),
(71, 'sasasadee3fdffsd', '2025-10-22', '12', 'Male', 'A-', 'Purok 2', 'SA', 'Single', 'D', 'SAS', '2025-10-29 12:08:45'),
(72, 'Hatdog Santos', '2025-06-06', '45', 'Male', 'A+', 'Purok 2', '0913232323', 'Single', 'hsahsha', '03233356565', '2025-10-31 02:19:34'),
(73, 'rhitz', '2025-11-27', '45', 'Male', 'A+', 'Purok 1', '0913232323', 'Widowed', '212', '3123233', '2025-11-02 06:51:43'),
(74, 'mommy dionisia', '1965-02-02', '61', 'Female', 'A+', 'Purok 1', '0913232323', 'Single', '212', '3123233', '2025-11-02 06:57:22'),
(75, 'sasasadeewakaWEWEW', '2024-03-01', '12', 'Male', 'A-', 'Purok 2', 'SA', 'Single', 'D', 'SAS', '2025-11-02 06:58:19'),
(76, 'ma dongseok', '2025-05-01', '12', 'Male', 'A-', 'Purok 2', 'SA', 'Single', 'D', 'SAS', '2025-11-02 07:27:30'),
(77, 'briella jazz', '2022-10-14', '3', 'Female', 'A+', 'Purok 6', '0913232323', '', 'hsahsha', '03233356565', '2025-11-02 07:57:15'),
(78, 'angelcia', '2001-09-12', '45', 'Female', 'B+', 'Purok 2', '0913232323', 'Married', 'sasasda', '03233356565', '2025-11-07 10:07:09'),
(79, 'rhitzy', '2025-08-08', '0', 'Female', '', 'Purok 2', '0913232323', 'Single', 'hsahsha', '03233356565', '2025-11-07 10:10:17'),
(80, 'aziel bugok', '2001-07-31', '24 years and 3 month', 'Male', 'Not y', 'Purok 2', '0913232323', 'Single', 'sdsds', 'sdsdsd', '2025-11-08 03:06:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `role` enum('admin','staff') NOT NULL,
  `username` varchar(100) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role`, `username`, `fullname`, `email`, `password`, `id_number`, `created_at`) VALUES
(21, 'admin', 'adminmayeye', 'Mariel Ilao', 'admin@bagongsilang.gov.ph', 'af7e333d12ea1689acb7c431f8677808', NULL, '2025-10-13 01:50:35'),
(22, 'admin', 'Trishami', 'Trisha Mae Santos', 'admintrisha@gmail.com', '$2y$10$00mD2jxudmeiEUULTRiEpeDlXa5K5ygLAUJ93VFceQWOv8Dbtr4LS', NULL, '2025-10-24 08:09:27'),
(24, 'admin', 'shasha', 'shasha santos', 'sha@gmail.com', '$2y$10$5LQ1hRc0NpfNbqScLARDz.fPI4w9nRIJU0FtKDUSc84aR4Wg6eCeK', NULL, '2025-10-29 04:03:02'),
(26, 'admin', 'jajabels', 'jaja ramos', 'jajaramos@gmail.com', '$2y$10$SkG2ZlmfBvXRxHUhCuphG.M4UPJdudGA8EhUUi.YdsS.EJcLamjye', NULL, '2025-11-06 02:33:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_user` (`user_id`);

--
-- Indexes for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `external_events`
--
ALTER TABLE `external_events`
  ADD PRIMARY KEY (`event_date`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`med_id`);

--
-- Indexes for table `medicine_assistance`
--
ALTER TABLE `medicine_assistance`
  ADD PRIMARY KEY (`assist_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `med_id` (`med_id`),
  ADD KEY `fk_medicine_record` (`record_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_trail`
--
ALTER TABLE `audit_trail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=675;

--
-- AUTO_INCREMENT for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `med_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `medicine_assistance`
--
ALTER TABLE `medicine_assistance`
  MODIFY `assist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE;

--
-- Constraints for table `medicine_assistance`
--
ALTER TABLE `medicine_assistance`
  ADD CONSTRAINT `fk_medicine_record` FOREIGN KEY (`record_id`) REFERENCES `medical_records` (`record_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `medicine_assistance_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicine_assistance_ibfk_2` FOREIGN KEY (`med_id`) REFERENCES `medicines` (`med_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

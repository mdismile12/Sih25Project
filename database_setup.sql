-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2025 at 11:58 AM
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
-- Database: `agrisense_db`
--
CREATE DATABASE IF NOT EXISTS `agrisense_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `agrisense_db`;

-- --------------------------------------------------------

--
-- Table structure for table `ai_analysis`
--
CREATE TABLE IF NOT EXISTS `ai_analysis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `animal_id` varchar(50) DEFAULT NULL,
  `symptoms` text DEFAULT NULL,
  `recommendation` text DEFAULT NULL,
  `confidence_score` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--
CREATE TABLE IF NOT EXISTS `alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `farm_id` varchar(50) DEFAULT NULL,
  `alert_type` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `severity` varchar(20) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `alerts`
INSERT INTO `alerts` (`farm_id`, `alert_type`, `message`, `severity`, `is_read`, `created_at`) VALUES
('FARM-001', 'Medication', 'Withdrawal period ending for Oxytetracycline', 'high', 0, '2025-12-08 10:28:56'),
('FARM-002', 'Health', 'Scheduled vaccination reminder', 'medium', 0, '2025-12-08 10:28:56'),
('FARM-003', 'MRL', 'Lab test results available', 'high', 1, '2025-12-08 10:28:56'),
('FARM-004', 'Inventory', 'Medicine stock running low', 'medium', 0, '2025-12-08 10:28:56'),
('FARM-005', 'Compliance', 'MRL compliance report generated', 'low', 1, '2025-12-08 10:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `resource` varchar(100) DEFAULT NULL,
  `details` longtext DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `audit_logs`
INSERT INTO `audit_logs` (`user_id`, `action`, `resource`, `details`, `ip_address`, `created_at`) VALUES
('VET001', 'CREATE', 'Prescription', 'Created prescription for COW-001', '192.168.1.100', '2025-12-08 10:28:56'),
('GOV001', 'VIEW', 'Dashboard', 'Accessed district dashboard', '192.168.1.101', '2025-12-08 10:28:56'),
('VET002', 'UPDATE', 'Lab Test', 'Updated test result status', '192.168.1.102', '2025-12-08 10:28:56'),
('GOV002', 'GENERATE', 'Report', 'Generated MRL compliance report', '192.168.1.103', '2025-12-08 10:28:56'),
('VET003', 'DELETE', 'Prescription', 'Deleted old prescription', '192.168.1.104', '2025-12-08 10:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--
CREATE TABLE IF NOT EXISTS `batches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batch_id` varchar(100) NOT NULL UNIQUE,
  `product_name` varchar(255) DEFAULT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `mrl_status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `batch_id` (`batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `batches`
INSERT INTO `batches` (`batch_id`, `product_name`, `manufacturer`, `mrl_status`, `created_at`) VALUES
('BATCH-2024-001', 'A2 Milk', 'Green Pastures Dairy', 'compliant', '2025-12-08 10:28:56'),
('BATCH-2024-002', 'Organic Eggs', 'Happy Hens Poultry', 'compliant', '2025-12-08 10:28:56'),
('BATCH-2024-003', 'Goat Cheese', 'Golden Goat Farm', 'compliant', '2025-12-08 10:28:56'),
('BATCH-2024-004', 'Buffalo Ghee', 'Royal Buffalo Farm', 'compliant', '2025-12-08 10:28:56'),
('BATCH-2024-005', 'Pork Products', 'Modern Pig Farm', 'compliant', '2025-12-08 10:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `consultation_requests`
--
CREATE TABLE IF NOT EXISTS `consultation_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `farmer_id` varchar(50) DEFAULT NULL,
  `vet_id` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `consultation_requests`
INSERT INTO `consultation_requests` (`farmer_id`, `vet_id`, `description`, `status`, `created_at`) VALUES
('FARM-001', 'VET001', 'Concerned about low milk production', 'pending', '2025-12-08 10:28:56'),
('FARM-002', 'VET002', 'Poultry disease outbreak query', 'in_progress', '2025-12-08 10:28:56'),
('FARM-003', 'VET003', 'Vaccination schedule consultation', 'completed', '2025-12-08 10:28:56'),
('FARM-004', 'VET001', 'Buffalo breeding advice needed', 'pending', '2025-12-08 10:28:56'),
('FARM-005', 'VET004', 'Pig health management consultation', 'completed', '2025-12-08 10:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `farms`
--
CREATE TABLE IF NOT EXISTS `farms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `farm_id` varchar(50) NOT NULL UNIQUE,
  `name` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(10,8) DEFAULT NULL,
  `owner_name` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `mrl_status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `farm_id` (`farm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `farm_alerts`
--
CREATE TABLE IF NOT EXISTS `farm_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `farm_id` varchar(50) DEFAULT NULL,
  `alert_type` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `severity` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `farm_alerts`
INSERT INTO `farm_alerts` (`farm_id`, `alert_type`, `message`, `severity`, `created_at`) VALUES
('FARM-001', 'Medication', 'Withdrawal period expiring soon', 'high', '2025-12-08 10:28:57'),
('FARM-002', 'Disease', 'Monitor for coccidiosis', 'medium', '2025-12-08 10:28:57'),
('FARM-003', 'Maintenance', 'Equipment service required', 'low', '2025-12-08 10:28:57'),
('FARM-004', 'Compliance', 'MRL test due this month', 'high', '2025-12-08 10:28:57'),
('FARM-005', 'Inventory', 'Reorder antibiotics soon', 'medium', '2025-12-08 10:28:57');

-- --------------------------------------------------------

--
-- Table structure for table `farm_details`
--
CREATE TABLE IF NOT EXISTS `farm_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `farm_id` varchar(50) NOT NULL UNIQUE,
  `farm_name` varchar(255) NOT NULL,
  `owner_name` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(10,8) DEFAULT NULL,
  `animal_count` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `farm_id` (`farm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `farm_details`
INSERT INTO `farm_details` (`farm_id`, `farm_name`, `owner_name`, `location`, `state`, `latitude`, `longitude`, `animal_count`, `status`, `created_at`) VALUES
('FARM-001', 'Green Pastures Dairy', 'Rajesh Kumar', 'Pune', 'Maharashtra', 18.52040000, 73.85670000, 150, 'active', '2025-12-08 10:28:56'),
('FARM-002', 'Happy Hens Poultry', 'Priya Singh', 'Delhi', 'Delhi', 28.70410000, 77.10250000, 5000, 'active', '2025-12-08 10:28:56'),
('FARM-003', 'Golden Goat Farm', 'Vikram Patel', 'Bangalore', 'Karnataka', 12.97160000, 77.59460000, 200, 'active', '2025-12-08 10:28:56'),
('FARM-004', 'Royal Buffalo Farm', 'Anita Sharma', 'Kolkata', 'West Bengal', 22.57260000, 88.36390000, 100, 'active', '2025-12-08 10:28:56'),
('FARM-005', 'Modern Pig Farm', 'Suresh Joshi', 'Chennai', 'Tamil Nadu', 13.08270000, 80.27070000, 300, 'active', '2025-12-08 10:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `government_users`
--
CREATE TABLE IF NOT EXISTS `government_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gov_id` varchar(50) NOT NULL UNIQUE,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `tier` varchar(50) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `gov_id` (`gov_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `government_users`
INSERT INTO `government_users` (`gov_id`, `name`, `email`, `phone`, `tier`, `region`, `password`, `status`, `created_at`) VALUES
('GOV001', 'District Officer', 'district@agrisense.local', NULL, 'District', 'Maharashtra', 'fe01ce2a7fbac8fafaed7c982a04e229', 'active', '2025-12-08 10:28:56'),
('GOV002', 'State Officer', 'state@agrisense.local', NULL, 'State', 'Maharashtra', 'fe01ce2a7fbac8fafaed7c982a04e229', 'active', '2025-12-08 10:28:56'),
('GOV003', 'Central Officer', 'central@agrisense.local', NULL, 'Central', 'India', 'fe01ce2a7fbac8fafaed7c982a04e229', 'active', '2025-12-08 10:28:56'),
('GOV004', 'Deputy Commissioner', 'deputy@agrisense.local', NULL, 'District', 'Karnataka', 'fe01ce2a7fbac8fafaed7c982a04e229', 'active', '2025-12-08 10:28:56'),
('GOV005', 'State Director', 'director@agrisense.local', NULL, 'State', 'Tamil Nadu', 'fe01ce2a7fbac8fafaed7c982a04e229', 'active', '2025-12-08 10:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `health_history`
--
CREATE TABLE IF NOT EXISTS `health_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `animal_id` varchar(50) DEFAULT NULL,
  `vet_id` varchar(50) DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `health_history`
INSERT INTO `health_history` (`animal_id`, `vet_id`, `diagnosis`, `treatment`, `status`, `created_at`) VALUES
('COW-001', 'VET001', 'Bacterial infection', 'Oxytetracycline prescribed', 'recovered', '2025-12-08 10:28:56'),
('GOAT-102', 'VET002', 'Respiratory illness', 'Enrofloxacin course', 'under treatment', '2025-12-08 10:28:56'),
('BUFFALO-205', 'VET001', 'Digestive disorder', 'Probiotic supplement', 'improving', '2025-12-08 10:28:56'),
('CHICKEN-310', 'VET003', 'Viral infection', 'Supportive care', 'monitoring', '2025-12-08 10:28:56'),
('PIG-415', 'VET004', 'Pneumonia', 'Antibiotic therapy', 'recovered', '2025-12-08 10:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `mrl_lab_tests`
--
CREATE TABLE IF NOT EXISTS `mrl_lab_tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sample_id` varchar(100) NOT NULL UNIQUE,
  `sample_type` varchar(100) DEFAULT NULL,
  `lab_id` varchar(50) DEFAULT NULL,
  `test_parameters` longtext DEFAULT NULL,
  `result` varchar(50) DEFAULT NULL,
  `mrl_compliant` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `sample_id` (`sample_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `mrl_lab_tests`
INSERT INTO `mrl_lab_tests` (`sample_id`, `sample_type`, `lab_id`, `test_parameters`, `result`, `mrl_compliant`, `created_at`) VALUES
('LAB-TEST-001', 'Milk', 'FSSAI-001', '["Antibiotic Residue","Pesticide"]', 'Passed', 1, '2025-12-08 10:28:56'),
('LAB-TEST-002', 'Meat', 'ICAR-001', '["Heavy Metals","Bacterial Load"]', 'Passed', 1, '2025-12-08 10:28:56'),
('LAB-TEST-003', 'Milk', 'PVT-001', '["Antibiotic Residue"]', 'Passed', 1, '2025-12-08 10:28:56'),
('LAB-TEST-004', 'Meat', 'FSSAI-001', '["Hormone Residue","Pesticide"]', 'Passed', 1, '2025-12-08 10:28:56'),
('LAB-TEST-005', 'Milk', 'ICAR-001', '["Antibiotic Residue","Heavy Metals"]', 'Passed', 1, '2025-12-08 10:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `policies`
--
CREATE TABLE IF NOT EXISTS `policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `policy_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `mrl_limits` longtext DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `policies`
INSERT INTO `policies` (`policy_name`, `description`, `mrl_limits`, `status`, `created_by`, `created_at`) VALUES
('Antibiotic MRL Policy', 'Maximum residue limits for antibiotics', '{"milk":"0.1ppm","meat":"0.2ppm"}', 'active', 'GOV001', '2025-12-08 10:28:56'),
('Pesticide Control Policy', 'Guidelines for pesticide usage', '{"vegetables":"0.05ppm","milk":"0.01ppm"}', 'active', 'GOV002', '2025-12-08 10:28:56'),
('Hormone Regulation Policy', 'Restrictions on growth hormones', '{"beef":"0.0ppm","dairy":"0.0ppm"}', 'active', 'GOV003', '2025-12-08 10:28:56'),
('Heavy Metal Standards', 'Maximum heavy metal content limits', '{"lead":"0.1ppm","cadmium":"0.05ppm"}', 'active', 'GOV001', '2025-12-08 10:28:56'),
('AMU Tracking Guidelines', 'Antimicrobial usage tracking standards', '{"documentation":"mandatory","reporting":"quarterly"}', 'active', 'GOV002', '2025-12-08 10:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--
CREATE TABLE IF NOT EXISTS `prescriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prescription_id` varchar(50) NOT NULL UNIQUE,
  `animal_id` varchar(50) NOT NULL,
  `animal_owner` varchar(100) DEFAULT NULL,
  `animal_weight` decimal(8,2) DEFAULT NULL,
  `farm_id` varchar(50) DEFAULT NULL,
  `farm_lat` decimal(10,8) DEFAULT NULL,
  `farm_lng` decimal(10,8) DEFAULT NULL,
  `medicines` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `vet_id` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `prescription_id` (`prescription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_info`
--
CREATE TABLE IF NOT EXISTS `product_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(100) NOT NULL UNIQUE,
  `product_name` varchar(255) DEFAULT NULL,
  `mrl_status` varchar(20) DEFAULT NULL,
  `batch_number` varchar(100) DEFAULT NULL,
  `lab_test_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `product_info`
INSERT INTO `product_info` (`product_id`, `product_name`, `mrl_status`, `batch_number`, `lab_test_date`, `created_at`) VALUES
('PROD-001', 'Organic A2 Milk', 'Compliant', 'BATCH-2024-001', '2025-12-01', '2025-12-08 10:28:57'),
('PROD-002', 'Free-range Eggs', 'Compliant', 'BATCH-2024-002', '2025-12-02', '2025-12-08 10:28:57'),
('PROD-003', 'Artisan Goat Cheese', 'Compliant', 'BATCH-2024-003', '2025-12-03', '2025-12-08 10:28:57'),
('PROD-004', 'Premium Buffalo Ghee', 'Compliant', 'BATCH-2024-004', '2025-12-04', '2025-12-08 10:28:57'),
('PROD-005', 'Natural Pork Meat', 'Compliant', 'BATCH-2024-005', '2025-12-05', '2025-12-08 10:28:57');

-- --------------------------------------------------------

--
-- Table structure for table `safety_alerts`
--
CREATE TABLE IF NOT EXISTS `safety_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alert_id` varchar(100) NOT NULL UNIQUE,
  `product_name` varchar(255) DEFAULT NULL,
  `alert_level` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `alert_id` (`alert_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `safety_alerts`
INSERT INTO `safety_alerts` (`alert_id`, `product_name`, `alert_level`, `description`, `created_at`) VALUES
('ALERT-001', 'Oxytetracycline Batch X123', 'Low', 'Minor contamination detected', '2025-12-08 10:28:56'),
('ALERT-002', 'Enrofloxacin Supply', 'Medium', 'Recall from one batch', '2025-12-08 10:28:56'),
('ALERT-003', 'Amoxicillin Generic', 'Low', 'Quality check pending', '2025-12-08 10:28:56'),
('ALERT-004', 'Feed Supplement', 'High', 'Pesticide residue found', '2025-12-08 10:28:56'),
('ALERT-005', 'Vaccine Batch Y456', 'Low', 'Expiry date update', '2025-12-08 10:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `treatment_history`
--
CREATE TABLE IF NOT EXISTS `treatment_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `animal_id` varchar(50) DEFAULT NULL,
  `treatment_date` date DEFAULT NULL,
  `medicine_name` varchar(255) DEFAULT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `withdrawal_period` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `treatment_history`
INSERT INTO `treatment_history` (`animal_id`, `treatment_date`, `medicine_name`, `dosage`, `withdrawal_period`, `created_at`) VALUES
('COW-001', '2025-12-01', 'Oxytetracycline', '10mg/kg', 7, '2025-12-08 10:28:57'),
('GOAT-102', '2025-12-02', 'Enrofloxacin', '5mg/kg', 5, '2025-12-08 10:28:57'),
('BUFFALO-205', '2025-12-03', 'Penicillin', '2ml/kg', 3, '2025-12-08 10:28:57'),
('CHICKEN-310', '2025-12-04', 'Enrofloxacin', '10mg/l', 4, '2025-12-08 10:28:57'),
('PIG-415', '2025-12-05', 'Amoxicillin', '15mg/kg', 8, '2025-12-08 10:28:57');

-- --------------------------------------------------------

--
-- Table structure for table `veterinarians`
--
CREATE TABLE IF NOT EXISTS `veterinarians` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vet_id` varchar(50) NOT NULL UNIQUE,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `license_number` varchar(100) DEFAULT NULL UNIQUE,
  `specialization` varchar(100) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `vet_id` (`vet_id`),
  UNIQUE KEY `license_number` (`license_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `veterinarians`
INSERT INTO `veterinarians` (`vet_id`, `name`, `email`, `phone`, `license_number`, `specialization`, `experience`, `password`, `status`, `created_at`, `updated_at`) VALUES
('VET001', 'Dr. Sharma', 'dr.sharma@agrisense.local', '9876543210', 'LIC-2018-001', 'Dairy Specialist', 15, 'fe01ce2a7fbac8fafaed7c982a04e229', 'active', '2025-12-08 10:44:24', '2025-12-08 10:44:24'),
('VET002', 'Dr. Patel', 'dr.patel@agrisense.local', '9876543209', 'LIC-2019-002', 'Poultry Expert', 8, 'fe01ce2a7fbac8fafaed7c982a04e229', 'active', '2025-12-08 10:44:24', '2025-12-08 10:44:24'),
('VET003', 'Dr. Singh', 'dr.singh@agrisense.local', '9876543210', 'LIC-2019-001', 'Dairy Specialist', 12, 'fe01ce2a7fbac8fafaed7c982a04e229', 'active', '2025-12-08 10:44:24', '2025-12-08 10:44:24'),
('VET004', 'Dr. Kumar', 'dr.kumar@agrisense.local', '9876543211', 'LIC-2020-002', 'Swine Expert', 7, 'fe01ce2a7fbac8fafaed7c982a04e229', 'active', '2025-12-08 10:44:24', '2025-12-08 10:44:24'),
('VET005', 'Dr. Verma', 'dr.verma@agrisense.local', '9876543212', 'LIC-2021-003', 'Equine Specialist', 6, 'fe01ce2a7fbac8fafaed7c982a04e229', 'active', '2025-12-08 10:44:24', '2025-12-08 10:44:24'),
('VET006', 'Dr. Desai', 'dr.desai@agrisense.local', '9876543213', 'LIC-2018-004', 'Poultry Specialist', 10, 'fe01ce2a7fbac8fafaed7c982a04e229', 'active', '2025-12-08 10:44:24', '2025-12-08 10:44:24'),
('VET007', 'Dr. Gupta', 'dr.gupta@agrisense.local', '9876543214', 'LIC-2022-005', 'Aquaculture Expert', 4, 'fe01ce2a7fbac8fafaed7c982a04e229', 'active', '2025-12-08 10:44:24', '2025-12-08 10:44:24');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

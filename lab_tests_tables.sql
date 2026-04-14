-- =====================================================
-- LAB TEST TABLES FOR AGRISENSE
-- =====================================================

-- Table for lab tests
CREATE TABLE IF NOT EXISTS `lab_tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `farm_id` varchar(50) NOT NULL,
  `animal_id` varchar(50) DEFAULT NULL,
  `test_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` varchar(20) DEFAULT 'normal',
  `vet_id` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `test_results` longtext,
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `farm_id` (`farm_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for lab test samples
CREATE TABLE IF NOT EXISTS `lab_test_samples` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lab_test_id` int(11) NOT NULL,
  `sample_id` varchar(100) NOT NULL UNIQUE,
  `sample_type` varchar(100) NOT NULL,
  `collection_date` date NOT NULL,
  `collector_name` varchar(100) DEFAULT NULL,
  `quantity` varchar(50) DEFAULT NULL,
  `unit` varchar(20) DEFAULT 'ml',
  `status` varchar(50) DEFAULT 'collected',
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `sample_id` (`sample_id`),
  KEY `lab_test_id` (`lab_test_id`),
  FOREIGN KEY (`lab_test_id`) REFERENCES `lab_tests`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for lab test reports
CREATE TABLE IF NOT EXISTS `lab_test_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lab_test_id` int(11) NOT NULL,
  `sample_id` varchar(100) DEFAULT NULL,
  `farm_id` varchar(50) DEFAULT NULL,
  `lab_name` varchar(100) DEFAULT 'Agrisense Reference Lab',
  `technician` varchar(100) DEFAULT NULL,
  `test_results` longtext,
  `mrl_status` varchar(50) DEFAULT 'pending',
  `approval_notes` text,
  `approved_by` varchar(100) DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `approved_at` timestamp NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `lab_test_id` (`lab_test_id`),
  KEY `mrl_status` (`mrl_status`),
  FOREIGN KEY (`lab_test_id`) REFERENCES `lab_tests`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for farms (if not exists)
CREATE TABLE IF NOT EXISTS `farms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `farm_id` varchar(50) NOT NULL UNIQUE,
  `farm_name` varchar(255) NOT NULL,
  `state` varchar(100) DEFAULT NULL,
  `latitude` decimal(10, 8) DEFAULT NULL,
  `longitude` decimal(11, 8) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `has_alert` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `farm_id` (`farm_id`),
  KEY `state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample farms for heatmap
INSERT INTO `farms` (`farm_id`, `farm_name`, `state`, `latitude`, `longitude`) VALUES
('FARM-001', 'Green Pastures Dairy', 'Maharashtra', 18.5204, 73.8567),
('FARM-002', 'Happy Hens Poultry', 'Delhi', 28.7041, 77.1025),
('FARM-003', 'Golden Goat Farm', 'Karnataka', 12.9716, 77.5946),
('FARM-004', 'Royal Buffalo Farm', 'West Bengal', 22.5726, 88.3639),
('FARM-005', 'Organic Cow Dairy', 'Rajasthan', 26.9124, 75.7873),
('FARM-006', 'Punjab Milk Farm', 'Punjab', 31.6340, 74.8723),
('FARM-007', 'Hyderabad Poultry', 'Telangana', 17.3850, 78.4867),
('FARM-008', 'Chennai Dairy', 'Tamil Nadu', 13.0827, 80.2707),
('FARM-009', 'Ahmedabad Livestock', 'Gujarat', 23.0225, 72.5714),
('FARM-010', 'Lucknow Farm', 'Uttar Pradesh', 26.8467, 80.9462);

-- Sample lab tests
INSERT INTO `lab_tests` (`farm_id`, `animal_id`, `test_type`, `description`, `priority`, `vet_id`, `status`) VALUES
('FARM-001', 'COW-001', 'Residue Panel', 'Check for antibiotic residues', 'high', 'VET001', 'pending'),
('FARM-002', 'CHICKEN-101', 'MRL Screening', 'Multi-residue screening', 'normal', 'VET002', 'sample_collected'),
('FARM-003', 'GOAT-205', 'Withdrawal Check', 'Verify withdrawal period', 'high', 'VET003', 'report_generated'),
('FARM-004', 'BUFFALO-310', 'Residue Panel', 'Heavy metal and residue check', 'normal', 'VET004', 'approved'),
('FARM-005', 'COW-415', 'AMU Analysis', 'Antimicrobial usage assessment', 'normal', 'VET005', 'pending');

-- Sample lab test samples
INSERT INTO `lab_test_samples` (`lab_test_id`, `sample_id`, `sample_type`, `collection_date`, `collector_name`, `quantity`, `status`) VALUES
(1, 'SAMPLE-20251208000001-1234', 'Milk', '2025-12-08', 'Dr. Sharma', '500', 'collected'),
(2, 'SAMPLE-20251208000002-5678', 'Tissue', '2025-12-08', 'Dr. Patel', '100', 'analyzed'),
(3, 'SAMPLE-20251208000003-9012', 'Serum', '2025-12-07', 'Dr. Singh', '50', 'tested'),
(4, 'SAMPLE-20251208000004-3456', 'Milk', '2025-12-06', 'Dr. Kumar', '500', 'reported'),
(5, 'SAMPLE-20251208000005-7890', 'Organ', '2025-12-08', 'Dr. Verma', '200', 'collected');

-- Sample lab test reports
INSERT INTO `lab_test_reports` (`lab_test_id`, `sample_id`, `farm_id`, `lab_name`, `technician`, `test_results`, `mrl_status`, `report_date`) VALUES
(3, 'SAMPLE-20251208000003-9012', 'FARM-003', 'Agrisense Reference Lab', 'Tech-001', '[{"chemical":"Oxytetracycline","detected_value":"0.05","mrl_limit":"0.1","status":"compliant"},{"chemical":"Amoxicillin","detected_value":"0.02","mrl_limit":"0.075","status":"compliant"}]', 'approved', '2025-12-08'),
(4, 'SAMPLE-20251208000004-3456', 'FARM-004', 'Agrisense Reference Lab', 'Tech-002', '[{"chemical":"Enrofloxacin","detected_value":"0.15","mrl_limit":"0.1","status":"non-compliant"}]', 'rejected', '2025-12-07');

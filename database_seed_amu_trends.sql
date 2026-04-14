-- Seed + schema for AMU trend analysis
-- Creates/ensures `amu_records`, `medicine_usage_trends`, `medicine_species_stats` exist and inserts sample rows

-- Ensure amu_records exists (extended columns)
CREATE TABLE IF NOT EXISTS `amu_records` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `prescription_id` INT DEFAULT NULL,
  `prescription_item_id` INT DEFAULT NULL,
  `medicine_id` VARCHAR(50) NOT NULL,
  `medicine_name` VARCHAR(255) DEFAULT NULL,
  `medicine_type` VARCHAR(255) DEFAULT NULL,
  `amu_category` VARCHAR(50) DEFAULT NULL,
  `amount` DECIMAL(14,4) DEFAULT 0,
  `unit` VARCHAR(50) DEFAULT NULL,
  `farm_id` VARCHAR(50) DEFAULT NULL,
  `location` VARCHAR(255) DEFAULT NULL,
  `state` VARCHAR(100) DEFAULT NULL,
  `latitude` DECIMAL(10,8) DEFAULT NULL,
  `longitude` DECIMAL(10,8) DEFAULT NULL,
  `species` VARCHAR(100) DEFAULT NULL,
  `reason` VARCHAR(255) DEFAULT NULL,
  `frequency_per_month` INT DEFAULT 0,
  `usage_rate` VARCHAR(50) DEFAULT 'low',
  `trend` VARCHAR(50) DEFAULT 'stable',
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ensure medicine_usage_trends exists
CREATE TABLE IF NOT EXISTS `medicine_usage_trends` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `medicine_id` VARCHAR(50) NOT NULL,
  `medicine_name` VARCHAR(255) NOT NULL,
  `species` VARCHAR(100) NOT NULL,
  `month` VARCHAR(7) NOT NULL,
  `state` VARCHAR(100) DEFAULT NULL,
  `usage_count` INT DEFAULT 0,
  `total_amount` DECIMAL(14,4) DEFAULT 0,
  `unit` VARCHAR(50) DEFAULT NULL,
  `avg_dosage` DECIMAL(10,2) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_med_species_month_state (medicine_id, species, month, state)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ensure medicine_species_stats exists
CREATE TABLE IF NOT EXISTS `medicine_species_stats` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `medicine_id` VARCHAR(50) NOT NULL,
  `medicine_name` VARCHAR(255) NOT NULL,
  `species` VARCHAR(100) NOT NULL,
  `total_usage_count` INT DEFAULT 0,
  `total_amount_used` DECIMAL(14,4) DEFAULT 0,
  `unit` VARCHAR(50) DEFAULT NULL,
  `state` VARCHAR(100) DEFAULT NULL,
  `region` VARCHAR(100) DEFAULT NULL,
  `last_used` TIMESTAMP NULL,
  `avg_usage_per_prescription` DECIMAL(10,2) DEFAULT 0,
  `trend_direction` VARCHAR(20) DEFAULT 'stable',
  `trend_percentage` DECIMAL(5,2) DEFAULT 0,
  `primary_reason` VARCHAR(255) DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_med_species_state (medicine_id, species, state)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert 10 sample amu_records rows (varied states, species, months via created_at)
INSERT INTO amu_records (prescription_id, prescription_item_id, medicine_id, medicine_name, medicine_type, amu_category, amount, unit, farm_id, location, state, latitude, longitude, species, reason, frequency_per_month, usage_rate, trend, notes, created_at)
VALUES
(2001, 11, 'MED-AMOX',  'Amoxicillin 250mg',           'beta-lactam',     'VHIA',  500.0000,   'mg', 'FARM-101', 'Bengaluru North', 'Karnataka', 13.0298, 77.5895, 'Cattle', 'Respiratory infection', 2, 'medium', 'increasing', 'Given orally', '2025-09-05 10:12:00'),
(2002, 12, 'MED-CIPRO','Ciprofloxacin 250mg',         'fluoroquinolone', 'VCIA',  250.0000,   'mg', 'FARM-102', 'Pune',            'Maharashtra', 18.5204, 73.8567, 'Goat', 'Gastrointestinal infection', 1, 'low', 'stable', 'IM injection', '2025-10-12 09:05:00'),
(2003, 13, 'MED-OXY',  'Oxytetracycline 200mg',       'tetracycline',    'VHIA', 1000.0000,   'mg', 'FARM-103', 'Madurai',         'Tamil Nadu',  9.9252, 78.1198, 'Cattle', 'Mastitis', 3, 'high', 'increasing', 'Water soln', '2025-11-02 14:22:00'),
(2004, 14, 'MED-ENRO', 'Enrofloxacin 50mg',           'fluoroquinolone', 'VCIA',  500.0000,   'mg', 'FARM-104', 'Kanpur',          'Uttar Pradesh',26.4499, 80.3319, 'Poultry', 'Fowl cholera', 4, 'high', 'increasing', 'Oral', '2025-11-18 08:30:00'),
(2005, 15, 'MED-CEFT', 'Ceftiofur 250mg',             'cephalosporin',   'VCIA',  250.0000,   'mg', 'FARM-105', 'Kozhikode',       'Kerala',      11.2588, 75.7804, 'Buffalo', 'Septicemia', 1, 'low', 'stable', '', '2025-09-22 11:45:00'),
(2006, 16, 'MED-PEN',  'Procaine Penicillin 1M IU',   'penicillin',      'VHIA',1000000.0000, 'IU', 'FARM-106', 'Lucknow',         'Uttar Pradesh',26.8467, 80.9462, 'Cattle', 'Wound infection', 2, 'very_high', 'decreasing', 'Slow IM', '2025-08-30 16:02:00'),
(2007, 17, 'MED-TMPS', 'Trimethoprim-Sulfa 160/800mg', 'sulfonamide',     'VHIA',  500.0000,   'mg', 'FARM-107', 'Indore',          'Madhya Pradesh',22.7196, 75.8577, 'Goat', 'Urinary infection', 1, 'medium', 'stable', '', '2025-10-03 13:00:00'),
(2008, 18, 'MED-AZI',  'Azithromycin 200mg',          'macrolide',       'VCIA',  200.0000,   'mg', 'FARM-108', 'Hyderabad',       'Telangana',   17.3850, 78.4867, 'Poultry', 'Respiratory', 2, 'medium', 'increasing', '', '2025-11-25 09:10:00'),
(2009, 19, 'MED-COLI', 'Colistin 100mg',              'polymyxin',       'VCIA',  100.0000,   'mg', 'FARM-109', 'Ahmedabad',       'Gujarat',     23.0225, 72.5714, 'Poultry', 'Enteritis', 5, 'very_high', 'increasing', 'Critical treatment', '2025-11-28 07:50:00'),
(2010, 20, 'MED-MULT', 'Multivitamin Oral 20ml',      'vitamin',         'VIA',    20.0000,   'ml', 'FARM-110', 'Chennai',         'Tamil Nadu',  13.0827, 80.2707, 'Buffalo', 'Supportive care', 1, 'low', 'stable', '', '2025-10-10 12:00:00');

-- Insert matching summary rows into medicine_usage_trends (monthly examples)
INSERT INTO medicine_usage_trends (medicine_id, medicine_name, species, month, state, usage_count, total_amount, unit, avg_dosage)
VALUES
('MED-AMOX','Amoxicillin 250mg','Cattle','2025-09','Karnataka',12,6000.0000,'mg',500.00),
('MED-OXY','Oxytetracycline 200mg','Cattle','2025-11','Tamil Nadu',8,8000.0000,'mg',1000.00),
('MED-ENRO','Enrofloxacin 50mg','Poultry','2025-11','Uttar Pradesh',25,12500.0000,'mg',500.00),
('MED-COLI','Colistin 100mg','Poultry','2025-11','Gujarat',30,3000.0000,'mg',100.00),
('MED-PEN','Procaine Penicillin 1M IU','Cattle','2025-08','Uttar Pradesh',5,5000000.0000,'IU',1000000.00);

-- Insert a few rows into medicine_species_stats
INSERT INTO medicine_species_stats (medicine_id, medicine_name, species, total_usage_count, total_amount_used, unit, state, avg_usage_per_prescription, trend_direction, trend_percentage, primary_reason, last_used)
VALUES
('MED-AMOX','Amoxicillin 250mg','Cattle',120,60000.0000,'mg','Karnataka',500.00,'increasing',12.50,'Respiratory infection','2025-09-05 10:12:00'),
('MED-OXY','Oxytetracycline 200mg','Cattle',40,40000.0000,'mg','Tamil Nadu',1000.00,'increasing',15.00,'Mastitis','2025-11-02 14:22:00');

-- End of seed
COMMIT;

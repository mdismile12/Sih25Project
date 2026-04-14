-- Migration: create amu_records table for Antimicrobial Use tracking
CREATE TABLE IF NOT EXISTS `amu_records` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `prescription_id` INT DEFAULT NULL,
  `prescription_item_id` INT DEFAULT NULL,
  `medicine_id` VARCHAR(50) NOT NULL,
  `medicine_name` VARCHAR(255) DEFAULT NULL,
  `medicine_type` VARCHAR(255) DEFAULT NULL,
  `amu_category` VARCHAR(50) DEFAULT NULL, -- VCIA, VHIA, VIA
  `amount` DECIMAL(14,4) DEFAULT 0, -- numeric amount (base unit)
  `unit` VARCHAR(50) DEFAULT NULL, -- mg, ml, IU etc
  `farm_id` VARCHAR(50) DEFAULT NULL,
  `location` VARCHAR(255) DEFAULT NULL,
  `state` VARCHAR(100) DEFAULT NULL,
  `latitude` DECIMAL(10,8) DEFAULT NULL,
  `longitude` DECIMAL(10,8) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_prescription_id (prescription_id),
  INDEX idx_medicine_id (medicine_id),
  INDEX idx_state (state)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migration: Extended AMU tracking with species, reason, and frequency
-- Add columns to amu_records for enhanced tracking

ALTER TABLE `amu_records` 
ADD COLUMN `species` VARCHAR(100) DEFAULT NULL AFTER `medicine_type`,
ADD COLUMN `reason` VARCHAR(255) DEFAULT NULL AFTER `species`,
ADD COLUMN `frequency_per_month` INT DEFAULT 0 AFTER `reason`,
ADD COLUMN `usage_rate` VARCHAR(50) DEFAULT 'low' AFTER `frequency_per_month`,
ADD COLUMN `trend` VARCHAR(50) DEFAULT 'stable' AFTER `usage_rate`,
ADD COLUMN `notes` TEXT DEFAULT NULL AFTER `trend`;

-- Create indexes for better query performance
CREATE INDEX idx_species ON `amu_records` (species);
CREATE INDEX idx_reason ON `amu_records` (reason);
CREATE INDEX idx_usage_rate ON `amu_records` (usage_rate);
CREATE INDEX idx_trend ON `amu_records` (trend);
CREATE INDEX idx_medicine_species ON `amu_records` (medicine_id, species);
CREATE INDEX idx_state_species ON `amu_records` (state, species);

-- Create a new table for medicine-species usage statistics (for trend analysis)
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
  `trend_direction` VARCHAR(20) DEFAULT 'stable', -- increasing, decreasing, stable
  `trend_percentage` DECIMAL(5,2) DEFAULT 0,
  `primary_reason` VARCHAR(255) DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_medicine_species (medicine_id, species),
  INDEX idx_state (state),
  INDEX idx_trend_direction (trend_direction),
  UNIQUE KEY unique_med_species_state (medicine_id, species, state)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create table for monthly medicine usage trends
CREATE TABLE IF NOT EXISTS `medicine_usage_trends` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `medicine_id` VARCHAR(50) NOT NULL,
  `medicine_name` VARCHAR(255) NOT NULL,
  `species` VARCHAR(100) NOT NULL,
  `month` VARCHAR(7) NOT NULL, -- YYYY-MM format
  `state` VARCHAR(100) DEFAULT NULL,
  `usage_count` INT DEFAULT 0,
  `total_amount` DECIMAL(14,4) DEFAULT 0,
  `unit` VARCHAR(50) DEFAULT NULL,
  `avg_dosage` DECIMAL(10,2) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_medicine_month (medicine_id, month),
  INDEX idx_species_month (species, month),
  INDEX idx_state_month (state, month),
  UNIQUE KEY unique_med_species_month_state (medicine_id, species, month, state)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

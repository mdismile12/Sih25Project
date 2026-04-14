-- Database migration: Add prescription_items table and update prescriptions table
-- This file adds the missing tables and columns required for the prescription system

-- First, let's check and add missing columns to prescriptions table if needed
ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS animal_type VARCHAR(50) DEFAULT NULL;
ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS diagnosis TEXT DEFAULT NULL;
ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS symptoms TEXT DEFAULT NULL;
ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS administration_frequency VARCHAR(100) DEFAULT NULL;
ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS administration_time VARCHAR(100) DEFAULT NULL;
ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS duration_days INT DEFAULT 7;

-- Create prescription_items table to store individual medicines in a prescription
CREATE TABLE IF NOT EXISTS `prescription_items` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `prescription_id` INT NOT NULL,
  `medicine_id` VARCHAR(50) NOT NULL,
  `dosage_rate` DECIMAL(8,2) NOT NULL,
  `body_weight` DECIMAL(8,2) NOT NULL,
  `calculated_dosage` DECIMAL(8,2) NOT NULL,
  `dosage_unit` VARCHAR(20) DEFAULT 'mg',
  `frequency` VARCHAR(100) NOT NULL,
  `duration_days` INT DEFAULT 7,
  `withdrawal_period_days` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions`(`id`) ON DELETE CASCADE,
  INDEX idx_prescription_id (prescription_id),
  INDEX idx_medicine_id (medicine_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_prescriptions_farm_id ON prescriptions(farm_id);
CREATE INDEX IF NOT EXISTS idx_prescriptions_vet_id ON prescriptions(vet_id);
CREATE INDEX IF NOT EXISTS idx_prescriptions_status ON prescriptions(status);
CREATE INDEX IF NOT EXISTS idx_prescriptions_created_at ON prescriptions(created_at);

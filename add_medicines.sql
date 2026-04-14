-- ============================================
-- MEDICINES TABLE & SAMPLE DATA
-- ============================================
-- This file adds the medicines table with sample data
-- Run this in phpMyAdmin to populate medicines

USE `agrisense_db`;

-- Drop existing table if exists
DROP TABLE IF EXISTS `medicines`;

-- Create medicines table
CREATE TABLE `medicines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_id` varchar(50) NOT NULL UNIQUE,
  `name` varchar(255) NOT NULL,
  `generic_name` varchar(255),
  `type` varchar(100),
  `description` text,
  `dosage_rate` decimal(10,2) NOT NULL,
  `dosage_unit` varchar(50) DEFAULT 'mg/kg',
  `mrl_limit` decimal(10,2),
  `mrl_unit` varchar(50),
  `withdrawal_period_days` int(11) DEFAULT 0,
  `approved` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `medicine_id` (`medicine_id`),
  KEY `type` (`type`),
  KEY `approved` (`approved`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SAMPLE MEDICINES DATA
-- ============================================
INSERT INTO `medicines` 
(`medicine_id`, `name`, `generic_name`, `type`, `description`, `dosage_rate`, `dosage_unit`, `mrl_limit`, `mrl_unit`, `withdrawal_period_days`, `approved`) 
VALUES

-- ANTIBIOTICS - Beta-lactams
('MED-20250101001', 'Amoxycillin', 'Amoxicillin trihydrate', 'Antibiotic - Beta-lactam', 'Beta-lactam antibiotic for bacterial infections', 15.00, 'mg/kg', 4.00, 'mg/L (milk)', 10, 1),
('MED-20250101002', 'Amoxycillin-Clavulanic Acid', 'Amoxicillin + Clavulanic acid', 'Antibiotic - Beta-lactam', 'Broad-spectrum with beta-lactamase inhibitor', 12.50, 'mg/kg', 4.00, 'mg/L (milk)', 12, 1),
('MED-20250101003', 'Ampicillin', 'Ampicillin sodium', 'Antibiotic - Beta-lactam', 'Semi-synthetic beta-lactam for gram-positive bacteria', 10.00, 'mg/kg', 5.00, 'mg/L (milk)', 8, 1),

-- ANTIBIOTICS - Tetracyclines
('MED-20250101004', 'Oxytetracycline', 'Oxytetracycline hydrochloride', 'Antibiotic - Tetracycline', 'Broad-spectrum tetracycline antibiotic', 20.00, 'mg/kg', 100.00, 'mg/L (milk)', 21, 1),
('MED-20250101005', 'Tetracycline', 'Tetracycline hydrochloride', 'Antibiotic - Tetracycline', 'Broad-spectrum antibiotic for respiratory infections', 15.00, 'mg/kg', 200.00, 'mg/L (milk)', 14, 1),
('MED-20250101006', 'Doxycycline', 'Doxycycline hyclate', 'Antibiotic - Tetracycline', 'Long-acting tetracycline derivative', 10.00, 'mg/kg', 100.00, 'mg/L (milk)', 7, 1),

-- ANTIBIOTICS - Fluoroquinolones
('MED-20250101007', 'Enrofloxacin', 'Enrofloxacin', 'Antibiotic - Fluoroquinolone', 'Broad-spectrum fluoroquinolone for respiratory & urinary infections', 10.00, 'mg/kg', 30.00, 'mg/L (milk)', 8, 1),
('MED-20250101008', 'Ciprofloxacin', 'Ciprofloxacin hydrochloride', 'Antibiotic - Fluoroquinolone', 'Potent fluoroquinolone for gram-negative bacteria', 20.00, 'mg/kg', 25.00, 'mg/L (milk)', 5, 1),
('MED-20250101009', 'Levofloxacin', 'Levofloxacin', 'Antibiotic - Fluoroquinolone', 'Left-handed fluoroquinolone with improved properties', 15.00, 'mg/kg', 30.00, 'mg/L (milk)', 7, 1),

-- ANTIBIOTICS - Macrolides
('MED-20250101010', 'Erythromycin', 'Erythromycin ethylsuccinate', 'Antibiotic - Macrolide', 'Macrolide antibiotic for gram-positive bacteria', 10.00, 'mg/kg', 40.00, 'mg/L (milk)', 5, 1),
('MED-20250101011', 'Azithromycin', 'Azithromycin dihydrate', 'Antibiotic - Macrolide', 'Broad-spectrum macrolide with extended half-life', 5.00, 'mg/kg', 50.00, 'mg/L (milk)', 3, 1),
('MED-20250101012', 'Tylosin', 'Tylosin tartrate', 'Antibiotic - Macrolide', 'Veterinary macrolide for respiratory disease', 10.00, 'mg/kg', 100.00, 'mg/L (milk)', 7, 1),

-- ANTIBIOTICS - Aminoglycosides
('MED-20250101013', 'Gentamicin', 'Gentamicin sulfate', 'Antibiotic - Aminoglycoside', 'Aminoglycoside for severe gram-negative infections', 4.00, 'mg/kg', 110.00, 'mg/L (milk)', 10, 1),
('MED-20250101014', 'Streptomycin', 'Streptomycin sulfate', 'Antibiotic - Aminoglycoside', 'Aminoglycoside for mycobacterial infections', 10.00, 'mg/kg', 200.00, 'mg/L (milk)', 30, 1),
('MED-20250101015', 'Neomycin', 'Neomycin sulfate', 'Antibiotic - Aminoglycoside', 'Topical and oral aminoglycoside', 7.00, 'mg/kg', 1500.00, 'mg/L (milk)', 0, 1),

-- ANTIBIOTICS - Others
('MED-20250101016', 'Chloramphenicol', 'Chloramphenicol', 'Antibiotic - Phenicol', 'Broad-spectrum with MRL concerns', 15.00, 'mg/kg', 0.30, 'mg/L (milk)', 30, 1),
('MED-20250101017', 'Sulfamethoxazole-Trimethoprim', 'Co-trimoxazole', 'Antibiotic - Sulfonamide', 'Synergistic sulfonamide combination', 25.00, 'mg/kg', 100.00, 'mg/L (milk)', 7, 1),
('MED-20250101018', 'Spectinomycin', 'Spectinomycin hydrochloride', 'Antibiotic - Other', 'Aminocyclitol for gram-negative coverage', 15.00, 'mg/kg', 200.00, 'mg/L (milk)', 5, 1),

-- ANTI-INFECTIVE - Antiprotozoals
('MED-20250101019', 'Metronidazole', 'Metronidazole', 'Anti-infective - Antiprotozal', 'Effective against anaerobes and protozoa', 20.00, 'mg/kg', 10.00, 'mg/L (milk)', 3, 1),
('MED-20250101020', 'Amprolium', 'Amprolium hydrochloride', 'Anti-infective - Coccidiostat', 'Specific for coccidian parasites', 5.00, 'mg/kg', 500.00, 'mg/L (milk)', 5, 1),

-- ANTI-INFLAMMATORY & ANALGESIC
('MED-20250101021', 'Diclofenac', 'Diclofenac sodium', 'NSAID - Anti-inflammatory', 'Potent NSAID for inflammation and pain', 1.00, 'mg/kg', 0.10, 'mg/L (milk)', 2, 0),
('MED-20250101022', 'Ibuprofen', 'Ibuprofen', 'NSAID - Anti-inflammatory', 'Common anti-inflammatory with lower MRL concern', 10.00, 'mg/kg', 50.00, 'mg/L (milk)', 1, 1),
('MED-20250101023', 'Meloxicam', 'Meloxicam', 'NSAID - Anti-inflammatory', 'Long-acting selective COX-2 inhibitor', 0.5, 'mg/kg', 10.00, 'mg/L (milk)', 3, 1),
('MED-20250101024', 'Acetylsalicylic Acid', 'Aspirin', 'NSAID - Anti-inflammatory', 'Mild analgesic and anti-inflammatory', 25.00, 'mg/kg', 100.00, 'mg/L (milk)', 1, 1),

-- ANTHELMINTIC (ANTIPARASITIC)
('MED-20250101025', 'Albendazole', 'Albendazole', 'Anthelmintic - Broad-spectrum', 'Broad-spectrum benzimidazole for worms', 15.00, 'mg/kg', 100.00, 'mg/L (milk)', 14, 1),
('MED-20250101026', 'Ivermectin', 'Ivermectin', 'Antiparasitic - Macrocyclic lactone', 'For internal and external parasites', 0.2, 'mg/kg', 10.00, 'mg/L (milk)', 28, 1),
('MED-20250101027', 'Mebendazole', 'Mebendazole', 'Anthelmintic - Benzimidazole', 'Benzimidazole for nematodes', 10.00, 'mg/kg', 50.00, 'mg/L (milk)', 7, 1),
('MED-20250101028', 'Levamisole', 'Levamisole hydrochloride', 'Anthelmintic - Phenolic', 'Immunomodulator and anthelmintic', 8.00, 'mg/kg', 10.00, 'mg/L (milk)', 10, 1),

-- CARDIOVASCULAR
('MED-20250101029', 'Furosemide', 'Furosemide', 'Diuretic - Loop', 'Loop diuretic for fluid overload', 1.00, 'mg/kg', 5.00, 'mg/L (milk)', 3, 1),
('MED-20250101030', 'Atenolol', 'Atenolol', 'Cardiovascular - Beta-blocker', 'Beta-1 selective blocker', 0.5, 'mg/kg', 10.00, 'mg/L (milk)', 2, 1),

-- GASTROINTESTINAL
('MED-20250101031', 'Omeprazole', 'Omeprazole', 'GI - Proton pump inhibitor', 'Gastric acid suppressor', 1.00, 'mg/kg', 10.00, 'mg/L (milk)', 2, 1),
('MED-20250101032', 'Metoclopramide', 'Metoclopramide hydrochloride', 'GI - Prokinetic', 'Enhances gastric motility', 0.1, 'mg/kg', 10.00, 'mg/L (milk)', 1, 1),

-- CORTICOSTEROIDS
('MED-20250101033', 'Dexamethasone', 'Dexamethasone sodium phosphate', 'Corticosteroid', 'Long-acting glucocorticoid', 0.05, 'mg/kg', 0.50, 'mg/L (milk)', 7, 1),
('MED-20250101034', 'Prednisolone', 'Prednisolone', 'Corticosteroid', 'Intermediate-acting corticosteroid', 1.00, 'mg/kg', 10.00, 'mg/L (milk)', 3, 1),

-- SUPPLEMENTS & VITAMINS
('MED-20250101035', 'Calcium Borogluconate', 'Calcium borogluconate 40%', 'Supplement', 'Calcium for hypocalcemia management', 100.00, 'ml/dose', 1000.00, 'mg/L (milk)', 0, 1),
('MED-20250101036', 'Vitamin B12', 'Cyanocobalamin', 'Vitamin', 'Essential B-complex vitamin', 1000.00, 'IU/kg', 10.00, 'mg/L (milk)', 0, 1),
('MED-20250101037', 'Multivitamin Supplement', 'Vitamin A, D, E complex', 'Supplement', 'Broad-spectrum vitamin support', 5000.00, 'IU/kg', 1000.00, 'mg/L (milk)', 0, 1),

-- VACCINES (INFORMATIONAL - Usually not dosed by mg/kg)
('MED-20250101038', 'FMD Vaccine', 'Foot and Mouth Disease', 'Vaccine', 'Inactivated polyvalent FMD vaccine', 5.00, 'ml/dose', 0.00, 'mg/L (milk)', 0, 1),
('MED-20250101039', 'Brucellosis Vaccine', 'Brucella abortus S19', 'Vaccine', 'Live attenuated brucellosis vaccine', 1.00, 'ml/dose', 0.00, 'mg/L (milk)', 0, 1),

-- IMMUNOMODULATORS
('MED-20250101040', 'Interferon-alpha', 'Interferon alpha', 'Immunomodulator', 'Enhances innate immunity', 1000.00, 'IU/kg', 0.00, 'mg/L (milk)', 0, 1);

-- Verify insertion
SELECT COUNT(*) as total_medicines FROM medicines;
SELECT * FROM medicines ORDER BY type, name;

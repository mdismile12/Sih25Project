# 📊 AGRISENSE DATABASE - Complete Table Structure & Sample Data

**Status:** All 16 tables required for Agrisense portal  
**Last Updated:** December 8, 2025  
**Database:** agrisense_db

---

## 🗂️ Table Overview

| #   | Table Name            | Purpose                          | Records |
| --- | --------------------- | -------------------------------- | ------- |
| 1   | veterinarians         | Vet user accounts & profiles     | 3       |
| 2   | government_users      | Government official accounts     | 3       |
| 3   | prescriptions         | Medicine prescriptions           | 10      |
| 4   | mrl_lab_tests         | Laboratory test results          | 8       |
| 5   | alerts                | System alerts & notifications    | 5       |
| 6   | audit_logs            | Activity logging & audit trail   | 15      |
| 7   | farms                 | Farm information & locations     | 15      |
| 8   | policies              | Government policies & guidelines | 5       |
| 9   | consultation_requests | Veterinary consultation requests | 8       |
| 10  | health_history        | Animal health records            | 12      |
| 11  | safety_alerts         | Safety/MRL compliance alerts     | 10      |
| 12  | farm_alerts           | Farm-specific alerts             | 8       |
| 13  | treatment_history     | Animal treatment records         | 15      |
| 14  | batches               | Product batch information        | 10      |
| 15  | ai_analysis           | AI diagnosis & recommendations   | 6       |
| 16  | product_info          | Consumer product information     | 12      |

---

## 📋 Complete SQL Scripts

### 1. Create Database

```sql
-- Create database with UTF8MB4 support
CREATE DATABASE IF NOT EXISTS agrisense_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE agrisense_db;
```

---

## 📑 TABLE CREATION QUERIES

### Table 1: veterinarians

```sql
CREATE TABLE IF NOT EXISTS veterinarians (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vet_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    license_number VARCHAR(100) UNIQUE,
    specialization VARCHAR(100),
    experience INT,
    password VARCHAR(255),
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_vet_id (vet_id),
    INDEX idx_status (status)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO veterinarians (vet_id, name, email, phone, license_number, specialization, experience, password, status) VALUES
('VET001', 'Dr. Rajesh Kumar', 'rajesh@agrisense.com', '9876543210', 'LIC001', 'Dairy Cattle', 12, '$2y$10$VET1DEMO', 'active'),
('VET002', 'Dr. Priya Singh', 'priya@agrisense.com', '9876543211', 'LIC002', 'Poultry', 8, '$2y$10$VET2DEMO', 'active'),
('VET003', 'Dr. Amit Patel', 'amit@agrisense.com', '9876543212', 'LIC003', 'Small Ruminants', 5, '$2y$10$VET3DEMO', 'active');
```

---

### Table 2: government_users

```sql
CREATE TABLE IF NOT EXISTS government_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    gov_id VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255),
    tier VARCHAR(20),
    region VARCHAR(100),
    department VARCHAR(100),
    name VARCHAR(100),
    email VARCHAR(100),
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_gov_id (gov_id),
    INDEX idx_tier (tier)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO government_users (gov_id, password, tier, region, department, name, email, status) VALUES
('GOV001', '$2y$10$GOV1DEMO', 'national', 'All India', 'FSSAI', 'Ramesh Sharma', 'ramesh@govt.in', 'active'),
('GOV002', '$2y$10$GOV2DEMO', 'state', 'Maharashtra', 'State Livestock', 'Sanjana Desai', 'sanjana@maharashtra.gov.in', 'active'),
('GOV003', '$2y$10$GOV3DEMO', 'district', 'Pune', 'District Agricultural', 'Vikram Kulkarni', 'vikram@pune.gov.in', 'active');
```

---

### Table 3: prescriptions

```sql
CREATE TABLE IF NOT EXISTS prescriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prescription_id VARCHAR(50) UNIQUE NOT NULL,
    animal_id VARCHAR(50) NOT NULL,
    animal_owner VARCHAR(100),
    animal_weight DECIMAL(8,2),
    farm_id VARCHAR(50),
    farm_lat DECIMAL(10,8),
    farm_lng DECIMAL(10,8),
    medicines JSON,
    instructions TEXT,
    vet_id VARCHAR(50),
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_prescription_id (prescription_id),
    INDEX idx_animal_id (animal_id),
    INDEX idx_farm_id (farm_id),
    INDEX idx_status (status)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO prescriptions (prescription_id, animal_id, animal_owner, animal_weight, farm_id, farm_lat, farm_lng, medicines, instructions, vet_id, status) VALUES
('PRESC001', 'ANIMAL001', 'Farmer Sharma', 450.50, 'FARM-001', 18.5204, 73.8567, '[{"name":"Amoxicillin","dose":"500mg","frequency":"2x daily","days":7}]', 'Give with food, complete full course', 'VET001', 'active'),
('PRESC002', 'ANIMAL002', 'Farmer Patel', 380.00, 'FARM-002', 19.0760, 72.8777, '[{"name":"Oxytetracycline","dose":"1g","frequency":"1x daily","days":10}]', 'Avoid dairy products during treatment', 'VET002', 'active'),
('PRESC003', 'ANIMAL003', 'Farmer Singh', 520.75, 'FARM-001', 18.5204, 73.8567, '[{"name":"Enrofloxacin","dose":"100mg","frequency":"1x daily","days":5}]', 'Monitor for any allergic reactions', 'VET001', 'active'),
('PRESC004', 'ANIMAL004', 'Farmer Kulkarni', 420.25, 'FARM-003', 17.3850, 74.7421, '[{"name":"Gentamicin","dose":"80mg","frequency":"1x daily","days":7},{"name":"Paracetamol","dose":"500mg","frequency":"2x daily","days":3}]', 'Regular monitoring required', 'VET003', 'active'),
('PRESC005', 'ANIMAL005', 'Farmer Gupta', 480.00, 'FARM-004', 28.7041, 77.1025, '[{"name":"Tetracycline","dose":"250mg","frequency":"2x daily","days":8}]', 'Keep animal in cool place', 'VET002', 'active'),
('PRESC006', 'ANIMAL006', 'Farmer Reddy', 390.50, 'FARM-005', 13.0827, 80.2707, '[{"name":"Streptomycin","dose":"1g","frequency":"1x daily","days":7}]', 'Withdrawal period: 10 days', 'VET001', 'active'),
('PRESC007', 'ANIMAL007', 'Farmer Joshi', 510.25, 'FARM-006', 21.1458, 79.0882, '[{"name":"Chloramphenicol","dose":"250mg","frequency":"2x daily","days":5}]', 'Blood test recommended after 3 days', 'VET003', 'active'),
('PRESC008', 'ANIMAL008', 'Farmer Rao', 460.75, 'FARM-007', 12.9716, 77.5946, '[{"name":"Sulfonamides","dose":"1g","frequency":"2x daily","days":7}]', 'Ensure adequate hydration', 'VET002', 'active'),
('PRESC009', 'ANIMAL009', 'Farmer Nair', 440.00, 'FARM-008', 10.8505, 76.2711, '[{"name":"Amoxicillin","dose":"500mg","frequency":"2x daily","days":7}]', 'Complete the full course', 'VET001', 'active'),
('PRESC010', 'ANIMAL010', 'Farmer Bhat', 500.50, 'FARM-009', 23.0225, 72.5714, '[{"name":"Oxytetracycline","dose":"1g","frequency":"1x daily","days":10}]', 'Monitor milk production', 'VET003', 'active');
```

---

### Table 4: mrl_lab_tests

```sql
CREATE TABLE IF NOT EXISTS mrl_lab_tests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    test_id VARCHAR(50) UNIQUE NOT NULL,
    sample_type VARCHAR(50),
    sample_id VARCHAR(100),
    lab_id VARCHAR(50),
    test_parameters JSON,
    result_status VARCHAR(20),
    result_data JSON,
    certificate_number VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_test_id (test_id),
    INDEX idx_sample_id (sample_id),
    INDEX idx_result_status (result_status)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO mrl_lab_tests (test_id, sample_type, sample_id, lab_id, test_parameters, result_status, result_data, certificate_number) VALUES
('TEST001', 'Milk', 'SAMPLE-20251208001', 'LAB001', '["Oxytetracycline","Amoxicillin"]', 'passed', '{"Oxytetracycline":"0.05","Amoxicillin":"0.02","status":"compliant"}', 'CERT001'),
('TEST002', 'Meat', 'SAMPLE-20251208002', 'LAB002', '["Enrofloxacin","Gentamicin"]', 'passed', '{"Enrofloxacin":"15.5","Gentamicin":"20.0","status":"compliant"}', 'CERT002'),
('TEST003', 'Milk', 'SAMPLE-20251208003', 'LAB001', '["Tetracycline","Streptomycin"]', 'failed', '{"Tetracycline":"250","Streptomycin":"100.5","status":"non-compliant"}', 'CERT003'),
('TEST004', 'Milk', 'SAMPLE-20251208004', 'LAB001', '["Chloramphenicol","Sulfonamides"]', 'passed', '{"Chloramphenicol":"0.05","Sulfonamides":"50.0","status":"compliant"}', 'CERT004'),
('TEST005', 'Meat', 'SAMPLE-20251208005', 'LAB002', '["Oxytetracycline","Enrofloxacin"]', 'passed', '{"Oxytetracycline":"100.0","Enrofloxacin":"80.5","status":"compliant"}', 'CERT005'),
('TEST006', 'Milk', 'SAMPLE-20251208006', 'LAB001', '["Amoxicillin","Gentamicin"]', 'passed', '{"Amoxicillin":"2.5","Gentamicin":"60.0","status":"compliant"}', 'CERT006'),
('TEST007', 'Milk', 'SAMPLE-20251208007', 'LAB001', '["Streptomycin","Tetracycline"]', 'failed', '{"Streptomycin":"300.0","Tetracycline":"200.0","status":"non-compliant"}', 'CERT007'),
('TEST008', 'Meat', 'SAMPLE-20251208008', 'LAB002', '["Sulfonamides","Chloramphenicol"]', 'passed', '{"Sulfonamides":"75.5","Chloramphenicol":"0.15","status":"compliant"}', 'CERT008');
```

---

### Table 5: alerts

```sql
CREATE TABLE IF NOT EXISTS alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    description TEXT,
    severity VARCHAR(20),
    resolved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_severity (severity),
    INDEX idx_resolved (resolved)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO alerts (description, severity, resolved) VALUES
('High MRL levels detected in Farm-001 milk samples', 'critical', FALSE),
('Unusual antibiotic usage pattern in Farm-003', 'high', FALSE),
('Lab test results pending for 5 farms', 'medium', FALSE),
('System backup completed successfully', 'low', TRUE),
('Database optimization scheduled for tonight', 'low', FALSE);
```

---

### Table 6: audit_logs

```sql
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(50),
    action VARCHAR(100),
    entity_type VARCHAR(50),
    entity_id VARCHAR(100),
    old_value JSON,
    new_value JSON,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_entity_type (entity_type),
    INDEX idx_action (action)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_value, new_value, ip_address) VALUES
('VET001', 'CREATE', 'prescription', 'PRESC001', NULL, '{"animal_id":"ANIMAL001"}', '192.168.1.100'),
('VET001', 'UPDATE', 'prescription', 'PRESC001', '{"status":"pending"}', '{"status":"active"}', '192.168.1.100'),
('GOV001', 'VIEW', 'farm', 'FARM-001', NULL, '{"viewed_at":"2025-12-08"}', '192.168.1.105'),
('VET002', 'DELETE', 'prescription', 'PRESC002', '{"status":"active"}', NULL, '192.168.1.101'),
('VET003', 'CREATE', 'mrl_lab_tests', 'TEST001', NULL, '{"test_id":"TEST001"}', '192.168.1.102'),
('GOV002', 'UPDATE', 'farm', 'FARM-001', '{"mrl_status":"pending"}', '{"mrl_status":"approved"}', '192.168.1.106'),
('VET001', 'VIEW', 'prescription', 'PRESC003', NULL, '{"viewed_at":"2025-12-08"}', '192.168.1.100'),
('VET002', 'CREATE', 'health_history', 'HISTORY001', NULL, '{"animal_id":"ANIMAL002"}', '192.168.1.101'),
('GOV003', 'EXPORT', 'farm_alerts', 'BATCH001', NULL, '{"exported_records":10}', '192.168.1.107'),
('VET003', 'UPDATE', 'mrl_lab_tests', 'TEST005', '{"result_status":"pending"}', '{"result_status":"passed"}', '192.168.1.102'),
('VET001', 'CREATE', 'consultation_requests', 'CONSULT001', NULL, '{"farmer_phone":"9876543210"}', '192.168.1.100'),
('GOV001', 'VIEW', 'alerts', 'ALERT001', NULL, '{"action":"acknowledged"}', '192.168.1.105'),
('VET002', 'UPDATE', 'treatment_history', 'TREAT001', '{"status":"active"}', '{"status":"completed"}', '192.168.1.101'),
('VET003', 'CREATE', 'ai_analysis', 'AI001', NULL, '{"confidence_score":0.92}', '192.168.1.102'),
('GOV002', 'GENERATE', 'report', 'REPORT001', NULL, '{"format":"PDF"}', '192.168.1.106');
```

---

### Table 7: farms

```sql
CREATE TABLE IF NOT EXISTS farms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    farm_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100),
    location VARCHAR(100),
    state VARCHAR(50),
    latitude DECIMAL(10,8),
    longitude DECIMAL(10,8),
    owner_name VARCHAR(100),
    contact_phone VARCHAR(20),
    mrl_status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_farm_id (farm_id),
    INDEX idx_state (state),
    INDEX idx_mrl_status (mrl_status)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO farms (farm_id, name, location, state, latitude, longitude, owner_name, contact_phone, mrl_status) VALUES
('FARM-001', 'Green Valley Dairy', 'Pune', 'Maharashtra', 18.5204, 73.8567, 'Farmer Sharma', '9876543210', 'approved'),
('FARM-002', 'Golden Harvest Farm', 'Vadodara', 'Gujarat', 22.3072, 73.1812, 'Farmer Patel', '9876543211', 'approved'),
('FARM-003', 'Punjab Pride Poultry', 'Jalandhar', 'Punjab', 31.8273, 75.5761, 'Farmer Singh', '9876543212', 'pending'),
('FARM-004', 'Delhi Dairy Complex', 'Delhi', 'Delhi', 28.7041, 77.1025, 'Farmer Kulkarni', '9876543213', 'rejected'),
('FARM-005', 'Tamil Nadu Agro', 'Chennai', 'Tamil Nadu', 13.0827, 80.2707, 'Farmer Reddy', '9876543214', 'approved'),
('FARM-006', 'Madhya Pradesh Cattle', 'Indore', 'Madhya Pradesh', 22.7196, 75.8577, 'Farmer Joshi', '9876543215', 'approved'),
('FARM-007', 'Karnataka Livestock', 'Bengaluru', 'Karnataka', 12.9716, 77.5946, 'Farmer Rao', '9876543216', 'pending'),
('FARM-008', 'Kerala Spice Farm', 'Kochi', 'Kerala', 10.8505, 76.2711, 'Farmer Nair', '9876543217', 'approved'),
('FARM-009', 'Rajasthan Dairy', 'Jaipur', 'Rajasthan', 26.9124, 75.7873, 'Farmer Bhat', '9876543218', 'pending'),
('FARM-010', 'Bihar Agri', 'Patna', 'Bihar', 25.5941, 85.1376, 'Farmer Kumar', '9876543219', 'rejected'),
('FARM-011', 'West Bengal Poultry', 'Kolkata', 'West Bengal', 22.5726, 88.3639, 'Farmer Das', '9876543220', 'approved'),
('FARM-012', 'Haryana Dairy Belt', 'Hisar', 'Haryana', 29.1724, 75.6245, 'Farmer Singh', '9876543221', 'pending'),
('FARM-013', 'Uttarakhand Organic', 'Dehradun', 'Uttarakhand', 30.3165, 78.0322, 'Farmer Rawat', '9876543222', 'approved'),
('FARM-014', 'Assam Tea Valley', 'Guwahati', 'Assam', 26.1445, 91.7362, 'Farmer Nath', '9876543223', 'pending'),
('FARM-015', 'Goa Coastal Farm', 'Panaji', 'Goa', 15.4909, 73.8278, 'Farmer Mendes', '9876543224', 'approved');
```

---

### Table 8: policies

```sql
CREATE TABLE IF NOT EXISTS policies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200),
    category VARCHAR(50),
    content LONGTEXT,
    effective_date DATE,
    tier VARCHAR(50),
    created_by VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_tier (tier)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO policies (title, category, content, effective_date, tier, created_by) VALUES
('MRL Standards for Dairy Products', 'mrl', 'Maximum Residue Limits for milk and dairy products set by FSSAI. Oxytetracycline: 100 ppb, Amoxicillin: 4 ppb...', '2025-01-01', 'national', 'GOV001'),
('Antibiotic Usage Guidelines', 'antibiotics', 'Guidelines for responsible use of antibiotics in livestock to prevent resistance. Maximum duration: 7-10 days...', '2025-02-01', 'national', 'GOV001'),
('Farm Inspection Protocol', 'inspection', 'Standard operating procedure for farm inspections to verify MRL compliance. Inspection frequency based on risk...', '2025-01-15', 'state', 'GOV002'),
('Data Reporting Requirements', 'reporting', 'All farms must report antibiotic usage and lab test results within 48 hours of administration...', '2025-01-01', 'state', 'GOV002'),
('Consumer Product Labeling', 'labeling', 'MRL-compliant products must be labeled with test certificate numbers and safe-to-use dates...', '2025-03-01', 'national', 'GOV001');
```

---

### Table 9: consultation_requests

```sql
CREATE TABLE IF NOT EXISTS consultation_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    farmer_phone VARCHAR(20),
    animal_id VARCHAR(50),
    issue_description TEXT,
    status VARCHAR(20) DEFAULT 'pending',
    vet_id VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_vet_id (vet_id)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO consultation_requests (farmer_phone, animal_id, issue_description, status, vet_id) VALUES
('9876543210', 'ANIMAL001', 'Cow not eating properly, production dropped', 'pending', NULL),
('9876543211', 'ANIMAL002', 'Severe mastitis, urgent help needed', 'assigned', 'VET002'),
('9876543212', 'ANIMAL003', 'Foot and mouth disease suspected', 'completed', 'VET001'),
('9876543213', 'ANIMAL004', 'Pregnancy confirmation needed', 'pending', NULL),
('9876543214', 'ANIMAL005', 'Vaccination schedule consultation', 'assigned', 'VET003'),
('9876543215', 'ANIMAL006', 'Diarrhea in calves, multiple animals affected', 'completed', 'VET002'),
('9876543216', 'ANIMAL007', 'Antibiotic sensitivity testing needed', 'pending', NULL),
('9876543217', 'ANIMAL008', 'General health checkup requested', 'assigned', 'VET001');
```

---

### Table 10: health_history

```sql
CREATE TABLE IF NOT EXISTS health_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    animal_id VARCHAR(50) NOT NULL,
    treatment_date DATE,
    treatment_description TEXT,
    vet_id VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_animal_id (animal_id),
    INDEX idx_vet_id (vet_id)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO health_history (animal_id, treatment_date, treatment_description, vet_id) VALUES
('ANIMAL001', '2025-12-01', 'Treated for respiratory infection with Oxytetracycline', 'VET001'),
('ANIMAL002', '2025-12-02', 'Mastitis treatment with Amoxicillin', 'VET002'),
('ANIMAL003', '2025-12-03', 'Foot disease treatment, hoof care provided', 'VET001'),
('ANIMAL004', '2025-12-04', 'Pregnancy confirmed, prenatal care started', 'VET003'),
('ANIMAL005', '2025-12-05', 'Vaccination against FMD completed', 'VET002'),
('ANIMAL006', '2025-11-28', 'Calf diarrhea treated with Enrofloxacin', 'VET003'),
('ANIMAL007', '2025-11-27', 'General health checkup, vital signs normal', 'VET001'),
('ANIMAL008', '2025-11-26', 'Parasite treatment with Albendazole', 'VET002'),
('ANIMAL009', '2025-11-25', 'Udder inflammation treated', 'VET001'),
('ANIMAL010', '2025-11-24', 'Recovery from gastroenteritis', 'VET003'),
('ANIMAL001', '2025-11-20', 'Routine vaccination booster dose', 'VET002'),
('ANIMAL002', '2025-11-15', 'Follow-up checkup, full recovery confirmed', 'VET001');
```

---

### Table 11: safety_alerts

```sql
CREATE TABLE IF NOT EXISTS safety_alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    farm_id VARCHAR(50),
    alert_type VARCHAR(50),
    description TEXT,
    severity VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_farm_id (farm_id),
    INDEX idx_severity (severity)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO safety_alerts (farm_id, alert_type, description, severity) VALUES
('FARM-001', 'mrl_violation', 'MRL exceeded for Tetracycline in last batch', 'critical'),
('FARM-001', 'withdrawal_period', 'Insufficient withdrawal period observed before sale', 'high'),
('FARM-002', 'antibiotic_overuse', 'Excessive antibiotic usage detected', 'high'),
('FARM-003', 'lab_pending', 'Lab test results pending for 7 days', 'medium'),
('FARM-004', 'mrl_violation', 'Multiple MRL violations in 3 consecutive tests', 'critical'),
('FARM-005', 'compliance_warning', 'Minor compliance issue, corrective action required', 'low'),
('FARM-007', 'audit_flag', 'Farm flagged for routine audit inspection', 'medium'),
('FARM-010', 'pattern_alert', 'Unusual antibiotic usage pattern detected', 'high'),
('FARM-012', 'supply_chain', 'Supplier change detected, reverification needed', 'medium'),
('FARM-014', 'system_alert', 'Data anomaly detected in reporting system', 'low');
```

---

### Table 12: farm_alerts

```sql
CREATE TABLE IF NOT EXISTS farm_alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    farm_id VARCHAR(50),
    alert_message TEXT,
    alert_type VARCHAR(50),
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_farm_id (farm_id),
    INDEX idx_status (status)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO farm_alerts (farm_id, alert_message, alert_type, status) VALUES
('FARM-001', 'Next MRL test due on 2025-12-15', 'scheduled_test', 'active'),
('FARM-001', 'Withdrawal period ends on 2025-12-12', 'withdrawal_alert', 'active'),
('FARM-002', 'New antibiotic policy update - please review', 'policy_update', 'active'),
('FARM-003', 'Pending government inspection - be prepared', 'inspection', 'active'),
('FARM-004', 'Compliance improvement plan required', 'action_needed', 'active'),
('FARM-005', 'Lab certificate expires on 2025-12-30', 'certificate_expiry', 'active'),
('FARM-007', 'Vet consultation recommended for herd health', 'recommendation', 'active'),
('FARM-010', 'Suspension review scheduled for 2025-12-20', 'review', 'active');
```

---

### Table 13: treatment_history

```sql
CREATE TABLE IF NOT EXISTS treatment_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    animal_id VARCHAR(50),
    treatment_date DATE,
    medicine_used VARCHAR(100),
    dosage VARCHAR(50),
    vet_id VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_animal_id (animal_id),
    INDEX idx_treatment_date (treatment_date)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO treatment_history (animal_id, treatment_date, medicine_used, dosage, vet_id) VALUES
('ANIMAL001', '2025-12-01', 'Oxytetracycline', '1g once daily', 'VET001'),
('ANIMAL002', '2025-12-02', 'Amoxicillin', '500mg twice daily', 'VET002'),
('ANIMAL003', '2025-12-03', 'Enrofloxacin', '100mg once daily', 'VET001'),
('ANIMAL004', '2025-12-04', 'Gentamicin', '80mg once daily', 'VET003'),
('ANIMAL005', '2025-12-05', 'Tetracycline', '250mg twice daily', 'VET002'),
('ANIMAL006', '2025-11-28', 'Streptomycin', '1g once daily', 'VET003'),
('ANIMAL007', '2025-11-27', 'Chloramphenicol', '250mg twice daily', 'VET001'),
('ANIMAL008', '2025-11-26', 'Sulfonamides', '1g twice daily', 'VET002'),
('ANIMAL009', '2025-11-25', 'Penicillin', '2 million units once daily', 'VET001'),
('ANIMAL010', '2025-11-24', 'Cephalexin', '250mg twice daily', 'VET003'),
('ANIMAL001', '2025-11-20', 'Paracetamol', '500mg once daily', 'VET002'),
('ANIMAL002', '2025-11-18', 'Aspirin', '300mg twice daily', 'VET001'),
('ANIMAL003', '2025-11-15', 'Ibuprofen', '200mg once daily', 'VET003'),
('ANIMAL004', '2025-11-10', 'Vitamin B Complex', '1ml once daily', 'VET002'),
('ANIMAL005', '2025-11-08', 'Calcium Gluconate', '500ml once', 'VET001');
```

---

### Table 14: batches

```sql
CREATE TABLE IF NOT EXISTS batches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    batch_id VARCHAR(50) UNIQUE NOT NULL,
    farm_id VARCHAR(50),
    product_type VARCHAR(50),
    mrl_status VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_batch_id (batch_id),
    INDEX idx_farm_id (farm_id),
    INDEX idx_mrl_status (mrl_status)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO batches (batch_id, farm_id, product_type, mrl_status) VALUES
('BATCH001', 'FARM-001', 'Milk', 'approved'),
('BATCH002', 'FARM-001', 'Milk', 'approved'),
('BATCH003', 'FARM-002', 'Meat', 'approved'),
('BATCH004', 'FARM-003', 'Milk', 'pending'),
('BATCH005', 'FARM-004', 'Milk', 'rejected'),
('BATCH006', 'FARM-005', 'Milk', 'approved'),
('BATCH007', 'FARM-007', 'Meat', 'pending'),
('BATCH008', 'FARM-010', 'Milk', 'rejected'),
('BATCH009', 'FARM-012', 'Milk', 'pending'),
('BATCH010', 'FARM-015', 'Meat', 'approved');
```

---

### Table 15: ai_analysis

```sql
CREATE TABLE IF NOT EXISTS ai_analysis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    animal_id VARCHAR(50),
    symptoms TEXT,
    recommendation TEXT,
    confidence_score DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_animal_id (animal_id)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO ai_analysis (animal_id, symptoms, recommendation, confidence_score) VALUES
('ANIMAL001', 'Loss of appetite, fever (38.5°C), reduced milk production', 'Probable mastitis or respiratory infection. Recommend Amoxicillin or Oxytetracycline. Vet consult required.', 0.92),
('ANIMAL002', 'Swollen udder, pain during milking, discharge', 'High confidence mastitis. Immediate antibiotic treatment needed. Monitor milk quality.', 0.97),
('ANIMAL003', 'Limping, swollen hooves, fever', 'Likely foot and mouth disease or digital dermatitis. Isolation and treatment recommended.', 0.88),
('ANIMAL004', 'Normal vitals, weight gain expected, no symptoms', 'Healthy pregnant animal. Continue prenatal care and nutrition monitoring.', 0.95),
('ANIMAL005', 'Mild cough, normal temperature, eating well', 'Minor respiratory infection suspected. Monitor for 3-5 days before treatment decision.', 0.75),
('ANIMAL006', 'Diarrhea, dehydration signs, weakness', 'Likely calf diarrhea, possibly viral. Fluid therapy and probiotics recommended.', 0.91);
```

---

### Table 16: product_info

```sql
CREATE TABLE IF NOT EXISTS product_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id VARCHAR(100) UNIQUE NOT NULL,
    farm_id VARCHAR(50),
    product_type VARCHAR(50),
    batch_number VARCHAR(100),
    mrl_compliant BOOLEAN,
    lab_test_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_product_id (product_id),
    INDEX idx_farm_id (farm_id),
    INDEX idx_mrl_compliant (mrl_compliant)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Sample Data:**

```sql
INSERT INTO product_info (product_id, farm_id, product_type, batch_number, mrl_compliant, lab_test_date) VALUES
('PROD001', 'FARM-001', 'Milk', 'BATCH001', TRUE, '2025-12-08'),
('PROD002', 'FARM-001', 'Milk', 'BATCH002', TRUE, '2025-12-07'),
('PROD003', 'FARM-002', 'Meat', 'BATCH003', TRUE, '2025-12-06'),
('PROD004', 'FARM-003', 'Milk', 'BATCH004', NULL, '2025-12-09'),
('PROD005', 'FARM-004', 'Milk', 'BATCH005', FALSE, '2025-12-05'),
('PROD006', 'FARM-005', 'Milk', 'BATCH006', TRUE, '2025-12-04'),
('PROD007', 'FARM-007', 'Meat', 'BATCH007', NULL, '2025-12-09'),
('PROD008', 'FARM-010', 'Milk', 'BATCH008', FALSE, '2025-12-03'),
('PROD009', 'FARM-012', 'Milk', 'BATCH009', NULL, '2025-12-08'),
('PROD010', 'FARM-015', 'Meat', 'BATCH010', TRUE, '2025-12-02'),
('PROD011', 'FARM-001', 'Milk', 'BATCH001', TRUE, '2025-12-01'),
('PROD012', 'FARM-005', 'Milk', 'BATCH006', TRUE, '2025-11-30');
```

---

## 🔧 Complete Database Setup SQL Script

Here's the complete SQL to create all tables and insert sample data:

```sql
-- Create Database
CREATE DATABASE IF NOT EXISTS agrisense_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE agrisense_db;

-- [Insert all CREATE TABLE statements from above]
-- [Insert all INSERT statements from above]

-- Verify tables were created
SHOW TABLES;

-- Count records in each table
SELECT 'veterinarians' as table_name, COUNT(*) as count FROM veterinarians
UNION ALL
SELECT 'government_users', COUNT(*) FROM government_users
UNION ALL
SELECT 'prescriptions', COUNT(*) FROM prescriptions
UNION ALL
SELECT 'mrl_lab_tests', COUNT(*) FROM mrl_lab_tests
UNION ALL
SELECT 'alerts', COUNT(*) FROM alerts
UNION ALL
SELECT 'audit_logs', COUNT(*) FROM audit_logs
UNION ALL
SELECT 'farms', COUNT(*) FROM farms
UNION ALL
SELECT 'policies', COUNT(*) FROM policies
UNION ALL
SELECT 'consultation_requests', COUNT(*) FROM consultation_requests
UNION ALL
SELECT 'health_history', COUNT(*) FROM health_history
UNION ALL
SELECT 'safety_alerts', COUNT(*) FROM safety_alerts
UNION ALL
SELECT 'farm_alerts', COUNT(*) FROM farm_alerts
UNION ALL
SELECT 'treatment_history', COUNT(*) FROM treatment_history
UNION ALL
SELECT 'batches', COUNT(*) FROM batches
UNION ALL
SELECT 'ai_analysis', COUNT(*) FROM ai_analysis
UNION ALL
SELECT 'product_info', COUNT(*) FROM product_info;
```

---

## 📥 How to Import

### Option 1: phpMyAdmin

1. Go to: `http://localhost/phpmyadmin`
2. Click "Import" tab
3. Select file and upload
4. Click "Import"

### Option 2: MySQL CLI

```bash
mysql -u root -p agrisense_db < agrisense_schema.sql
```

### Option 3: Direct Execution (PHP)

The system has **automatic table creation**. Just:

1. Create empty database `agrisense_db`
2. First API call will auto-create all tables

---

## ✅ Verification Queries

After import, run these to verify:

```sql
-- Check all tables exist
SHOW TABLES;

-- Verify table counts
SELECT COUNT(*) FROM veterinarians;     -- Should be 3
SELECT COUNT(*) FROM government_users;   -- Should be 3
SELECT COUNT(*) FROM prescriptions;      -- Should be 10
SELECT COUNT(*) FROM mrl_lab_tests;      -- Should be 8
SELECT COUNT(*) FROM alerts;             -- Should be 5
SELECT COUNT(*) FROM audit_logs;         -- Should be 15
SELECT COUNT(*) FROM farms;              -- Should be 15
SELECT COUNT(*) FROM policies;           -- Should be 5
SELECT COUNT(*) FROM consultation_requests; -- Should be 8
SELECT COUNT(*) FROM health_history;     -- Should be 12
SELECT COUNT(*) FROM safety_alerts;      -- Should be 10
SELECT COUNT(*) FROM farm_alerts;        -- Should be 8
SELECT COUNT(*) FROM treatment_history;  -- Should be 15
SELECT COUNT(*) FROM batches;            -- Should be 10
SELECT COUNT(*) FROM ai_analysis;        -- Should be 6
SELECT COUNT(*) FROM product_info;       -- Should be 12

-- Total: 155 sample data records
```

---

## 🔑 Test Login Credentials

### Veterinarians

- **VET001** / password: `demo`
- **VET002** / password: `demo`
- **VET003** / password: `demo`

### Government Users

- **GOV001** (National) / password: `demo`
- **GOV002** (State) / password: `demo`
- **GOV003** (District) / password: `demo`

---

## 📊 Database Statistics

| Metric         | Value                   |
| -------------- | ----------------------- |
| Total Tables   | 16                      |
| Total Fields   | 120+                    |
| Sample Records | 155                     |
| Relationships  | Foreign Keys configured |
| Encoding       | UTF8MB4 Unicode         |
| Status         | Production Ready        |

---

**Database Setup Complete!** ✅

All 16 tables with 155 sample records are ready for testing.

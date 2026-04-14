# Lab Tests Database Setup Guide

This guide will help you set up the database tables needed for the Lab Tests feature in Agrisense.

## Prerequisites

- XAMPP is running (Apache + MySQL)
- You have access to phpMyAdmin or MySQL command line

## Step-by-Step Setup

### Option 1: Using phpMyAdmin (Easiest)

1. **Open phpMyAdmin**

   - Open your browser and go to: `http://localhost/phpmyadmin`

2. **Select/Create Database**

   - Click on "Databases" tab
   - Create a new database named `agrisense_db` if it doesn't exist
   - Select `agrisense_db` from the left panel

3. **Import SQL File**

   - Click on the "Import" tab
   - Click "Choose File" and select: `lab_tests_tables.sql` (from the project root directory)
   - Click "Import" button
   - You should see a success message

4. **Verify Tables**
   - You should now see these tables in the left panel:
     - `lab_tests`
     - `lab_test_samples`
     - `lab_test_reports`
     - `farms`
   - Each table will have sample data pre-populated for testing

### Option 2: Using MySQL Command Line

1. **Open Command Prompt/PowerShell**

   - Navigate to your XAMPP MySQL directory (usually `C:\xampp\mysql\bin`)

2. **Run the Import Command**

   ```bash
   mysql -u root -p agrisense_db < "C:\path\to\lab_tests_tables.sql"
   ```

   - When prompted for password, leave it empty (just press Enter)
   - If database doesn't exist yet, create it first:

   ```bash
   mysql -u root -p -e "CREATE DATABASE agrisense_db;"
   ```

3. **Verify Installation**
   ```bash
   mysql -u root -p agrisense_db -e "SHOW TABLES;"
   ```

## Database Schema Overview

### lab_tests Table

- Stores all lab tests with their metadata
- Fields: id, farm_id, animal_id, test_type, description, priority, vet_id, status, test_results, notes
- Statuses: `pending`, `sample_collected`, `report_generated`, `approved`, `rejected`

### lab_test_samples Table

- Tracks sample collection for each lab test
- Auto-generates unique sample IDs (format: SAMPLE-YYYYMMDDHHmmss-XXXX)
- Fields: id, lab_test_id, sample_id, sample_type, collection_date, collector_name, quantity, unit, status, notes

### lab_test_reports Table

- Stores generated lab reports with MRL compliance status
- Fields: id, lab_test_id, sample_id, farm_id, lab_name, technician, test_results, mrl_status, approval_notes, approved_by, report_date, approved_at

### farms Table

- Contains farm location data for heatmap visualization
- Fields: id, farm_id, farm_name, state, latitude, longitude, status, has_alert

## Sample Data Included

The SQL script includes sample data for testing:

- 10 farms across different Indian states with latitude/longitude coordinates
- 5 sample lab tests with various statuses
- 5 sample collected samples
- 2 lab test reports with MRL compliance results

## Testing the Setup

### 1. Test API Endpoints

Open your browser and test these URLs:

**Get all lab tests:**

```
http://localhost/update_after_mentoring_1/api/lab_tests.php
```

**Get heatmap data:**

```
http://localhost/update_after_mentoring_1/api/heatmap.php?metric=mrl&time_period=30days
```

You should see JSON responses with the sample data.

### 2. Test the Web Interface

1. Open: `http://localhost/update_after_mentoring_1/index.html`
2. Log in as a veterinarian
3. Navigate to the "Lab Tests" section
4. Try creating a new lab test
5. Collect a sample
6. Generate a report
7. Review and approve the report

### 3. Test the Heatmap

1. Navigate to "Government Portal"
2. Click on "National AMU & MRL Heatmaps"
3. Select different filters (Region, Time Period, Metric)
4. Click "Apply Filters" to see the map update
5. Click on markers to see farm details
6. Try exporting data to CSV

## Troubleshooting

### Database Connection Error

If you get "Database connection failed":

1. Verify MySQL is running in XAMPP Control Panel
2. Check that `agrisense_db` database exists
3. Verify credentials in `api/config.php` (should be root/no password for default XAMPP)

### Table Already Exists Error

If you see "Table already exists":

- This is normal if you've run the setup before
- The SQL uses `CREATE TABLE IF NOT EXISTS` so it won't overwrite existing tables
- To start fresh, you can drop the tables first in phpMyAdmin

### API Returns 500 Error

1. Check `api/logs/` directory for error logs
2. Verify that required tables exist in the database
3. Check browser console (F12) for JavaScript errors

### Heatmap Not Showing

1. Verify that the `farms` table has sample data with valid latitude/longitude
2. Check browser console for JavaScript errors
3. Ensure Leaflet library is loaded (check page source for script tags)

## Next Steps

After database setup:

1. Create new lab tests through the web interface
2. Monitor the heatmap visualization
3. Test all CRUD operations (Create, Read, Update, Delete)
4. Verify MRL compliance checking works correctly

## Support

For issues or questions, check:

- `api/logs/` directory for API error logs
- Browser console (F12) for JavaScript errors
- Database tables directly in phpMyAdmin

---

**Lab Tests Implementation Complete!** ✅

The system is now ready for:

- Creating and managing laboratory tests
- Collecting and tracking samples
- Generating compliance reports
- Monitoring antimicrobial usage via heatmaps

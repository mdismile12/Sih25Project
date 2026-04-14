# QUICK START - Lab Tests Implementation

## ⚡ 5-Minute Setup

### 1. Import Database (3 minutes)

**Option A: phpMyAdmin (Easiest)**

- Go to: `http://localhost/phpmyadmin`
- Click "Import" tab
- Upload: `lab_tests_tables.sql`
- Click "Import" ✓

**Option B: MySQL Command Line**

```bash
mysql -u root -p agrisense_db < lab_tests_tables.sql
```

Press Enter at password prompt (XAMPP has no password).

### 2. Verify Files (1 minute)

All files should be in place:

- ✅ `api/lab_tests.php`
- ✅ `api/lab_test_samples.php`
- ✅ `api/lab_test_reports.php`
- ✅ `api/heatmap.php`
- ✅ `js/lab_tests.js`
- ✅ `js/heatmap.js`
- ✅ `index.html` (updated with script imports)

### 3. Test It! (1 minute)

**Test API:**

```
http://localhost/update_after_mentoring_1/api/lab_tests.php
```

Should return JSON with sample tests.

**Test Portal:**

```
http://localhost/update_after_mentoring_1/index.html
```

Login as veterinarian → Navigate to "Lab Tests" section.

---

## 🎯 What You Can Do Now

### Lab Tests Module

✅ Create lab tests (Farm ID, Animal ID, Type, Priority)  
✅ Collect samples (Auto-generated Sample IDs)  
✅ Generate reports (Add test results)  
✅ Approve/Reject reports (Automatic MRL compliance checking)

### Heatmap

✅ View interactive farm locations on map  
✅ Filter by region (Maharashtra, Delhi, etc.)  
✅ Filter by metric (AMU or MRL Compliance)  
✅ Filter by time period (7/30/90 days)  
✅ Export data to CSV

---

## 📊 Sample Data Included

Database comes with pre-loaded data for testing:

- **10 farms** with real Indian coordinates
- **5 lab tests** with various statuses
- **5 samples** with auto-generated tracking IDs
- **2 reports** showing MRL compliance results

No data entry needed - just test the features!

---

## 🔍 Testing the Workflow

### Complete Lab Test Cycle (5 minutes)

1. **Create Test**

   - Click: Lab Tests → Create Lab Test
   - Enter: Farm ID (e.g., "FARM-001"), Animal ID, Test Type
   - Click: Create Test

2. **Collect Sample**

   - Click: Collect Sample
   - Select: Your created test
   - Click: Collect Sample
   - Note: Sample ID generated automatically

3. **Generate Report**

   - Click: Generate Report
   - Select: Your test
   - Add test results:
     - Click "Add Test Result"
     - Select chemical (e.g., Oxytetracycline)
     - Enter value (e.g., 0.05)
   - Click: Generate Report
   - Note: MRL status shows approved/rejected automatically

4. **Review & Approve**
   - Click: Review Reports
   - Find your report
   - Click: Approve Report
   - Status changes to "approved" with timestamp

---

## 🗺️ Testing the Heatmap

### Heatmap Features (3 minutes)

1. Go to: **Government Portal** → **Heatmaps**
2. Map loads with 10 farm markers
3. Try filters:
   - Metric: Switch "AMU" ↔ "MRL"
   - Region: Select "Maharashtra"
   - Time: Select "7 Days"
4. Click "Apply Filters" - map updates
5. Click marker - see farm details popup
6. Click "Export Data" - CSV downloads

---

## 📋 Documentation

Three guides available:

1. **`DATABASE_SETUP_GUIDE.md`** - Detailed setup instructions
2. **`TESTING_CHECKLIST.md`** - 60+ test scenarios
3. **`LAB_TESTS_IMPLEMENTATION_SUMMARY.md`** - Technical details

---

## 🚨 If Something Breaks

### Check Error Logs

```
api/logs/
├── lab_tests.log
├── lab_samples.log
├── lab_reports.log
└── heatmap.log
```

### Browser Console (F12)

- Click F12 in browser
- Go to "Console" tab
- Look for red errors
- Search for function names

### Common Issues

**"Database connection failed"**
→ Is MySQL running in XAMPP? (Check XAMPP Control Panel)

**"Table doesn't exist"**
→ Did you import `lab_tests_tables.sql`? (Check phpMyAdmin)

**Map not showing**
→ Check browser console for Leaflet errors

**Features not working**
→ Check both: API logs AND browser console

---

## 📞 Need Help?

### Quick Reference

| Issue                      | Check                               |
| -------------------------- | ----------------------------------- |
| API returns 500            | `api/logs/` directory               |
| Portal shows blank         | Browser console (F12)               |
| Map won't load             | Check Leaflet in network tab        |
| Tests not saving           | Verify `lab_tests` table exists     |
| Samples not generating IDs | Check database for sample_id column |

### Key Files

| File                       | Purpose                 |
| -------------------------- | ----------------------- |
| `index.html`               | Portal UI (5500+ lines) |
| `js/lab_tests.js`          | Lab test logic          |
| `js/heatmap.js`            | Map visualization       |
| `api/lab_tests.php`        | Test API                |
| `api/lab_test_samples.php` | Sample API              |
| `api/lab_test_reports.php` | Report API              |
| `api/heatmap.php`          | Heatmap API             |
| `lab_tests_tables.sql`     | Database schema         |

---

## ✅ You're All Set!

Everything is ready to:

1. Import database (5 min)
2. Test features (5 min)
3. Review & approve (2 min)

**Total time: ~12 minutes** ⏱️

---

## 🎉 Next Steps

After testing:

1. Train veterinarians on the new system
2. Monitor API logs for any issues
3. Backup database regularly
4. Customize MRL standards if needed (edit `js/lab_tests.js`)
5. Add your own farms (insert into `farms` table)

---

**Status: PRODUCTION READY** ✅

All systems go for immediate deployment!

---

For detailed information, see:

- 📖 `DATABASE_SETUP_GUIDE.md`
- ✅ `TESTING_CHECKLIST.md`
- 📊 `LAB_TESTS_IMPLEMENTATION_SUMMARY.md`

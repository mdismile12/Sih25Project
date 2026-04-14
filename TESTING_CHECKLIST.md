# Lab Tests & Heatmap Feature - Testing Checklist

## Pre-Testing Setup ✓

- [ ] Database tables created (`lab_tests`, `lab_test_samples`, `lab_test_reports`, `farms`)
- [ ] Sample data imported successfully
- [ ] MySQL service is running in XAMPP
- [ ] XAMPP Apache is running
- [ ] All API files updated to use PDO (✓ DONE)
- [ ] JavaScript modules added to HTML head (✓ DONE)
- [ ] Module initialization in DOMContentLoaded (✓ DONE)

## Lab Tests Module Testing

### Create Lab Test

- [ ] Navigate to Vet Portal > Lab Tests
- [ ] Click "Create Lab Test" tab
- [ ] Fill in: Farm ID, Animal ID, Test Type, Priority
- [ ] Click "Create Test" button
- [ ] Verify test appears in "Recent Lab Tests" table
- [ ] Verify status shows "pending"

### Sample Collection

- [ ] Click "Collect Sample" tab
- [ ] Select a lab test from dropdown
- [ ] Fill in: Sample Type, Collection Date, Collector Name, Quantity
- [ ] Click "Collect Sample" button
- [ ] Verify sample ID is generated (format: SAMPLE-YYYYMMDDHHmmss-XXXX)
- [ ] Verify test status changes to "sample_collected" in database
- [ ] Verify success notification appears

### Report Generation

- [ ] Click "Generate Report" tab
- [ ] Select same lab test
- [ ] Fill in: Lab Name, Technician
- [ ] Add test results:
  - [ ] Click "Add Test Result" button
  - [ ] Select chemical (e.g., "Oxytetracycline")
  - [ ] Enter detected value (e.g., 0.05)
  - [ ] Enter detected unit (e.g., "mg/L")
  - [ ] Verify MRL limit is auto-filled
  - [ ] Add 2-3 chemicals total
- [ ] Click "Generate Report" button
- [ ] Verify:
  - [ ] Report is created with ID
  - [ ] MRL status shows "approved" if all values < MRL limits
  - [ ] MRL status shows "rejected" if any value > MRL limits
  - [ ] Success notification appears

### Report Review & Approval

- [ ] Click "Review Reports" tab
- [ ] Verify generated report appears in list
- [ ] Click on report to view details
- [ ] Verify all test results display correctly
- [ ] Click "Approve Report" button
- [ ] Verify:
  - [ ] Approval timestamp is recorded
  - [ ] Status changes to "approved"
  - [ ] "Approved by" shows current user
  - [ ] Report card highlights in green
- [ ] Test "Reject" for another report:
  - [ ] Click "Reject Report"
  - [ ] Enter reason
  - [ ] Verify status changes to "rejected"
  - [ ] Verify report highlights in red

### Filtering & Sorting

- [ ] In "Recent Lab Tests" section:
  - [ ] Click "Filter by Status" dropdown
  - [ ] Select "approved"
  - [ ] Verify only approved tests display
  - [ ] Select "rejected"
  - [ ] Verify only rejected tests display
  - [ ] Select "All Statuses"
  - [ ] Verify all tests display

## Heatmap Module Testing

### Map Initialization

- [ ] Navigate to Government Portal > National AMU & MRL Heatmaps
- [ ] Verify:
  - [ ] Map loads successfully
  - [ ] OpenStreetMap tiles are visible
  - [ ] Farm markers appear on map
  - [ ] Color legend displays (Red, Orange, Yellow, Green)

### Filtering by Metric

- [ ] From metric dropdown, select "AMU (Antimicrobial Usage)"
- [ ] Click "Apply Filters"
- [ ] Verify:
  - [ ] Map updates with new markers
  - [ ] Color scheme represents AMU levels
  - [ ] Legend shows AMU-specific ranges
- [ ] Change metric to "MRL Compliance"
- [ ] Click "Apply Filters"
- [ ] Verify:
  - [ ] Map updates with new markers
  - [ ] Color scheme represents compliance rates
  - [ ] Legend shows compliance percentage ranges

### Filtering by Region

- [ ] From region dropdown, select "Maharashtra"
- [ ] Click "Apply Filters"
- [ ] Verify:
  - [ ] Only Maharashtra farms display on map
  - [ ] Statistics update to show Maharashtra data
- [ ] Test other regions (Delhi, Karnataka, etc.)
- [ ] Select "All India"
- [ ] Verify all farms display again

### Filtering by Time Period

- [ ] From time period dropdown, select "Last 7 Days"
- [ ] Click "Apply Filters"
- [ ] Verify data updates (may be fewer records)
- [ ] Select "Last 30 Days"
- [ ] Click "Apply Filters"
- [ ] Verify more data appears
- [ ] Test "Last 90 Days"

### Marker Interaction

- [ ] Click on a farm marker on the map
- [ ] Verify popup displays:
  - [ ] Farm name
  - [ ] Farm ID
  - [ ] State
  - [ ] Current metric value
  - [ ] Action buttons (View Details, View Alerts)
- [ ] Close popup (click elsewhere on map)

### Statistics Display

- [ ] Verify statistics container shows:
  - [ ] Total farm locations counted
  - [ ] Risk breakdown (High Risk, Medium Risk, Low Risk, Compliant)
  - [ ] Percentage calculations
  - [ ] Time period displayed

### Export Functionality

- [ ] Click "Export Data" button
- [ ] Verify:
  - [ ] CSV file downloads to computer
  - [ ] Filename includes timestamp
  - [ ] CSV contains headers and farm data
  - [ ] Data can be opened in Excel

### Responsive Behavior

- [ ] Resize browser window (test responsive design)
- [ ] Verify:
  - [ ] Map resizes properly
  - [ ] Filters remain accessible
  - [ ] Buttons stay clickable
  - [ ] Layout adjusts for smaller screens

## API Endpoint Testing

### Lab Tests Endpoint

- [ ] Test GET: `http://localhost/update_after_mentoring_1/api/lab_tests.php`
  - [ ] Returns JSON with all lab tests
- [ ] Test GET with filter: `?status=approved`
  - [ ] Returns only approved tests
- [ ] Test GET with farm_id: `?farm_id=FARM-001`
  - [ ] Returns tests for specific farm

### Lab Samples Endpoint

- [ ] Test GET: `http://localhost/update_after_mentoring_1/api/lab_test_samples.php`
  - [ ] Returns JSON with all samples
- [ ] Test POST with valid data:
  - [ ] Creates new sample
  - [ ] Returns generated sample_id
  - [ ] Updates parent test status

### Lab Reports Endpoint

- [ ] Test GET: `http://localhost/update_after_mentoring_1/api/lab_test_reports.php`
  - [ ] Returns JSON with all reports
- [ ] Test POST with test results:
  - [ ] Creates report
  - [ ] Evaluates MRL compliance
  - [ ] Returns mrl_status (approved/rejected/pending)

### Heatmap Endpoint

- [ ] Test: `http://localhost/update_after_mentoring_1/api/heatmap.php?metric=mrl&time_period=30days`
  - [ ] Returns farm data with metrics
  - [ ] Includes latitude/longitude for mapping
- [ ] Test with region filter: `?metric=mrl&time_period=30days&region=Maharashtra`
  - [ ] Returns only Maharashtra farms

## Error Handling Testing

- [ ] Try creating test without required fields
  - [ ] Verify error message displays
- [ ] Try collecting sample without selecting test
  - [ ] Verify validation message appears
- [ ] Try generating report with invalid data
  - [ ] Verify error is shown
- [ ] Disconnect database and test
  - [ ] Verify error message is user-friendly (not raw PHP error)
- [ ] Check browser console for JavaScript errors
  - [ ] Should be no errors (only info/warning logs)

## Browser Compatibility Testing

- [ ] Test in Chrome
  - [ ] All features work
  - [ ] Map displays correctly
  - [ ] No console errors
- [ ] Test in Firefox
  - [ ] All features work
  - [ ] Map displays correctly
  - [ ] No console errors
- [ ] Test in Edge
  - [ ] All features work
- [ ] Test in Safari (if available)
  - [ ] All features work

## Performance Testing

- [ ] Creating test with 10+ chemicals in report
  - [ ] Should complete in < 2 seconds
- [ ] Loading heatmap with all 10 farms
  - [ ] Should display smoothly
- [ ] Exporting large CSV
  - [ ] Should complete without freezing
- [ ] Rapid filtering on heatmap
  - [ ] Should respond without lag

## Data Persistence Testing

- [ ] Create a lab test and refresh page
  - [ ] Test should still be there
- [ ] Generate a report, close browser, reopen
  - [ ] Report should still be in the system
- [ ] Export data, verify CSV has all records
  - [ ] Should match database count

## Final Verification

- [ ] All required files exist:

  - [ ] `api/lab_tests.php` ✓
  - [ ] `api/lab_test_samples.php` ✓
  - [ ] `api/lab_test_reports.php` ✓
  - [ ] `api/heatmap.php` ✓
  - [ ] `js/lab_tests.js` ✓
  - [ ] `js/heatmap.js` ✓
  - [ ] `lab_tests_tables.sql` ✓
  - [ ] `index.html` (updated) ✓

- [ ] All errors fixed:

  - [ ] No PHP errors
  - [ ] No JavaScript errors
  - [ ] No database connection errors

- [ ] Documentation complete:
  - [ ] `DATABASE_SETUP_GUIDE.md` created
  - [ ] `TESTING_CHECKLIST.md` created (this file)

---

**Testing Status: READY FOR EXECUTION** ✅

Once database is set up, follow this checklist systematically to validate the entire implementation.

**Estimated Testing Time:** 30-45 minutes for full coverage

**Report Issues:** Check logs in `api/logs/` and browser console for detailed error messages.

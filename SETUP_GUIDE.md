# Agrisense Portal - Setup & Configuration Guide

## ⚠️ Important: Fixing 404 Errors

Your application is experiencing 404 errors because the API routing wasn't properly configured. Follow these steps to fix it:

### Step 1: Database Setup ✅

1. **Open phpMyAdmin** in your browser:
   - Navigate to: `http://localhost/phpmyadmin`
2. **Import the SQL database:**

   - Click on "Import" tab
   - Click "Choose File" and select: `/update_after_mentoring_1/database_setup.sql`
   - Click "Go" to execute
   - The database `agrisense_db` will be created with all tables and sample data

3. **Verify database connection:**
   - Test credentials in `api/config.php`:
     - Host: `localhost`
     - Database: `agrisense_db`
     - User: `root`
     - Password: `` (empty)

### Step 2: .htaccess Configuration ✅

Your `.htaccess` file has been updated to:

- Properly exclude `/api/` endpoints from rewriting
- Allow API calls to pass through to actual PHP files
- Only rewrite non-API routes to `index.html` for SPA routing

**The file now uses the correct base path:** `/update_after_mentoring_1/`

### Step 3: Frontend API Configuration ✅

The JavaScript in `index.html` uses the correct API base URL:

```javascript
const API_BASE_URL = "http://localhost/update_after_mentoring_1/api";
```

This will properly route all API calls to the API directory.

### Step 4: Running the Application

1. **Start XAMPP:**

   - Start Apache and MySQL from XAMPP Control Panel

2. **Access the application:**

   - Navigate to: `http://localhost/update_after_mentoring_1/`
   - Or: `http://127.0.0.1/update_after_mentoring_1/`

3. **Test the API endpoints:**
   - API Test: `http://localhost/update_after_mentoring_1/api/index.php`
   - Should return JSON with endpoints list

## API Endpoints

### Veterinarian Endpoints

- `POST /api/vet_login.php` - Login
- `POST /api/vet_register.php` - Register new vet
- `POST /api/vet_reset_password.php` - Reset password
- `POST /api/prescriptions.php` - Create prescription
- `GET /api/prescriptions.php` - Retrieve prescriptions
- `POST /api/health_history.php` - Record health history
- `GET /api/health_history.php` - Get health records

### Government Endpoints

- `POST /api/gov_login.php` - Government login
- `GET /api/alerts.php` - Get alerts
- `POST /api/alerts.php` - Create alert
- `GET /api/policies.php` - Get policies
- `POST /api/policies.php` - Create policy
- `GET /api/audit_logs.php` - Get audit logs

### Lab & Testing

- `POST /api/mrl_lab_tests.php` - Schedule lab test
- `GET /api/mrl_lab_tests.php` - Get test results
- `POST /api/batches.php` - Manage batches
- `GET /api/batches.php` - Get batch info

### General

- `GET /api/farm_details.php` - Get farm information
- `GET /api/product_info.php` - Get product details
- `GET /api/safety_alerts.php` - Get safety alerts
- `POST /api/ai_analysis.php` - AI symptom analysis
- `GET /api/consultation_requests.php` - Get consultations

## Test Login Credentials

### Veterinarian

- **ID:** VET001, VET002, etc.
- **Password:** demo (or md5 hash: fe01ce2a7fbac8fafaed7c982a04e229)

### Government Officer

- **ID:** GOV001, GOV002, etc.
- **Password:** demo (or md5 hash: fe01ce2a7fbac8fafaed7c982a04e229)

## Troubleshooting

### 404 Errors on API Calls

**Symptom:** API calls return 404 Not Found

**Solution:**

1. Verify `.htaccess` file is in `/update_after_mentoring_1/` directory
2. Enable mod_rewrite in Apache configuration
3. Check that API files exist in `/api/` subdirectory
4. Verify API base URL is correct in JavaScript

### Database Connection Errors

**Symptom:** "Database connection failed"

**Solution:**

1. Verify MySQL is running
2. Check `api/config.php` credentials:
   ```php
   $host = 'localhost';
   $dbname = 'agrisense_db';
   $username = 'root';
   $password = '';
   ```
3. Import database_setup.sql if not done

### Cross-Origin (CORS) Issues

**The API is already configured with CORS headers:**

```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
```

## File Structure

```
update_after_mentoring_1/
├── index.html                 # Main SPA application
├── .htaccess                  # Apache routing configuration
├── database_setup.sql         # Database SQL dump
├── setup.bat / setup.sh       # Setup scripts
├── api/
│   ├── config.php             # Database & CORS configuration
│   ├── index.php              # API status endpoint
│   ├── vet_login.php          # Vet authentication
│   ├── vet_register.php       # Vet registration
│   ├── gov_login.php          # Government login
│   ├── prescriptions.php      # E-prescription management
│   ├── mrl_lab_tests.php      # Lab testing
│   ├── alerts.php             # Alert management
│   ├── policies.php           # Policy management
│   ├── audit_logs.php         # Audit trails
│   ├── health_history.php     # Health records
│   ├── farm_details.php       # Farm information
│   ├── product_info.php       # Product details
│   ├── safety_alerts.php      # Safety alerts
│   ├── batches.php            # Batch management
│   ├── consultation_requests.php  # Consultations
│   └── ai_analysis.php        # AI analysis
└── SETUP_GUIDE.md            # This file
```

## Next Steps

1. ✅ Import database (database_setup.sql)
2. ✅ Check .htaccess configuration
3. ✅ Start XAMPP services
4. ✅ Navigate to application
5. ✅ Test login with provided credentials
6. ✅ Verify all API endpoints working

## Common Issues & Solutions

### Issue: Getting 404 on /api/ endpoints

**Fix:** Make sure .htaccess has the correct RewriteBase and API exclusion rules

### Issue: API returns empty response

**Fix:** Check that the PHP endpoint exists and includes `require_once 'config.php'`

### Issue: Database table not found

**Fix:** Import database_setup.sql through phpMyAdmin

### Issue: CORS errors in console

**Fix:** API already has CORS headers configured, but check browser console for actual error

## Additional Features Included

✅ Three-tier government dashboard (District/State/Central)
✅ MRL compliance tracking
✅ AMU (Antimicrobial Usage) monitoring
✅ E-prescription system with withdrawal periods
✅ Blockchain verification support
✅ QR code verification for consumers
✅ Lab testing integration
✅ AI-powered symptom analysis
✅ Offline mobile app capabilities
✅ Audit trail & blockchain integration
✅ Export compliance certificates

## Support

For issues or questions:

1. Check the API logs in `/api/api_logs.txt`
2. Review browser console for JavaScript errors
3. Verify all database tables are created
4. Confirm API files have correct permissions

---

**Last Updated:** December 8, 2025
**Version:** 1.0.0

# 🚀 Agrisense Portal - Complete Setup & Fix Guide

## Overview

Your Agrisense application is a comprehensive farm management system for monitoring Maximum Residue Limits (MRL) and Antimicrobial Usage (AMU) in livestock farming across India. The 404 errors have been resolved!

## ✅ Issues Fixed

### 1. **API 404 Errors** ✅

**Problem:** API endpoints were returning 404 Not Found
**Solution:** Updated `.htaccess` to properly exclude `/api/` routes from SPA rewriting

### 2. **Routing Configuration** ✅

**Problem:** .htaccess was rewriting all requests to index.html including API calls
**Solution:** Added explicit rules to bypass API directory rewriting

### 3. **Database Setup** ✅

**Problem:** No database created with tables
**Solution:** Created comprehensive SQL dump with all required tables

### 4. **CORS Headers** ✅

**Problem:** Potential cross-origin issues
**Solution:** API already configured with proper CORS headers

## 📋 Quick Start (5 Steps)

### Step 1: Import Database ✅

```
1. Open http://localhost/phpmyadmin
2. Click "Import" tab
3. Select file: /update_after_mentoring_1/database_setup.sql
4. Click "Go"
5. Database 'agrisense_db' will be created automatically
```

### Step 2: Start Services ✅

```
1. Open XAMPP Control Panel
2. Start "Apache" service
3. Start "MySQL" service
4. Both should show green
```

### Step 3: Access Application ✅

```
Navigate to: http://localhost/update_after_mentoring_1/
```

### Step 4: Test API ✅

```
Open: http://localhost/update_after_mentoring_1/api_test_panel.html
Click any test button to verify endpoints working
```

### Step 5: Login ✅

```
Veterinarian:
- ID: VET001 (or VET002, VET003, etc.)
- Password: demo

Government Officer:
- ID: GOV001 (or GOV002, GOV003, etc.)
- Password: demo
```

## 📁 Project Structure

```
update_after_mentoring_1/
│
├── index.html                 # Main SPA Application
├── api_test_panel.html        # API Testing Tool
├── .htaccess                  # ✅ FIXED - Apache Routing
├── database_setup.sql         # ✅ NEW - Database Setup
├── SETUP_GUIDE.md            # ✅ NEW - Detailed Guide
├── README.md                 # ✅ NEW - This File
│
└── api/                       # All API Endpoints
    ├── config.php             # Database & CORS Config
    ├── index.php              # API Status
    ├── vet_login.php          # Veterinarian Login
    ├── vet_register.php       # Vet Registration
    ├── vet_reset_password.php # Password Reset
    ├── gov_login.php          # Government Login
    ├── prescriptions.php      # E-Prescriptions
    ├── health_history.php     # Animal Health Records
    ├── mrl_lab_tests.php      # Lab Testing
    ├── batches.php            # Batch Management
    ├── alerts.php             # Alert System
    ├── farm_alerts.php        # Farm-Specific Alerts
    ├── policies.php           # Policy Management
    ├── audit_logs.php         # Audit Trail
    ├── farm_details.php       # Farm Information
    ├── product_info.php       # Product Details
    ├── safety_alerts.php      # Safety Alerts
    ├── consultation_requests.php  # Vet Consultations
    └── ai_analysis.php        # AI Analysis
```

## 🔌 API Endpoints

### Authentication

```
POST /api/vet_login.php           Login veterinarian
POST /api/vet_register.php        Register new veterinarian
POST /api/vet_reset_password.php  Reset veterinarian password
POST /api/gov_login.php           Government officer login
```

### E-Prescriptions

```
POST /api/prescriptions.php       Create new prescription
GET  /api/prescriptions.php       Get all prescriptions
GET  /api/prescriptions.php?id=ID Get specific prescription
```

### Health Management

```
GET  /api/health_history.php      Get health records
POST /api/health_history.php      Add health record
GET  /api/treatment_history.php   Get treatment history
```

### Lab Testing & MRL

```
GET  /api/mrl_lab_tests.php       Get all MRL tests
POST /api/mrl_lab_tests.php       Schedule new test
GET  /api/batches.php             Get batch info
POST /api/batches.php             Add batch
```

### Farm Management

```
GET  /api/farm_details.php        Get farm information
GET  /api/farm_alerts.php         Get farm alerts
POST /api/farm_alerts.php         Create farm alert
```

### Compliance & Governance

```
GET  /api/policies.php            Get government policies
POST /api/policies.php            Create policy
GET  /api/alerts.php              Get system alerts
POST /api/alerts.php              Create alert
```

### Audit & Analytics

```
GET  /api/audit_logs.php          Get audit logs
GET  /api/product_info.php        Get product information
GET  /api/safety_alerts.php       Get safety alerts
POST /api/ai_analysis.php         Run AI analysis
GET  /api/consultation_requests.php Get consultations
```

## 🧪 Testing the System

### Using API Test Panel (Easiest)

```
1. Open: http://localhost/update_after_mentoring_1/api_test_panel.html
2. Click "Test API Status" button
3. If green checkmark appears, API is working
4. Try other test buttons to verify endpoints
```

### Using Browser Console

```javascript
// Open DevTools (F12) and paste:
const API_BASE_URL = "http://localhost/update_after_mentoring_1/api";

// Test API
fetch(`${API_BASE_URL}/index.php`)
  .then((r) => r.json())
  .then((d) => console.log(d));
```

### Using cURL (Command Line)

```bash
# Test API Status
curl http://localhost/update_after_mentoring_1/api/index.php

# Test Vet Login
curl -X POST http://localhost/update_after_mentoring_1/api/vet_login.php \
  -H "Content-Type: application/json" \
  -d '{"vetId":"VET001","password":"demo"}'
```

## 🔒 Security Notes

### Database Credentials (For Development)

```php
Host: localhost
Database: agrisense_db
Username: root
Password: (empty)
```

### Important: Change Before Production

- Use strong passwords
- Enable password for MySQL root user
- Restrict database access
- Use HTTPS only
- Implement proper authentication tokens

## 🐛 Troubleshooting

### Issue: "API Returns 404"

**Solution:**

1. Check Apache is running
2. Verify `.htaccess` file exists in `/update_after_mentoring_1/`
3. Confirm API files exist in `/api/` folder
4. Check browser console for error details

### Issue: "Database Connection Failed"

**Solution:**

1. Start MySQL service in XAMPP
2. Verify database_setup.sql was imported
3. Check database credentials in `api/config.php`
4. Use phpMyAdmin to confirm database exists

### Issue: "404 on /api/endpoint"

**Reason:** Apache mod_rewrite not working properly
**Solution:**

1. Ensure `.htaccess` file is present
2. Check Apache has `mod_rewrite` enabled
3. Verify `RewriteBase` is set correctly
4. See SETUP_GUIDE.md for details

### Issue: "CORS Errors in Console"

**Solution:**

- API already has CORS headers configured
- This usually means the API endpoint doesn't exist
- Check spelling of endpoint name
- Use API test panel to verify endpoint works

## 📊 Key Features

### Veterinarian Portal

✅ Smart e-prescriptions with withdrawal periods
✅ MRL lab testing integration with FSSAI labs
✅ AI-powered symptom analysis
✅ Tele-veterinary services
✅ Health history tracking
✅ Offline mobile app support

### Consumer Portal

✅ QR code product verification
✅ MRL compliance certificates
✅ Blockchain-verified traceability
✅ Export readiness verification
✅ Product safety information

### Government Portal (Three-Tier)

✅ District level monitoring
✅ State level analytics
✅ Central government dashboard
✅ MRL gatekeeping controls
✅ Lab monitoring and certification
✅ Export compliance management
✅ Fraud detection with AI
✅ Digital audit trail with blockchain

## 📚 Additional Resources

### Configuration Files

- `.htaccess` - Apache routing (includes API exclusion rules)
- `api/config.php` - Database configuration & CORS headers
- `database_setup.sql` - Complete database schema

### Documentation

- `SETUP_GUIDE.md` - Detailed setup instructions
- `START_HERE.md` - Getting started guide
- `CHANGES_SUMMARY.md` - Recent modifications
- `DEPLOYMENT_SUMMARY.md` - Deployment information

### Testing

- `api_test_panel.html` - API endpoint tester
- `debug_login.php` - Login debugging utility
- `test_login.php` - Login testing script

## 🎯 Next Steps

1. ✅ Database imported
2. ✅ API configured
3. ✅ .htaccess fixed
4. ✅ Services running
5. → Test all features
6. → Deploy to production
7. → Configure SSL/HTTPS
8. → Set up backups

## 📞 Support Information

### Common Credentials for Testing

```
Veterinarians:
- VET001 through VET007 with password "demo"

Government Officers:
- GOV001 through GOV005 with password "demo"

Farms:
- FARM-001 through FARM-005 (pre-populated with data)

Districts:
- Maharashtra, Karnataka, Delhi, Tamil Nadu, West Bengal
```

### Checking System Status

1. API Health: `/api/index.php`
2. Database: phpMyAdmin at `localhost/phpmyadmin`
3. Logs: Check `/api/api_logs.txt` for request logs

## 🚀 Production Deployment Checklist

- [ ] Change MySQL root password
- [ ] Set strong database credentials
- [ ] Enable HTTPS/SSL certificate
- [ ] Update API base URL to production domain
- [ ] Configure proper authentication tokens
- [ ] Set up database backups
- [ ] Enable error logging
- [ ] Disable debug mode
- [ ] Configure firewall rules
- [ ] Set up monitoring/alerts
- [ ] Test all API endpoints
- [ ] Backup database before launch
- [ ] Document API changes
- [ ] Set up CDN for static files

## 📝 Version Information

- **Version:** 1.0.0
- **Created:** December 8, 2025
- **Last Updated:** December 8, 2025
- **PHP Version Required:** 7.4+
- **MySQL Version Required:** 5.7+
- **Apache Modules Required:** mod_rewrite

## 📄 License

All rights reserved - Agrisense Portal 2025

---

**🎉 Your Agrisense Portal is now ready to use!**

Start with the quick start guide above, then use the API test panel to verify everything is working.
Need help? Check SETUP_GUIDE.md for detailed instructions.

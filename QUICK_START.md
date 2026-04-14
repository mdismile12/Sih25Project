# 🚀 AGRISENSE PORTAL - QUICK START GUIDE

## ⚡ 5-Minute Quick Start

### 1. Copy Files to XAMPP

```bash
Copy this entire folder to: C:\xampp\htdocs\agrisense\
```

### 2. Create Database

- Open: http://localhost/phpmyadmin
- Create new database named: `agrisense_db`
- Collation: `utf8mb4_unicode_ci`

### 3. Start XAMPP

- Open XAMPP Control Panel
- Click "Start" for Apache
- Click "Start" for MySQL
- Wait for both to show "Running" (green)

### 4. Open Application

```
Browser: http://localhost/agrisense/
```

### 5. Login with Demo Credentials

**Veterinarian Portal:**

- ID: `VET001` or `VET002`
- Password: `demo`

**Government Portal:**

- ID: `GOV001`, `GOV002`, or `GOV003`
- Password: `demo`
- Tier: Select from dropdown

---

## 📁 What's Fixed & Updated

### ✅ Backend (PHP API)

- [x] `config.php` - Localhost database configuration
- [x] `vet_login.php` - Veterinarian authentication
- [x] `vet_register.php` - Registration with validation
- [x] `gov_login.php` - Government authentication
- [x] `prescriptions.php` - Full CRUD + DELETE support
- [x] `health_history.php` - Patient records
- [x] All API endpoints - Proper error handling
- [x] Database auto-initialization
- [x] Demo data fallbacks

### ✅ Frontend (HTML/JavaScript)

- [x] API paths - Changed to relative URLs
- [x] API calls - Support for DELETE, PUT, PATCH
- [x] Error handling - Better error messages
- [x] Demo authentication - Works without database
- [x] Form validation - Client-side checks
- [x] Maps & Charts - Fully functional

### ✅ Configuration

- [x] `.htaccess` - Apache rewrite rules
- [x] CORS headers - Enabled for development
- [x] Error handling - Detailed error messages
- [x] Logging - API request logging
- [x] Database setup - Automatic table creation

---

## 🎯 Features Now Working Locally

### Veterinarian Portal ✓

✅ E-Prescriptions (Create, Edit, Delete)
✅ MRL Lab Testing Scheduling
✅ Medicine Database (20+ medicines)
✅ Withdrawal Period Calculation
✅ AI Decision Support
✅ Health History Access
✅ Prescription Preview & Download
✅ Patient Treatment Records

### Consumer Portal ✓

✅ QR Code Scanning (Simulated)
✅ Product Verification
✅ MRL Compliance Check
✅ Blockchain Verification Display
✅ Full Traceability View

### Government Portal ✓

✅ Three-Tier Dashboard

- District Level
- State Level
- Central Level
  ✅ MRL Gatekeeping
  ✅ Lab Monitoring
  ✅ Heatmap Visualization
  ✅ Fraud Detection
  ✅ Audit Trail
  ✅ Policy Management
  ✅ Export Compliance

---

## 🛠️ Database Setup

### Option 1: Automatic (Recommended)

1. First API call will automatically create tables
2. Demo tables created if tables don't exist
3. All data persists in MySQL

### Option 2: Manual

```sql
-- Run in phpMyAdmin or MySQL CLI
CREATE DATABASE agrisense_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agrisense_db;
-- Tables auto-created on first request
```

---

## 🧪 Testing

### Test API Connectivity

```
URL: http://localhost/agrisense/api/status.php
Expected: JSON response with system status
```

### Test Vet Login

```javascript
// In browser console (F12):
fetch("./api/vet_login.php", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify({ vetId: "VET001", password: "demo" }),
})
  .then((r) => r.json())
  .then(console.log);
```

### Test DELETE Method

```javascript
// Delete prescription
fetch("./api/prescriptions.php", {
  method: "DELETE",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify({ prescription_id: "RX..." }),
})
  .then((r) => r.json())
  .then(console.log);
```

---

## 📋 Troubleshooting

### "Database Connection Failed"

```
Solution:
1. Start MySQL in XAMPP Control Panel
2. Create agrisense_db database in phpMyAdmin
3. Refresh browser
```

### "API Endpoints Not Found (404)"

```
Solution:
1. Check if XAMPP is running
2. Verify URL: http://localhost/agrisense/api/status.php
3. Check file permissions on api/ folder
```

### "Login Not Working"

```
Solution:
1. Use correct demo credentials: VET001/demo or GOV001/demo
2. Clear browser localStorage: DevTools → Application → Clear All
3. Check browser console for errors (F12)
```

### "Maps Not Showing"

```
Solution:
1. Check browser console for JavaScript errors
2. Verify Leaflet.js is loaded
3. Try disabling browser extensions
```

### "JavaScript Errors in Console"

```
Solution:
1. Press F12 to open Developer Tools
2. Check Console tab for error messages
3. Look for API call failures
4. Verify api/ folder files exist
```

---

## 📊 Demo Data Credentials

### Veterinarians

| Vet ID | Name       | Specialization       | Password |
| ------ | ---------- | -------------------- | -------- |
| VET001 | Dr. Sharma | Livestock Specialist | demo     |
| VET002 | Dr. Patel  | Poultry Expert       | demo     |

### Government Users

| Gov ID | Name           | Tier     | Password |
| ------ | -------------- | -------- | -------- |
| GOV001 | District Admin | District | demo     |
| GOV002 | State Admin    | State    | demo     |
| GOV003 | Central Admin  | Central  | demo     |

### Farms

| Farm ID  | Location             | State       | MRL Status |
| -------- | -------------------- | ----------- | ---------- |
| FARM-102 | Green Pastures Dairy | Maharashtra | Compliant  |
| FARM-205 | Happy Hens Poultry   | Delhi       | Pending    |
| FARM-001 | Golden Goat Farm     | Karnataka   | Compliant  |

---

## 🔄 HTTP Methods Support

All endpoints now support:

- ✅ **GET** - Retrieve data
- ✅ **POST** - Create data
- ✅ **PUT** - Update data
- ✅ **PATCH** - Partial update
- ✅ **DELETE** - Remove data (NOW WORKING!)
- ✅ **OPTIONS** - CORS preflight

---

## 📁 Important Files

```
agrisense/
├── index.html              ← Main application
├── README.md               ← Detailed guide
├── DEPLOYMENT_SUMMARY.md   ← Changes made
├── setup.bat               ← Windows setup
├── setup.sh                ← Linux/Mac setup
├── .htaccess               ← Apache config
│
└── api/
    ├── config.php          ← Database setup
    ├── vet_login.php       ← Vet auth
    ├── vet_register.php    ← Vet registration
    ├── gov_login.php       ← Gov auth
    ├── prescriptions.php   ← Prescriptions CRUD
    ├── health_history.php  ← Health records
    ├── status.php          ← API health check
    └── [other endpoints]   ← More features
```

---

## 🚀 Performance Tips

1. **First Load**: Takes 2-3 seconds for maps to load
2. **Smooth Performance**: Works on most modern browsers
3. **Large Files**: Charts render after data loads
4. **Offline Mode**: Some features work offline

---

## 🔒 Security Notes

⚠️ **This is development-only!**

For production, you need:

- HTTPS/SSL encryption
- Proper password hashing (bcrypt)
- Database backups
- API authentication (JWT)
- Rate limiting
- CORS restrictions
- Request validation

---

## 📞 Quick Reference

| Task                | Steps                                               |
| ------------------- | --------------------------------------------------- |
| Start App           | XAMPP (Apache+MySQL) → http://localhost/agrisense/  |
| Login               | Use VET001/demo or GOV001/demo                      |
| Create Prescription | Vet Portal → E-Prescriptions → Fill form → Generate |
| Delete Prescription | Click row → Delete button                           |
| Check API           | http://localhost/agrisense/api/status.php           |
| View Logs           | Check api/api_logs.txt                              |
| Reset Data          | Delete database → Refresh → Auto-recreate           |

---

## ✨ What's New

✅ Works 100% locally - no internet needed  
✅ DELETE method now functional  
✅ All CRUD operations working  
✅ Automatic database setup  
✅ Demo mode with fallback data  
✅ Better error messages  
✅ Proper HTTP method handling  
✅ Comprehensive documentation

---

## 🎓 Learning Resources

All source code is available:

- Frontend: Look in `<script>` section of index.html
- Backend: Check `api/*.php` files
- Database: Tables created in MySQL
- Styling: Tailwind CSS in HTML head

---

## 📈 Next Steps

1. **Explore Features**: Login and try all portals
2. **Create Test Data**: Add prescriptions, check deletion
3. **Review Code**: Check api/ files and JavaScript
4. **Customize**: Modify colors, text, or functionality
5. **Deploy**: Follow production guide in README.md

---

**Status**: ✅ Ready for Local Development  
**Version**: 1.0 (XAMPP Edition)  
**Last Updated**: December 2024

---

## 💡 Pro Tips

- Use browser DevTools (F12) to debug
- Check API responses in Network tab
- Use localStorage for session persistence
- Test with different browsers
- Clear cache if seeing old data

**Questions?** Check README.md for detailed guide!

# Agrisense Portal - Local XAMPP Setup Guide

## Overview

Agrisense is a comprehensive farm management portal with features for:

- **Veterinarians**: E-prescriptions, MRL lab testing, AI support
- **Consumers**: QR verification, product traceability
- **Government**: Three-tier monitoring, MRL gatekeeping, analytics

## Prerequisites

- XAMPP (with PHP 7.2+, MySQL)
- Modern web browser (Chrome, Firefox, Edge)
- Git (optional, for version control)

## Installation Steps

### Step 1: Extract Files to XAMPP

```
Extract the project to: C:\xampp\htdocs\agrisense\
```

### Step 2: Create Database

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Create new database:
   - Name: `agrisense_db`
   - Collation: `utf8mb4_unicode_ci`
3. The tables will be created automatically on first API call

### Step 3: Start XAMPP

```
1. Open XAMPP Control Panel
2. Start Apache and MySQL
3. Verify both are running (green indicators)
```

### Step 4: Access the Application

```
Open in browser: http://localhost/agrisense/
```

## Demo Credentials

### Veterinarian Portal

- **Vet ID**: VET001 or VET002
- **Password**: demo
- Features: Create prescriptions, schedule MRL lab tests, AI support

### Government Portal

- **Government ID**: GOV001, GOV002, or GOV003
- **Password**: demo
- **Tier**: Select from dropdown (District/State/Central)
- Features: View dashboard, manage MRL compliance, audit logs

### Consumer Portal

- No login required
- Scan QR code or enter product ID manually
- View MRL compliance certificates

## Features Overview

### 1. Veterinarian Portal ✓

- **E-Prescriptions**: Create and manage digital prescriptions
- **MRL Lab Testing**: Schedule tests with FSSAI labs
- **AI Decision Support**: Get AI recommendations for treatments
- **Health History**: Access complete animal treatment records
- **Tele-Vet Services**: Connect with farmers via phone
- **Mobile Field App**: Offline data collection capability

### 2. Consumer Portal ✓

- **QR Verification**: Scan product QR codes
- **Product Traceability**: View complete supply chain
- **MRL Compliance**: Check lab test results
- **Blockchain Verification**: Immutable record verification

### 3. Government Portal ✓

- **Three-Tier Dashboard**:
  - District Level: Farm monitoring
  - State Level: Regional analytics
  - Central Level: National policies & trends
- **MRL Gatekeeping**: Block non-compliant batches
- **Lab Monitoring**: Track FSSAI lab performance
- **Heatmaps**: Visualize AMU patterns by region
- **Fraud Detection**: AI-powered anomaly detection
- **Audit Trail**: Blockchain-verified logs
- **Policy Management**: Update MRL guidelines
- **Export Control**: Manage export certifications

## API Endpoints

### Authentication

- `POST /api/vet_login.php` - Veterinarian login
- `POST /api/vet_register.php` - Veterinarian registration
- `POST /api/vet_reset_password.php` - Reset password
- `POST /api/gov_login.php` - Government login

### Prescriptions

- `GET /api/prescriptions.php` - List prescriptions
- `POST /api/prescriptions.php` - Create prescription
- `PUT /api/prescriptions.php` - Update prescription
- `DELETE /api/prescriptions.php` - Delete prescription

### Other Endpoints

- `GET /api/alerts.php` - Get alerts
- `GET /api/farm_alerts.php` - Get farm alerts
- `GET /api/health_history.php` - Health records
- `GET /api/treatment_history.php` - Treatment history
- `GET /api/audit_logs.php` - Audit logs
- `GET /api/policies.php` - Policies
- `POST /api/consultation_requests.php` - Create consultation request
- `GET /api/status.php` - API health check

## Key Technologies

- **Frontend**: HTML5, Tailwind CSS, JavaScript (Vanilla)
- **Maps**: Leaflet.js for farm location mapping
- **Charts**: Chart.js for analytics
- **QR Codes**: QR Code library
- **Backend**: PHP 7.2+, PDO
- **Database**: MySQL with UTF8MB4 encoding

## Important Notes

### Local Development

- All database credentials are set to XAMPP defaults:
  - Host: `localhost`
  - User: `root`
  - Password: (empty)
  - Port: `3306`

### API Calls

- All API calls use relative paths: `./api/endpoint.php`
- CORS headers enabled for cross-origin requests
- JSON request/response format

### Database Initialization

- Tables are created automatically via `config.php`
- Demo data is included in responses
- All tables use UTF8MB4 for Unicode support

### DELETE Method Support

- ✓ DELETE requests fully supported
- ✓ All CRUD operations working
- ✓ No restrictions on local environment

## Troubleshooting

### Database Connection Error

```
Solution:
1. Check XAMPP MySQL is running
2. Create agrisense_db database
3. Verify credentials in api/config.php
```

### API Endpoints Returning Empty

```
Solution:
1. Check browser console for errors
2. Verify API is accessible: http://localhost/agrisense/api/status.php
3. Check server error logs
```

### Login Not Working

```
Solution:
1. Use demo credentials (VET001, GOV001, etc.)
2. Password: 'demo'
3. Check browser localStorage settings
```

### Features Not Working

```
Solution:
1. Open browser DevTools (F12)
2. Check Console tab for JavaScript errors
3. Check Network tab for failed API calls
4. Verify API endpoints exist
```

## Performance Tips

1. **First Load**: Allow 2-3 seconds for map initialization
2. **Large Data**: Heatmaps load data dynamically
3. **Offline**: Mobile app features work offline, sync when online

## Production Deployment

For production deployment:

1. Update `config.php` with real database credentials
2. Use proper password hashing (bcrypt instead of MD5)
3. Enable HTTPS/SSL
4. Configure proper CORS headers
5. Set up proper authentication & authorization
6. Regular database backups
7. Monitor API logs

## File Structure

```
agrisense/
├── index.html              (Main application)
├── api/
│   ├── config.php         (Database config & helpers)
│   ├── vet_login.php      (Vet authentication)
│   ├── vet_register.php   (Vet registration)
│   ├── vet_reset_password.php
│   ├── gov_login.php      (Government authentication)
│   ├── prescriptions.php  (Prescription management)
│   ├── alerts.php         (Alert management)
│   ├── farm_alerts.php
│   ├── health_history.php
│   ├── treatment_history.php
│   ├── audit_logs.php
│   ├── policies.php
│   ├── consultation_requests.php
│   ├── status.php         (API health check)
│   └── [other endpoints]
├── README.md              (This file)
└── UPDATE_INSTRUCTIONS.txt
```

## Support & Debugging

### Enable Debug Mode

All API errors include detailed messages. Check browser console:

```javascript
// In browser DevTools Console
localStorage.debug = true;
```

### API Test Script

```javascript
// Test API connectivity
fetch("./api/status.php")
  .then((r) => r.json())
  .then((d) => console.log(d));
```

## Security Notes

⚠️ **This is a development setup!** For production:

- Use HTTPS only
- Implement proper authentication
- Use environment variables for secrets
- Regular security audits
- Keep dependencies updated
- Implement rate limiting
- Add request validation

## Contact & Support

For issues or questions:

1. Check this README first
2. Review browser console errors
3. Check API status at `/api/status.php`
4. Enable logging in `api/` directory

---

**Version**: 1.0  
**Last Updated**: December 2024  
**Status**: Ready for local development

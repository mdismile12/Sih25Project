@echo off
REM ============================================
REM MEDICINE SYSTEM SETUP BATCH SCRIPT
REM ============================================
REM This script imports medicines into database
REM and verifies the setup is complete

echo.
echo ============================================
echo   AGRISENSE MEDICINE SYSTEM SETUP
echo ============================================
echo.

REM Check if MySQL is accessible
echo Checking MySQL connection...
mysql -u root agrisense_db -e "SELECT COUNT(*) as medicines FROM medicines;" 2>nul

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo MySQL error detected. Attempting to import medicines...
    echo.
)

REM Get the directory where this script is located
cd /d %~dp0

echo.
echo Importing medicines data...
echo Please wait...
echo.

REM Import the medicines SQL file
mysql -u root agrisense_db < add_medicines.sql

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ============================================
    echo SUCCESS! Medicines imported successfully
    echo ============================================
    echo.
    
    REM Verify the import
    echo Verifying medicines in database...
    mysql -u root agrisense_db -e "SELECT COUNT(*) as total_medicines, COUNT(DISTINCT type) as categories FROM medicines WHERE approved = 1;"
    
    echo.
    echo ============================================
    echo NEXT STEPS:
    echo ============================================
    echo.
    echo 1. Open Test Page:
    echo    http://localhost/update_after_mentoring_1/test_medicine_integration.html
    echo.
    echo 2. Open Portal:
    echo    http://localhost/update_after_mentoring_1/index.html
    echo.
    echo 3. Test Medicine Selection:
    echo    - Login as Vet
    echo    - Go to E-Prescriptions
    echo    - Add Medicine
    echo    - Verify dosage calculates
    echo.
    echo ============================================
    
) else (
    echo.
    echo ============================================
    echo ERROR: Import failed
    echo ============================================
    echo.
    echo Please verify:
    echo - MySQL service is running
    echo - Database 'agrisense_db' exists
    echo - add_medicines.sql file exists
    echo - You have proper permissions
    echo.
)

pause

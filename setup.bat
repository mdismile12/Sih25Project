@echo off
REM Agrisense Portal - XAMPP Setup Script (Windows)
REM This script initializes the Agrisense Portal for local XAMPP development

echo ============================================
echo Agrisense Portal - XAMPP Setup Script
echo ============================================
echo.

REM Define XAMPP paths
set XAMPP_PATH=C:\xampp
set PROJECT_PATH=%XAMPP_PATH%\htdocs\agrisense

echo XAMPP Path: %XAMPP_PATH%
echo Project Path: %PROJECT_PATH%
echo.

REM Step 1: Create directory structure
echo [1/4] Creating directory structure...
if not exist "%PROJECT_PATH%\api" mkdir "%PROJECT_PATH%\api"
if not exist "%PROJECT_PATH%\logs" mkdir "%PROJECT_PATH%\logs"
echo Directories created/verified
echo.

REM Step 2: Check if MySQL is accessible
echo [2/4] Checking MySQL...
where mysql >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    echo MySQL command found
    echo Creating database 'agrisense_db'...
    echo CREATE DATABASE IF NOT EXISTS agrisense_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; | mysql -u root
    echo Database creation attempted
) else (
    echo MySQL command not found in PATH
    echo Make sure XAMPP MySQL is running and C:\xampp\mysql\bin is in PATH
)
echo.

REM Step 3: Check PHP
echo [3/4] Checking PHP...
where php >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    for /f "tokens=*" %%i in ('php --version ^| find /v ""') do set PHP_VERSION=%%i
    echo PHP found: %PHP_VERSION%
) else (
    echo PHP not found - ensure XAMPP is properly installed
)
echo.

REM Step 4: Create configuration files
echo [4/4] Creating configuration files...
if not exist "%PROJECT_PATH%\.htaccess" (
    echo .htaccess file not found - should be created manually
) else (
    echo .htaccess already exists
)
echo.

REM Create a test file
echo Creating test.php for verification...
(
echo ^<?php
echo echo "XFF::: PHP is working^<br^>";
echo echo "XFF::: Version: " . phpversion() . "^<br^>";
echo echo "XFF::: Extensions: ";
echo $exts = ['mysqli', 'pdo', 'pdo_mysql', 'json'];
echo foreach ($exts as $ext) {
echo     echo extension_loaded($ext) ? "OK $ext " : "FAIL $ext ";
echo }
echo echo "^<br^>";
echo ?^>
) > "%PROJECT_PATH%\test.php"
echo Test file created at: %PROJECT_PATH%\test.php
echo.

echo ============================================
echo Setup Complete!
echo ============================================
echo.
echo Next Steps:
echo 1. Start XAMPP Control Panel
echo    - Start Apache
echo    - Start MySQL
echo 2. Open in browser: http://localhost/agrisense/
echo 3. Use demo credentials:
echo    - Vet: VET001 / demo
echo    - Gov: GOV001 / demo
echo.
echo Test API at: http://localhost/agrisense/api/status.php
echo Access phpMyAdmin: http://localhost/phpmyadmin
echo.
echo If you see errors:
echo 1. Check XAMPP Control Panel - ensure Apache and MySQL are green
echo 2. Open http://localhost/agrisense/test.php to verify PHP
echo 3. Create database manually in phpMyAdmin: agrisense_db
echo 4. Check file permissions on the agrisense folder
echo.
pause

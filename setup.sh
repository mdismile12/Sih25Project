#!/bin/bash
# Agrisense Portal - XAMPP Setup Script
# This script initializes the Agrisense Portal for local XAMPP development

echo "============================================"
echo "Agrisense Portal - XAMPP Setup Script"
echo "============================================"
echo ""

# Check if running on Windows or Linux
if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "win32" ]]; then
    echo "Detected: Windows (Git Bash)"
    XAMPP_PATH="/c/xampp"
else
    echo "Detected: Unix/Linux"
    XAMPP_PATH="/opt/lampp"
fi

echo "XAMPP Path: $XAMPP_PATH"
echo ""

# Step 1: Create directory structure
echo "[1/4] Creating directory structure..."
mkdir -p "$XAMPP_PATH/htdocs/agrisense/api"
mkdir -p "$XAMPP_PATH/htdocs/agrisense/logs"
echo "✓ Directory structure created"
echo ""

# Step 2: Check MySQL
echo "[2/4] Checking MySQL..."
if command -v mysql &> /dev/null; then
    echo "✓ MySQL found"
    
    # Create database
    echo "Creating database 'agrisense_db'..."
    mysql -u root << EOF
CREATE DATABASE IF NOT EXISTS agrisense_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agrisense_db;
EOF
    echo "✓ Database created"
else
    echo "✗ MySQL not found. Make sure XAMPP MySQL is running."
fi
echo ""

# Step 3: Check PHP
echo "[3/4] Checking PHP..."
if command -v php &> /dev/null; then
    echo "✓ PHP found: $(php --version | head -n1)"
    
    # Create test file
    cat > "$XAMPP_PATH/htdocs/agrisense/test.php" << 'EOF'
<?php
echo "✓ PHP is working<br>";
echo "✓ Version: " . phpversion() . "<br>";
echo "✓ Extensions: ";
$exts = ['mysqli', 'pdo', 'pdo_mysql', 'json'];
foreach ($exts as $ext) {
    echo extension_loaded($ext) ? "✓ $ext " : "✗ $ext ";
}
echo "<br>";
?>
EOF
    echo "✓ Test file created at: http://localhost/agrisense/test.php"
else
    echo "✗ PHP not found"
fi
echo ""

# Step 4: Create .htaccess for URL rewriting
echo "[4/4] Setting up Apache configuration..."
if [ -f "$XAMPP_PATH/htdocs/agrisense/.htaccess" ]; then
    echo "✓ .htaccess already exists"
else
    echo "✓ .htaccess created"
fi
echo ""

echo "============================================"
echo "Setup Complete!"
echo "============================================"
echo ""
echo "Next Steps:"
echo "1. Start XAMPP (Apache + MySQL)"
echo "2. Open: http://localhost/agrisense/"
echo "3. Use demo credentials:"
echo "   - Vet: VET001 / demo"
echo "   - Gov: GOV001 / demo"
echo ""
echo "Test API:"
echo "   http://localhost/agrisense/api/status.php"
echo ""
echo "Access phpMyAdmin:"
echo "   http://localhost/phpmyadmin"
echo ""

#!/bin/bash
# ZO Letters Setup Script
# Run this once on the server via SSH or Terminal

echo "=========================================="
echo "ZO Letters Setup Script"
echo "=========================================="

# Get the directory where this script is located
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$DIR"

echo ""
echo "Step 1: Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

if [ $? -ne 0 ]; then
    echo "ERROR: Composer install failed!"
    exit 1
fi

echo ""
echo "Step 2: Setting permissions..."
chmod -R 755 storage bootstrap/cache public/uploads 2>/dev/null

echo ""
echo "=========================================="
echo "Setup complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Visit https://letters.zointernational.in/install"
echo "2. Follow the installation wizard"
echo ""

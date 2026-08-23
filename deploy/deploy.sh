#!/bin/bash
# Deployment script for web-etiket-gunung-kerinci
# Usage: bash deploy/deploy.sh

set -e

APP_DIR="/var/www/web-etiket-gunung-kerinci"
echo "=== Deploying E-Tiket Gunung Kerinci ==="

cd "$APP_DIR"

# Pull latest code
echo "[1/8] Pulling latest code..."
git pull origin peringatan-dini

# Install PHP dependencies
echo "[2/8] Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build
echo "[3/8] Building frontend assets..."
npm install --ignore-scripts
npm run build

# Run migrations
echo "[4/8] Running migrations..."
php artisan migrate --force

# Cache configuration
echo "[5/8] Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Restart queue workers (graceful)
echo "[6/8] Restarting queue workers..."
php artisan queue:restart

# Set permissions
echo "[7/8] Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Reload services
echo "[8/8] Reloading services..."
sudo supervisorctl restart queue-worker:*
# Note: Only restart Reverb if broadcasting config changed
# sudo supervisorctl restart reverb
sudo systemctl reload nginx

echo "=== Deployment complete ==="

#!/bin/bash
set -e

echo "Starting application setup..."

cd /var/www/html

# Install composer dependencies if vendor doesn't exist
if [ ! -d vendor ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

# Generate or update .env.local.php
echo "Dumping environment variables..."
composer dump-env dev --no-interaction

echo "Setup complete! Starting PHP-FPM..."

# Start PHP-FPM
exec php-fpm
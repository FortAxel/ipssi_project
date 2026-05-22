#!/bin/sh
set -e

UPLOAD_DIR="/var/www/html/public/images/uploads"
mkdir -p "$UPLOAD_DIR"
chown -R www-data:www-data "$UPLOAD_DIR" 2>/dev/null || chmod 775 "$UPLOAD_DIR"

exec docker-php-entrypoint "$@"

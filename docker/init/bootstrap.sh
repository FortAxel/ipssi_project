#!/bin/bash
set -euo pipefail

cd /var/www/html

echo "==> Storybook Kids — database bootstrap"

if [ ! -f vendor/autoload.php ]; then
  echo "==> Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist
fi

if [ ! -f config/jwt/private.pem ]; then
  echo "==> Generating JWT key pair..."
  php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction
fi

echo "==> Ensuring upload directory is writable..."
mkdir -p public/images/uploads
chmod 1777 public/images/uploads 2>/dev/null || true

echo "==> Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

STORY_COUNT="$(php bin/console dbal:run-sql "SELECT COUNT(*) FROM story" 2>/dev/null | grep -oE '[0-9]+' | tail -1 || true)"
STORY_COUNT="${STORY_COUNT:-0}"

if [ "$STORY_COUNT" = "0" ]; then
  echo "==> Seeding users and 5 French stories from backend/data/stories..."
  php bin/console doctrine:fixtures:load --no-interaction
else
  echo "==> Database already contains ${STORY_COUNT} story/stories — skip fixtures."
fi

echo "==> Bootstrap complete."

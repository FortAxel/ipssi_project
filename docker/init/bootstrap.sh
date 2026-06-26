#!/bin/bash
set -euo pipefail

APP_ENV="${APP_ENV:-dev}"
MYSQL_HOST="${MYSQL_HOST:-mysql}"
MYSQL_DATABASE="${MYSQL_DATABASE:-storybook}"
MYSQL_USER="${MYSQL_USER:-storybook}"
MYSQL_PASSWORD="${MYSQL_PASSWORD:?MYSQL_PASSWORD is required}"

echo "==> Waiting for MySQL to accept connections..."
until php -r "
  new PDO(
    'mysql:host=${MYSQL_HOST};dbname=${MYSQL_DATABASE}',
    '${MYSQL_USER}',
    '${MYSQL_PASSWORD}'
  );
" 2>/dev/null; do
  sleep 2
done
echo "==> MySQL ready!"

cd /var/www/html

echo "==> Storybook Kids — bootstrap (${APP_ENV})"

if [ "$APP_ENV" = "prod" ]; then
  echo "==> Installing Composer dependencies (no dev)..."
  composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader
  CONSOLE_ENV=(--env=prod)
else
  echo "==> Installing Composer dependencies (with dev)..."
  composer install --no-interaction --prefer-dist
  CONSOLE_ENV=()
fi

ensure_jwt_keys() {
  local need_generate=0

  if [ ! -f config/jwt/private.pem ] || [ ! -f config/jwt/public.pem ]; then
    need_generate=1
  elif ! openssl pkey -in config/jwt/private.pem -passin "pass:${JWT_PASSPHRASE}" -noout 2>/dev/null; then
    echo "==> JWT keys do not match JWT_PASSPHRASE — regenerating..."
    need_generate=1
  fi

  if [ "$need_generate" = "1" ]; then
    echo "==> Generating JWT key pair..."
    php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction "${CONSOLE_ENV[@]}"
  fi
}

ensure_jwt_keys

echo "==> Ensuring upload directory exists..."
mkdir -p public/images/uploads
chown -R www-data:www-data public/images/uploads 2>/dev/null || chmod 775 public/images/uploads 2>/dev/null || true

echo "==> Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction "${CONSOLE_ENV[@]}"

STORY_COUNT="$(php bin/console dbal:run-sql "SELECT COUNT(*) FROM story" "${CONSOLE_ENV[@]}" 2>/dev/null | grep -oE '[0-9]+' | tail -1 || true)"
STORY_COUNT="${STORY_COUNT:-0}"

if [ "$STORY_COUNT" = "0" ]; then
  echo "==> Seeding users and stories..."
  php bin/console doctrine:fixtures:load --no-interaction "${CONSOLE_ENV[@]}"
else
  echo "==> Database already contains ${STORY_COUNT} story/stories — skip fixtures."
fi

if [ "$APP_ENV" = "prod" ]; then
  echo "==> Warming up Symfony cache..."
  php bin/console cache:warmup --env=prod
fi

echo "==> Bootstrap complete."

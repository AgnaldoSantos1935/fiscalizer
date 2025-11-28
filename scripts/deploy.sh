#!/usr/bin/env bash
set -euo pipefail

# Usage:
# ENV=production APP_URL=https://seu-dominio BUILD_ASSETS=true NO_MIGRATE=false bash scripts/deploy.sh

ENV=${ENV:-production}
APP_URL=${APP_URL:-http://example.com}
BUILD_ASSETS=${BUILD_ASSETS:-true}
NO_MIGRATE=${NO_MIGRATE:-false}

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

log(){ echo "[deploy] $*"; }

require(){ command -v "$1" >/dev/null 2>&1 || { echo "Missing dependency: $1"; exit 1; }; }
require php
require composer

if [ ! -f .env ] && [ -f .env.example ]; then cp .env.example .env; fi

# Update .env values
touch .env
if grep -q '^APP_ENV=' .env; then sed -i "s/^APP_ENV=.*/APP_ENV=${ENV}/" .env; else echo "APP_ENV=${ENV}" >> .env; fi
if grep -q '^APP_DEBUG=' .env; then sed -i "s/^APP_DEBUG=.*/APP_DEBUG=false/" .env; else echo "APP_DEBUG=false" >> .env; fi
if grep -q '^APP_URL=' .env; then sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|" .env; else echo "APP_URL=${APP_URL}" >> .env; fi

# Generate key if missing
if ! grep -q '^APP_KEY=base64:' .env; then
  log "Generating APP_KEY"
  php artisan key:generate || true
fi

log "Composer install"
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

if [ "$BUILD_ASSETS" = "true" ]; then
  if command -v npm >/dev/null 2>&1; then
    log "Installing Node dependencies"
    (npm ci || npm install)
    log "Building assets"
    npm run build
  else
    log "npm not found; skip assets build. Build locally and upload public/build"
  fi
fi

log "Storage link"
php artisan storage:link || true

if [ "$NO_MIGRATE" != "true" ]; then
  log "Running migrations"
  php artisan migrate --force
fi

log "Caching config/routes/views"
php artisan config:cache
php artisan route:cache
php artisan view:cache

mkdir -p storage bootstrap/cache
log "Adjusting permissions"
sudo chown -R www-data:www-data storage bootstrap/cache || true
sudo chmod -R ug+rwx storage bootstrap/cache || true

log "Done"


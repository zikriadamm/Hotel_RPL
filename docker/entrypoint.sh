#!/usr/bin/env bash
set -e

PORT_TO_USE="${PORT:-8080}"
sed -ri "s/Listen [0-9]+/Listen ${PORT_TO_USE}/g" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT_TO_USE}>/g" /etc/apache2/sites-available/000-default.conf

DB_PATH="${DB_DATABASE:-/data/database.sqlite}"
mkdir -p "$(dirname "$DB_PATH")" /var/www/html/storage/framework/cache/data /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/logs /var/www/html/bootstrap/cache
touch "$DB_PATH"

chown -R www-data:www-data "$(dirname "$DB_PATH")" /var/www/html/storage /var/www/html/bootstrap/cache

php artisan config:clear || true
php artisan migrate --force

USER_COUNT=$(php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); try { echo \App\Models\User::count(); } catch (\Throwable \$e) { echo '0'; }")
if [ "${USER_COUNT:-0}" = "0" ]; then
    php artisan db:seed --force
fi

php artisan route:cache || true
php artisan view:cache || true

rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*
a2enmod mpm_prefork >/dev/null 2>&1 || true

exec apache2-foreground

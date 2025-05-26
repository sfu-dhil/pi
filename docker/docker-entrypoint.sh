#!/bin/bash
set -e

if [[ -r ".env" && -w ".env" ]]; then
    chown -R www-data:www-data .env
    chmod -R 775 .env
fi

# app specific setup here
./bin/console doctrine:migrations:migrate --no-interaction --no-ansi --allow-no-migration
./bin/console assets:install --symlink --relative
rm -rf var/cache/prod/* var/cache/dev/* var/cache/test/*

apache2-foreground
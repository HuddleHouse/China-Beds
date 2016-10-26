#!/usr/bin/env bash
git pull
composer install --no-dev --optimize-autoloader
sudo service apache2 restart
sudo rm -Rf var/cache/*
sudo chmod -Rf 777 var/cache
sudo chmod -Rf 777 var/logs
php app/console cache:clear --env=prod --no-debug
php app/console assetic:dump --env=prod --no-debug
php app/console doctrine:schema:update --force --env=prod --no-debug
sudo chmod -Rf 777 var/cache
sudo chmod -Rf 777 var/logs

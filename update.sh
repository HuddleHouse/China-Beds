#!/usr/bin/env bash
export SYMFONY_ENV=prod
git pull
sudo rm -Rf var/cache/*
composer install --optimize-autoloader
sudo service apache2 restart
sudo chmod -Rf 777 var/cache
sudo chmod -Rf 777 var/logs
php bin/console cache:clear --no-debug
php bin/console assetic:dump --no-debug
php bin/console doctrine:schema:update --force --no-debug
sudo chmod -Rf 777 var/cache
sudo chmod -Rf 777 var/logs
#export SYMFONY_ENV=prod
#git pull
#sudo rm -Rf var/cache/*
#composer install --no-dev --optimize-autoloader
#sudo service apache2 restart
#sudo chmod -Rf 777 var/cache
#sudo chmod -Rf 777 var/logs
#php bin/console cache:clear --env=prod --no-debug
#php bin/console assetic:dump --env=prod --no-debug
#php bin/console doctrine:schema:update --force --env=prod --no-debug
#sudo chmod -Rf 777 var/cache
#sudo chmod -Rf 777 var/logs

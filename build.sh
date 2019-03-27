#!/usr/bin/env bash

cp -f .env ./backend-js/
cp -f .env ./backend-php/

docker-compose up --force-recreate --build -t 30 -d

sleep 5

docker exec moneywaster_backend-fpm_1 php composer install -n -vvv
docker exec moneywaster_backend-fpm_1 ./bin/console doctrine:migrations:migrate -n
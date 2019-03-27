#!/usr/bin/env bash

cp -f .env ./backend-js/
cp -f .env ./backend-php/

docker-compose up --build -d

docker exec moneywaster_backend-php-nginx_1 php composer install -n -vvv
docker exec moneywaster_backend-php-nginx_1 ./bin/console doctrine:migrations:migrate -n
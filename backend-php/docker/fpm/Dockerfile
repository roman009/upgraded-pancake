FROM php:7.3-fpm
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install -j$(nproc) ctype \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql \
    && docker-php-ext-install -j$(nproc) json \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql
# VOLUME /app/
# COPY . /app/
WORKDIR /app
RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;
# RUN rm ./.env.local
# RUN sed -i "s|APP_ENV=dev|APP_ENV=prod|g" ./.env
# RUN php composer install -n -vvv
# RUN ./wait-for-it.sh db:3306
# RUN ./bin/console doctrine:migrations:migrate -n
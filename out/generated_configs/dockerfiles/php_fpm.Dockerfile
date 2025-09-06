FROM php:7.4-fpm

RUN sed -i 's/^listen = .*/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf

RUN apt-get update && \
    apt-get install -y --no-install-recommends libpq-dev && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_pgsql pgsql

RUN addgroup --system --gid 1000 rockylinux && \
    adduser --system --uid 1000 --gid 1000 rockylinux

USER rockylinux

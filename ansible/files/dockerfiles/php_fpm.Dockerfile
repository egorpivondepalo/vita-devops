FROM php:7.4-fpm

RUN sed -i 's/^listen = .*/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf

RUN apt-get update && \
    apt-get install -y --no-install-recommends libpq-dev && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_pgsql pgsql

RUN mkdir -p /root/vita/logs/php_fpm/php-fpm

RUN mkdir -p /var/log/php-fpm && \
    chown -R www-data:www-data /var/log/php-fpm && \
    chmod -R 755 /var/log/php-fpm

RUN sed -i 's|;error_log = log/php-fpm.log|error_log = /var/log/php-fpm/php-fpm.log|' /usr/local/etc/php-fpm.conf && \
    sed -i 's|;log_level = notice|log_level = notice|' /usr/local/etc/php-fpm.conf && \
    sed -i 's|;daemonize = yes|daemonize = no|' /usr/local/etc/php-fpm.conf

RUN sed -i 's|;access.log = log/\$pool.access.log|access.log = /var/log/php-access.log|' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's|;slowlog = log/\$pool.log.slow|slowlog = /var/log/php-slow.log|' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's|;catch_workers_output = yes|catch_workers_output = yes|' /usr/local/etc/php-fpm.d/www.conf && \
    echo 'php_admin_flag[log_errors] = on' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'php_admin_value[error_log] = /var/log/php-errors.log' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'php_admin_value[error_reporting] = E_ALL' >> /usr/local/etc/php-fpm.d/www.conf

RUN touch /var/log/php-fpm/php-fpm.log && \
    touch /var/log/php-access.log && \
    touch /var/log/php-slow.log && \
    touch /var/log/php-errors.log && \
    chown www-data:www-data /var/log/php-fpm/php-fpm.log /var/log/php-access.log /var/log/php-slow.log /var/log/php-errors.log && \
    chmod 644 /var/log/php-fpm/php-fpm.log /var/log/php-access.log /var/log/php-slow.log /var/log/php-errors.log

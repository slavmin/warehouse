FROM php:8.4-fpm

ARG HOST_UID=1000
ARG HOST_GID=1000
ARG HOST_USER=www-data
ARG HOST_GROUP=www-data
ARG HOST_TZ=UTC
ARG APP_DIR=/var/www/html/app
ARG XDEBUG_INSTALL=false

RUN mkdir -p $APP_DIR \
    && usermod -u $HOST_UID $HOST_USER \
    && groupmod -g $HOST_GID $HOST_GROUP \
    && chown -R $HOST_USER:$HOST_GROUP $APP_DIR /run /var/log

ENV SUPERVISOR_PHP_USER=$HOST_USER
ENV SUPERVISOR_PHP_DIRECTORY=$APP_DIR
ENV TZ=$HOST_TZ
RUN echo "date.timezone = ${TZ:-UTC}" > ${PHP_INI_DIR}/conf.d/timezone.ini

WORKDIR $APP_DIR

# Install packages
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev libz-dev \
    libmemcached-dev libssl-dev libonig-dev libxml2-dev libicu-dev \
    libreadline-dev unzip git supervisor \
    libpq-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install PHP extensions
RUN docker-php-ext-install zip opcache pdo_mysql intl pcntl \
    && docker-php-ext-install pdo_pgsql pgsql \
    && pecl install redis memcached \
    && docker-php-ext-enable redis memcached pgsql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer \
    --2.7.1 \
    --quiet

# Copy config files
COPY ./php/config/php.ini ${PHP_INI_DIR}/php.ini
COPY ./php/config/fpm-pool.conf ${PHP_INI_DIR}/php-fpm.d/www.conf
COPY ./php/config/xdebug.ini ${PHP_INI_DIR}/conf.d/xdebug.ini
COPY ./php/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install Xdebug
RUN if [ ${XDEBUG_INSTALL} = true ]; then \
    pecl install xdebug && docker-php-ext-enable xdebug \
;fi

# Switch to use a non-root user
USER $HOST_USER

# Healthcheck
HEALTHCHECK --interval=10s --timeout=3s --retries=3 \
    CMD SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET \
    cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1

# Start Supervisord
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
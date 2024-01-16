# Use the official PHP 7.2 image
FROM php:7.2-fpm

# Install common extensions
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    default-mysql-client \
    mariadb-server \
    libzip-dev \
    zip \
    unzip \
    procps \
    vim

# Install Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install other dependencies and clean up
RUN docker-php-ext-install mysqli pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set MySQL root password
ENV MYSQL_ROOT_PASSWORD=123456

# Set working directory
WORKDIR /var/www

# Set file permissions
RUN chown -R www-data:www-data .

# Copy Project to the working directory
COPY . ./shop/

RUN chmod -R 777 ./shop/logs/ && chmod -R 755 ./shop/app/shell

RUN cd ./shop && composer update

COPY ./docs/Nginx_conf/local.shop_api.com.conf /etc/nginx/conf.d/local.shop_api.com.conf

# Import sql file if you used mysql images
#COPY ./docs/Databases/shop.sql /docker-entrypoint-initdb.d/
#RUN chmod 755 /docker-entrypoint-initdb.d/shop.sql

# complicated Supervisord setting file
COPY ./docs/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# load Supervisord and shell
CMD /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf


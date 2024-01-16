# 使用官方的PHP 7.2镜像
FROM php:7.2-fpm

# 安装常用扩展和依赖
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

# 安装 Redis 扩展
RUN pecl install redis \
    && docker-php-ext-enable redis

# 安装其他依赖并清理
RUN docker-php-ext-install mysqli pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 安装 Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 设置MySQL的root密码
ENV MYSQL_ROOT_PASSWORD=123456

# 设置工作目录
WORKDIR /var/www

# 设置文件权限
RUN chown -R www-data:www-data .

# 复制PHP应用到工作目录
COPY . ./shop

# 修改项目目录下log文件夹的权限
RUN chmod -R 755 ./shop/logs && chmod -R 755 ./shop/app/shell

# 复制Nginx配置文件到配置文件目录
COPY ./docs/Nginx_conf/local.shop_api.com.conf /etc/nginx/conf.d/local.shop_api.com.conf

# 导入MySQL数据库
#COPY ./docs/Databases/shop.sql /docker-entrypoint-initdb.d/
#RUN chmod 755 /docker-entrypoint-initdb.d/shop.sql

# 复制Supervisord配置文件
COPY ./docs/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# 启动Supervisord和shell
CMD /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf


# 使用Alpine Linux为基础镜像
FROM php:7.2-fpm-alpine

# 设置MySQL的root密码为环境变量中的值，默认为123456
ENV MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD:-123456}
ENV MYSQL_PORT=${MYSQL_PORT:-3306}

# 安装nginx和mysql客户端
RUN apk --no-cache add nginx mysql-client

# 安装Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

# 复制Nginx配置文件到配置文件目录
COPY ./docs/Nginx_conf/local.shop_api.com.conf /etc/nginx/conf.d/local.shop_api.com.conf

# 创建项目目录
RUN mkdir -p /var/www/html

# 修改项目目录下log文件夹的权限
RUN chmod -R 755 /var/www/html/log

# 暴露80端口
EXPOSE 80

# 启动Nginx和PHP-FPM服务
CMD ["nginx", "-g", "daemon off;"]

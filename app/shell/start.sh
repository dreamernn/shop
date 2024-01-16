#!/bin/bash

# MySQL connect
MYSQL_HOST="localhost"
MYSQL_PORT="3306"
MYSQL_USER="root"
MYSQL_PASSWORD="123456"

# SQL 文件路径
SQL_FILE="/var/www/shop/docs/Databases/shop.sql"

# 导入 SQL 文件
mysql -h $MYSQL_HOST -P $MYSQL_PORT -u $MYSQL_USER -p$MYSQL_PASSWORD < $SQL_FILE

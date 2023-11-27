<?php

return [
    'log_path'     => env('APP_LOG_BASE_PATH', ''),
    'log_level'    => env('APP_LOG_LEVEL', '4'),
    'api_domain'   => env('API_DOMAIN', 'http://local.shop_api.com'),
    'customer_url' => env('HTML_DOMAIN').'/html/customer/',
    'admin_url'    => env('HTML_DOMAIN').'/html/admin/',
    'database'     => [
        'default' => [
            'dbname'   => env('DB_DATABASE', 'shop'),
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '3306'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', '123456'),
            'charset'  => env('DB_CHARSET', 'utf8mb4'),
        ],
    ],
    'redis'        => [
        'host'           => env('REDIS_HOST', '127.0.0.1'),
        'port'           => env('REDIS_PORT', '6379'),
        'persistent'     => env('REDIS_PERSISTENT', '1'),
        'store_database' => env('REDIS_STORE_DATABASE', '0'),
        'password'       => env('REDIS_PASSWORD', ''),
    ],
];

<?php

use Illuminate\Support\Str;
use Pdo\Mysql;

return [

    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),

            'host' => env('DB_HOST'),
            'port' => env('DB_PORT', '3306'),

            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),

            'unix_socket' => env('DB_SOCKET', ''),

            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',

            'prefix' => '',
            'prefix_indexes' => true,

            'strict' => true,
            'engine' => null,

            'options' => extension_loaded('pdo_mysql') ? array_filter([
                // 🔥 FIX penting (timeout biar ga "server has gone away")
                PDO::ATTR_TIMEOUT => 5,

                // SSL (Railway kadang butuh ini)
                (PHP_VERSION_ID >= 80500 
                    ? Mysql::ATTR_SSL_CA 
                    : PDO::MYSQL_ATTR_SSL_CA
                ) => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

    ],

    'migrations' => [
        'table' => 'migrations',
    ],

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => 'redis',
            'prefix' => Str::slug((string) env('APP_NAME', 'laravel')).'-database-',
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => '0',
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => '1',
        ],

    ],

];
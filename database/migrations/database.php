<?php

use Illuminate\Support\Str;

// ==============================================================
// File ini adalah versi ringkas config/database.php Laravel,
// difokuskan pada koneksi 'mysql' yang dipakai project ini.
// Kalau project Laravel kamu sudah punya config/database.php
// (hasil `laravel new` / `composer create-project`), CUKUP GANTI
// bagian 'mysql' => [...] di dalam array 'connections' dengan versi
// di bawah ini — tidak perlu mengganti seluruh file.
// ==============================================================

return [

    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'ppk_reservasi_fasilitas'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,

            // WAJIB true: mode strict mengaktifkan validasi ketat MySQL
            // (termasuk penegakan ENUM & CHECK constraint yang dipakai
            // di skema project ini). Jangan diubah jadi false.
            'strict' => true,

            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // Koneksi lain (sqlite, pgsql, sqlsrv) dari default Laravel bisa
        // tetap dipertahankan di sini kalau memang ada di file aslimu —
        // dihilangkan dari contoh ini karena project ini hanya memakai MySQL.

    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

    ],

];

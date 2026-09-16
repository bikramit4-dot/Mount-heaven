<?php

/*
|--------------------------------------------------------------------------
| Mount Heaven English School — Configuration
|--------------------------------------------------------------------------
| You can override any value with environment variables.
| IMPORTANT: change the admin seed password BEFORE first install
| (it is used only once, when the database is created).
*/

return [
    'app' => [
        'name'     => 'Mount Heaven English School',
        'url'      => getenv('APP_URL') ?: 'http://localhost:8000',
        'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Kolkata',
        'debug'    => (bool) (getenv('APP_DEBUG') ?: false),
        'key'      => getenv('APP_KEY') ?: 'change-this-random-32-char-secret-key',
    ],

    'db' => [
        'host'    => getenv('DB_HOST') ?: '127.0.0.1',
        'port'    => getenv('DB_PORT') ?: '3306',
        'name'    => getenv('DB_NAME') ?: 'mount_heaven_school',
        'user'    => getenv('DB_USER') ?: 'root',
        'pass'    => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
        'charset' => 'utf8mb4',
    ],

    // Used ONLY on first install to create the default administrator.
    'admin' => [
        'name'     => 'Administrator',
        'username' => 'admin',
        'email'    => 'admin@mountheaven.edu',
        'password' => 'Admin@123',
    ],

    'uploads' => [
        'max_size' => 5 * 1024 * 1024, // 5 MB
        'allowed'  => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    ],

    'backup' => [
        'dir'        => BASE_PATH . '/storage/backups',
        'max_upload' => 32 * 1024 * 1024, // 32 MB
    ],
];

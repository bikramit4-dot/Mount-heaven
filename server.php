<?php

// Development router:  php -S localhost:8000 server.php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false; // let the built-in server serve static files
}

require __DIR__ . '/public/index.php';

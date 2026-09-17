<?php

// Development router:  php -S localhost:8000 server.php
//
// Static files are served ONLY from public/ (the web root in production).
// Everything else — app/, config/, database/, storage/ — is never exposed,
// mirroring the Apache .htaccess rules so dev behaves like live.

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$path = rawurldecode($path);

// Resolve the requested file against the PROJECT root, then only serve it if
// it actually lives inside public/. URLs carry a /public prefix here because
// the project root is the doc root during development.
$publicDir = realpath(__DIR__ . '/public');
$requested = realpath(__DIR__ . $path);

$file = ($publicDir && $requested && str_starts_with($requested, $publicDir . DIRECTORY_SEPARATOR))
    ? $requested
    : null;

if ($file !== null && is_file($file)) {
    return false; // let the built-in server serve the static file
}

require __DIR__ . '/public/index.php';

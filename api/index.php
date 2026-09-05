<?php

// Fix storage paths for Vercel serverless environment
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/views',
];

foreach ($dirs as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Ensure LARAVEL_STORAGE_PATH points to writable /tmp/storage
$_ENV['LARAVEL_STORAGE_PATH'] = '/tmp/storage';
$_SERVER['LARAVEL_STORAGE_PATH'] = '/tmp/storage';
putenv('LARAVEL_STORAGE_PATH=/tmp/storage');

$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';
putenv('VIEW_COMPILED_PATH=/tmp/views');

// Provide APP_KEY fallback if not configured in Vercel UI
if (empty($_ENV['APP_KEY']) && empty(getenv('APP_KEY'))) {
    $fallbackKey = 'base64:GEawW5rXtXYC2VhYr3hnHBMDMBxbKBOhbyBOycScCGA=';
    $_ENV['APP_KEY'] = $fallbackKey;
    $_SERVER['APP_KEY'] = $fallbackKey;
    putenv("APP_KEY={$fallbackKey}");
}

// Setup SQLite database
$sourceDb = __DIR__.'/../database/database.sqlite';
$targetDb = '/tmp/database.sqlite';

if (file_exists($sourceDb) && filesize($sourceDb) > 0) {
    if (! file_exists($targetDb) || filesize($targetDb) === 0) {
        copy($sourceDb, $targetDb);
    }
} else {
    if (! file_exists($targetDb)) {
        touch($targetDb);
    }
}

$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = $targetDb;
putenv('DB_CONNECTION=sqlite');
putenv("DB_DATABASE={$targetDb}");

// Forward to Laravel public/index.php
require __DIR__.'/../public/index.php';

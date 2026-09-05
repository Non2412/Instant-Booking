<?php

// Ensure writable directories in /tmp for Vercel serverless environment
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

// Copy sqlite database to /tmp if using sqlite
$sourceDb = __DIR__.'/../database/database.sqlite';
$targetDb = '/tmp/database.sqlite';
if (file_exists($sourceDb) && ! file_exists($targetDb)) {
    copy($sourceDb, $targetDb);
} elseif (! file_exists($targetDb)) {
    touch($targetDb);
}

putenv('VIEW_COMPILED_PATH=/tmp/views');
putenv('DB_DATABASE=/tmp/database.sqlite');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';
$_ENV['DB_DATABASE'] = '/tmp/database.sqlite';

// Forward to public/index.php
require __DIR__.'/../public/index.php';

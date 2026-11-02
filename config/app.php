<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'PDF Workspace'),
    'env' => env('APP_ENV', 'local'),
    'debug' => (bool)env('APP_DEBUG', true),
    'url' => env('APP_URL', 'http://localhost:8080'),
    'secret' => env('APP_SECRET', 'pdfworkspace_secret_key_change_in_production_32chars'),
    'max_file_size' => (int)env('MAX_FILE_SIZE', 52428800), // 50MB
    'allowed_extensions' => ['pdf'],
    
    'paths' => [
        'base' => dirname(__DIR__),
        'storage' => dirname(__DIR__) . '/storage',
        'sources' => dirname(__DIR__) . '/storage/sources',
        'outputs' => dirname(__DIR__) . '/storage/outputs',
        'temp' => dirname(__DIR__) . '/storage/temp',
        'logs' => dirname(__DIR__) . '/storage/logs',
        'views' => dirname(__DIR__) . '/resources/views',
    ],
];

<?php

declare(strict_types=1);

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Tự động kích hoạt SSL/TLS khi kết nối tới Cloud Database từ xa (TiDB Cloud, Aiven, v.v.)
$host = env('DB_HOST', '127.0.0.1');
if (env('DB_SSL', false) || (!in_array($host, ['127.0.0.1', 'localhost']) && !empty($host))) {
    if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
    }
    if (defined('PDO::MYSQL_ATTR_SSL_CA') && file_exists('/etc/ssl/certs/ca-certificates.crt')) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = '/etc/ssl/certs/ca-certificates.crt';
    }
}

return [
    'host' => $host,
    'port' => (int)env('DB_PORT', 3306),
    'database' => env('DB_DATABASE', 'pdf_workspace'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options' => $options,
];

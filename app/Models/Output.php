<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;

class Output
{
    public static function createOutput(
        int $exportJobId,
        int $userId,
        string $originalFilename,
        string $storedFilename,
        string $filePath,
        int $fileSize,
        int $totalPages
    ): string {
        $token = bin2hex(random_bytes(32));

        Database::query(
            "INSERT INTO outputs (export_job_id, user_id, original_filename, stored_filename, file_path, file_size, total_pages, download_token, created_at)
             VALUES (:export_job_id, :user_id, :original_filename, :stored_filename, :file_path, :file_size, :total_pages, :download_token, NOW())",
            [
                'export_job_id' => $exportJobId,
                'user_id' => $userId,
                'original_filename' => $originalFilename,
                'stored_filename' => $storedFilename,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'total_pages' => $totalPages,
                'download_token' => $token,
            ]
        );

        return $token;
    }

    public static function findByToken(string $token): ?array
    {
        return Database::fetchOne(
            "SELECT * FROM outputs WHERE download_token = :token LIMIT 1",
            ['token' => $token]
        );
    }

    public static function findByIdAndUser(int $id, int $userId): ?array
    {
        return Database::fetchOne(
            "SELECT * FROM outputs WHERE id = :id AND user_id = :user_id LIMIT 1",
            ['id' => $id, 'user_id' => $userId]
        );
    }
}

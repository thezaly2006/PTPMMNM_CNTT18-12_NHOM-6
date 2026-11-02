<?php

declare(strict_types=1);

namespace App\Helpers;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $config = require dirname(__DIR__, 2) . '/config/database.php';

            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            try {
                self::$pdo = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );

                // Đồng bộ múi giờ với thời gian thực Việt Nam (UTC+7)
                self::$pdo->exec("SET time_zone = '+07:00'");

                // Tự động bổ sung cột fit_portrait nếu chưa có
                try {
                    self::$pdo->exec("ALTER TABLE edit_plan_pages ADD COLUMN fit_portrait TINYINT(1) NOT NULL DEFAULT 0");
                } catch (\Throwable $e) {}
                try {
                    self::$pdo->exec("ALTER TABLE export_job_pages ADD COLUMN fit_portrait TINYINT(1) NOT NULL DEFAULT 0");
                } catch (\Throwable $e) {}

                // Tự động bổ sung cột role và is_active cho bảng users phục vụ quản trị đề tài
                try {
                    self::$pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'user'");
                } catch (\Throwable $e) {}
                try {
                    self::$pdo->exec("ALTER TABLE users ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1");
                } catch (\Throwable $e) {}

                // Tách biệt hoàn toàn: Tài khoản demo@pdfworkspace.test là User thường
                try {
                    self::$pdo->exec("UPDATE users SET role = 'user' WHERE email = 'demo@pdfworkspace.test'");
                } catch (\Throwable $e) {}

                // Tạo hoặc cập nhật tài khoản Quản trị viên (Admin) riêng biệt: admin@pdfworkspace.test / Admin@123
                try {
                    $checkAdmin = self::$pdo->query("SELECT id FROM users WHERE email = 'admin@pdfworkspace.test'")->fetch();
                    $adminHash = password_hash('Admin@123', PASSWORD_BCRYPT);
                    if (!$checkAdmin) {
                        $stmt = self::$pdo->prepare("INSERT INTO users (name, email, password_hash, role, is_active, created_at) VALUES (:name, :email, :hash, 'admin', 1, NOW())");
                        $stmt->execute([
                            'name' => 'Quản Trị Viên Hệ Thống',
                            'email' => 'admin@pdfworkspace.test',
                            'hash' => $adminHash,
                        ]);
                    } else {
                        self::$pdo->exec("UPDATE users SET role = 'admin', is_active = 1 WHERE email = 'admin@pdfworkspace.test'");
                    }
                } catch (\Throwable $e) {}
            } catch (PDOException $e) {
                // Ghi log loi ra storage/logs/database.log ma khong de lo thong tin bao mat ra ngoai
                $logDir = dirname(__DIR__, 2) . '/storage/logs';
                if (!is_dir($logDir)) {
                    mkdir($logDir, 0755, true);
                }
                $logMessage = sprintf("[%s] Database Connection Error: %s\n", date('Y-m-d H:i:s'), $e->getMessage());
                file_put_contents($logDir . '/database.log', $logMessage, FILE_APPEND);

                throw new RuntimeException("Không thể kết nối đến cơ sở dữ liệu. Vui lòng kiểm tra file cấu hình .env!");
            }
        }

        return self::$pdo;
    }

    /**
     * Chay query tra ve statement
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Lay tat ca ban ghi
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    /**
     * Lay 1 ban ghi
     */
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }

    /**
     * Lay ID cua ban ghi vua insert
     */
    public static function lastInsertId(): int
    {
        return (int)self::getConnection()->lastInsertId();
    }
}

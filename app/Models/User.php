<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;

class User
{
    public static function findByEmail(string $email): ?array
    {
        return Database::fetchOne(
            "SELECT * FROM users WHERE email = :email LIMIT 1",
            ['email' => strtolower(trim($email))]
        );
    }

    public static function findById(int $id): ?array
    {
        return Database::fetchOne(
            "SELECT id, name, email, role, is_active, created_at FROM users WHERE id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public static function create(string $name, string $email, string $password): int
    {
        return self::createWithRole($name, $email, $password, 'user');
    }

    public static function createWithRole(string $name, string $email, string $password, string $role = 'user'): int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        Database::query(
            "INSERT INTO users (name, email, password_hash, role, is_active, created_at) 
             VALUES (:name, :email, :hash, :role, 1, NOW())",
            [
                'name' => trim($name),
                'email' => strtolower(trim($email)),
                'hash' => $hash,
                'role' => in_array($role, ['admin', 'user'], true) ? $role : 'user',
            ]
        );
        return Database::lastInsertId();
    }

    public static function countAll(?string $search = null, ?string $role = null): int
    {
        $sql = "SELECT COUNT(*) AS total FROM users WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (name LIKE :s_name OR email LIKE :s_email)";
            $params['s_name'] = '%' . $search . '%';
            $params['s_email'] = '%' . $search . '%';
        }

        if (!empty($role) && in_array($role, ['admin', 'user'], true)) {
            $sql .= " AND role = :role";
            $params['role'] = $role;
        }

        $res = Database::fetchOne($sql, $params);
        return (int)($res['total'] ?? 0);
    }

    public static function getPaginated(
        int $page = 1,
        int $perPage = 10,
        ?string $search = null,
        ?string $role = null,
        string $sort = 'created_at_desc'
    ): array {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT u.id, u.name, u.email, u.role, u.is_active, u.created_at,
                       (SELECT COUNT(*) FROM documents d WHERE d.user_id = u.id) AS total_docs,
                       (SELECT COUNT(*) FROM export_jobs j WHERE j.user_id = u.id) AS total_exports
                FROM users u
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (u.name LIKE :s_name OR u.email LIKE :s_email)";
            $params['s_name'] = '%' . $search . '%';
            $params['s_email'] = '%' . $search . '%';
        }

        if (!empty($role) && in_array($role, ['admin', 'user'], true)) {
            $sql .= " AND u.role = :role";
            $params['role'] = $role;
        }

        switch ($sort) {
            case 'created_at_asc':
                $sql .= " ORDER BY u.created_at ASC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY u.name ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY u.name DESC";
                break;
            case 'docs_desc':
                $sql .= " ORDER BY total_docs DESC";
                break;
            case 'exports_desc':
                $sql .= " ORDER BY total_exports DESC";
                break;
            case 'created_at_desc':
            default:
                $sql .= " ORDER BY u.created_at DESC";
                break;
        }

        $sql .= " LIMIT {$perPage} OFFSET {$offset}";

        return Database::fetchAll($sql, $params);
    }

    public static function updateRole(int $userId, string $role): bool
    {
        if (!in_array($role, ['admin', 'user'], true)) {
            return false;
        }
        Database::query(
            "UPDATE users SET role = :role WHERE id = :id",
            ['role' => $role, 'id' => $userId]
        );
        return true;
    }

    public static function toggleStatus(int $userId): bool
    {
        Database::query(
            "UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = :id",
            ['id' => $userId]
        );
        return true;
    }

    public static function updatePassword(int $userId, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        Database::query(
            "UPDATE users SET password_hash = :hash WHERE id = :id",
            ['hash' => $hash, 'id' => $userId]
        );
        return true;
    }

    public static function delete(int $userId): bool
    {
        // Khi xóa user, foreign key ON DELETE CASCADE sẽ tự động dọn dẹp documents, edit_plans, export_jobs
        Database::query("DELETE FROM users WHERE id = :id", ['id' => $userId]);
        return true;
    }

    public static function getAll(): array
    {
        return Database::fetchAll("SELECT id, name, email, role FROM users ORDER BY name ASC");
    }
}

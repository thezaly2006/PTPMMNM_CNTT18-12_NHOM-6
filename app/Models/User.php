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
        $user = Database::fetchOne(
            "SELECT id, name, email, role, tokens, plan_tier, plan_expires_at, is_active, created_at FROM users WHERE id = :id LIMIT 1",
            ['id' => $id]
        );

        // Tự động kiểm tra thời hạn gói theo mốc UTC+7
        if ($user && ($user['plan_tier'] ?? 'free') !== 'free' && ($user['role'] ?? '') !== 'admin') {
            if (!empty($user['plan_expires_at'])) {
                date_default_timezone_set('Asia/Ho_Chi_Minh');
                if (strtotime($user['plan_expires_at']) <= time()) {
                    // Đã hết thời hạn gói -> Tự động chuyển về gói Miễn phí (Free)
                    Database::query("UPDATE users SET plan_tier = 'free' WHERE id = :id", ['id' => $id]);
                    $user['plan_tier'] = 'free';
                }
            }
        }

        return $user;
    }

    /**
     * Kiểm tra người dùng có quyền VÔ HẠN TOKEN hay không:
     * - Admin luôn có quyền vô hạn.
     * - Khi đăng ký bất kỳ gói trả phí nào (Plus, Pro, Premium) và còn trong thời hạn theo mốc 00:00 (UTC+7),
     *   người dùng sở hữu VÔ HẠN TOKEN không bị giới hạn số lần xuất file.
     */
    public static function hasUnlimitedTokens(?array $user): bool
    {
        if (!$user) {
            return false;
        }

        if (($user['role'] ?? '') === 'admin') {
            return true;
        }

        $tier = strtolower(trim($user['plan_tier'] ?? 'free'));
        if ($tier === 'free') {
            return false;
        }

        // Người dùng đã đăng ký gói trả phí
        $expiresAt = $user['plan_expires_at'] ?? null;
        if (empty($expiresAt)) {
            return true;
        }

        date_default_timezone_set('Asia/Ho_Chi_Minh');
        return strtotime($expiresAt) > time();
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

        $sql = "SELECT u.id, u.name, u.email, u.role, u.tokens, u.plan_tier, u.plan_expires_at, u.is_active, u.created_at,
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
        return Database::fetchAll("SELECT id, name, email, role, tokens, plan_tier FROM users ORDER BY name ASC");
    }

    /**
     * Khấu trừ token khi thực hiện tác vụ (Export, Merge, Split)
     */
    public static function deductTokens(int $userId, int $amount, string $action, ?string $description = null): bool
    {
        $user = self::findById($userId);
        if (!$user) {
            return false;
        }

        // Quản trị viên và người dùng đang trong thời hạn gói (Plus, Pro, Premium) được VÔ HẠN TOKEN
        if (self::hasUnlimitedTokens($user)) {
            return true;
        }

        $current = (int)($user['tokens'] ?? 0);
        if ($current < $amount) {
            return false;
        }

        $newBalance = $current - $amount;
        Database::query("UPDATE users SET tokens = :tokens WHERE id = :id", [
            'tokens' => $newBalance,
            'id' => $userId,
        ]);

        Database::query("
            INSERT INTO token_transactions (user_id, amount, action, description, balance_after, created_at)
            VALUES (:uid, :amount, :act, :desc, :bal, NOW())
        ", [
            'uid' => $userId,
            'amount' => -$amount,
            'act' => $action,
            'desc' => $description,
            'bal' => $newBalance,
        ]);

        return true;
    }

    /**
     * Cộng thêm token vào tài khoản
     */
    public static function addTokens(int $userId, int $amount, string $action, ?string $description = null): int
    {
        $user = self::findById($userId);
        $current = (int)($user['tokens'] ?? 0);
        $newBalance = $current + $amount;

        Database::query("UPDATE users SET tokens = :tokens WHERE id = :id", [
            'tokens' => $newBalance,
            'id' => $userId,
        ]);

        Database::query("
            INSERT INTO token_transactions (user_id, amount, action, description, balance_after, created_at)
            VALUES (:uid, :amount, :act, :desc, :bal, NOW())
        ", [
            'uid' => $userId,
            'amount' => $amount,
            'act' => $action,
            'desc' => $description,
            'bal' => $newBalance,
        ]);

        return $newBalance;
    }

    /**
     * Nâng cấp gói cước và kích hoạt VÔ HẠN TOKEN trong thời hạn gói tính đến mốc 00:00 (UTC+7)
     */
    public static function upgradePlan(
        int $userId,
        string $planTier,
        string $billingCycle,
        int $price,
        int $tokensGranted,
        string $paymentMethod
    ): array {
        $user = self::findById($userId);
        if (!$user) {
            throw new \RuntimeException("Người dùng không tồn tại.");
        }

        date_default_timezone_set('Asia/Ho_Chi_Minh');

        // Tính mốc 00:00 (UTC+7) theo yêu cầu:
        // Đăng ký 1 tuần: Có vô hạn token trong 1 tuần tròn tính đến mốc 00:00 (UTC+7) của ngày thứ 8.
        // Nếu gói đang còn hạn, cộng dồn nối tiếp từ mốc hết hạn trước đó.
        $hasActivePlan = !empty($user['plan_expires_at']) && strtotime($user['plan_expires_at']) > time();
        $baseDate = $hasActivePlan ? date('Y-m-d', strtotime($user['plan_expires_at'])) : date('Y-m-d');

        $daysToAdd = match ($billingCycle) {
            'weekly' => $hasActivePlan ? 7 : 8,
            'yearly' => $hasActivePlan ? 365 : 366,
            default => $hasActivePlan ? 30 : 31, // monthly
        };

        $expires = date('Y-m-d 00:00:00', strtotime($baseDate . " +{$daysToAdd} days"));

        // Cập nhật người dùng: Nâng cấp gói và kích hoạt thời hạn
        Database::query("
            UPDATE users 
            SET plan_tier = :plan, plan_expires_at = :exp 
            WHERE id = :id
        ", [
            'plan' => $planTier,
            'exp' => $expires,
            'id' => $userId,
        ]);

        // Tạo mã giao dịch
        $txnCode = 'INV-' . strtoupper($planTier) . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

        // Lưu bản ghi đăng ký gói
        Database::query("
            INSERT INTO subscriptions (user_id, plan_tier, billing_cycle, price, tokens_granted, payment_method, transaction_code, status, expires_at, created_at)
            VALUES (:uid, :tier, :cycle, :price, :tok, :pm, :code, 'completed', :exp, NOW())
        ", [
            'uid' => $userId,
            'tier' => $planTier,
            'cycle' => $billingCycle,
            'price' => $price,
            'tok' => 0, // Vô hạn tokens
            'pm' => $paymentMethod,
            'code' => $txnCode,
            'exp' => $expires,
        ]);

        $cycleName = match($billingCycle) {
            'weekly' => '1 Tuần',
            'yearly' => '1 Năm',
            default => '1 Tháng'
        };

        // Ghi nhận lịch sử giao dịch
        Database::query("
            INSERT INTO token_transactions (user_id, amount, action, description, balance_after, created_at)
            VALUES (:uid, 0, 'purchase', :desc, :bal, NOW())
        ", [
            'uid' => $userId,
            'desc' => "Kích hoạt Vô Hạn Token gói " . strtoupper($planTier) . " ({$cycleName}) đến " . date('d/m/Y 00:00', strtotime($expires)) . " (UTC+7) - Mã: {$txnCode}",
            'bal' => (int)($user['tokens'] ?? 50),
        ]);

        return [
            'success' => true,
            'plan_tier' => $planTier,
            'billing_cycle' => $billingCycle,
            'new_tokens' => 'unlimited',
            'is_unlimited' => true,
            'expires_at' => $expires,
            'expires_formatted' => date('d/m/Y 00:00', strtotime($expires)) . ' (UTC+7)',
            'transaction_code' => $txnCode,
        ];
    }

    public static function getTokenTransactions(int $userId, int $limit = 20): array
    {
        return Database::fetchAll("
            SELECT * FROM token_transactions 
            WHERE user_id = :uid 
            ORDER BY created_at DESC 
            LIMIT {$limit}
        ", ['uid' => $userId]);
    }
}

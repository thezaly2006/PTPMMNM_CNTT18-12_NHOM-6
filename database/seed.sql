-- ========================================================
-- PDF Workspace - Seed Data (Du lieu mau khoi tao)
-- 1. Tai khoan Sinh vien (User thuong):
--    Email: demo@pdfworkspace.test
--    Mat khau: Password@123
--    Role: user
--
-- 2. Tai khoan Quan tri vien (Admin rieng biet):
--    Email: admin@pdfworkspace.test
--    Mat khau: Admin@123
--    Role: admin
-- ========================================================

USE `pdf_workspace`;

-- 1. Chen tai khoan Demo User thuong
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `is_active`, `created_at`) 
VALUES (
    1, 
    'Sinh Viên Thử Nghiệm', 
    'demo@pdfworkspace.test', 
    '$2y$10$tM78w8qXh/b11F8Nff2z1eIwhiFv9i2RvhgE4xR0Yk1VjVfPspD92', 
    'user',
    1,
    NOW()
) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `role` = 'user';

-- 2. Chen tai khoan Admin rieng biet
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `is_active`, `created_at`) 
VALUES (
    'Quản Trị Viên Hệ Thống', 
    'admin@pdfworkspace.test', 
    '$2y$10$xsq79t1.K2L2G7iGjO55Q.eB0b0kLrkc1aU35wG.7Z1e.m83WqVea', 
    'admin',
    1,
    NOW()
) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `role` = 'admin';

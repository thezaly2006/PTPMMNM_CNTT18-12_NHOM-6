-- ========================================================
-- PDF Workspace - Database Schema
-- He quan tri: MySQL 8.0+ / MariaDB 10.4+
-- Charset: utf8mb4, Collation: utf8mb4_unicode_ci
-- ========================================================

CREATE DATABASE IF NOT EXISTS `pdf_workspace` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pdf_workspace`;

-- 1. Bang users: Thong tin tai khoan nguoi dung
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bang documents: Tai lieu PDF nguon thuoc nguoi dung
CREATE TABLE IF NOT EXISTS `documents` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `original_filename` VARCHAR(255) NOT NULL,
    `stored_filename` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_size` BIGINT UNSIGNED NOT NULL,
    `total_pages` INT UNSIGNED NOT NULL DEFAULT 0,
    `mime_type` VARCHAR(100) DEFAULT 'application/pdf',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_documents_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_documents_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bang source_pages: Thong tin tung trang nguon
CREATE TABLE IF NOT EXISTS `source_pages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `document_id` INT UNSIGNED NOT NULL,
    `page_number` INT UNSIGNED NOT NULL,
    `width` DECIMAL(8,2) DEFAULT NULL,
    `height` DECIMAL(8,2) DEFAULT NULL,
    `orientation` ENUM('portrait', 'landscape') DEFAULT 'portrait',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_source_pages_doc` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
    INDEX `idx_source_pages_doc` (`document_id`, `page_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bang edit_plans: Cac phien chinh sua PDF cua nguoi dung
CREATE TABLE IF NOT EXISTS `edit_plans` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL DEFAULT 'Phiên chỉnh sửa mới',
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_edit_plans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_edit_plans_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bang edit_plan_pages: Danh sach trang va thu tu trong tung ke hoach
CREATE TABLE IF NOT EXISTS `edit_plan_pages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `plan_id` INT UNSIGNED NOT NULL,
    `document_id` INT UNSIGNED NOT NULL,
    `source_page_number` INT UNSIGNED NOT NULL,
    `output_order` INT UNSIGNED NOT NULL,
    `rotation_degrees` INT NOT NULL DEFAULT 0,
    `fit_portrait` TINYINT(1) NOT NULL DEFAULT 0,
    `is_included` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_plan_pages_plan` FOREIGN KEY (`plan_id`) REFERENCES `edit_plans` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_plan_pages_doc` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE RESTRICT,
    INDEX `idx_plan_pages_order` (`plan_id`, `output_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bang export_jobs: Cac lan xuat va trang thai xu ly
CREATE TABLE IF NOT EXISTS `export_jobs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `plan_id` INT UNSIGNED NULL,
    `job_type` ENUM('merge', 'split', 'editor') NOT NULL,
    `status` ENUM('pending', 'processing', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    `error_message` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `completed_at` TIMESTAMP NULL,
    CONSTRAINT `fk_export_jobs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_export_jobs_plan` FOREIGN KEY (`plan_id`) REFERENCES `edit_plans` (`id`) ON DELETE SET NULL,
    INDEX `idx_export_jobs_user` (`user_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Bang export_job_pages: Ban chup bat bien (Immutable Snapshot) danh sach trang cua lan xuat
CREATE TABLE IF NOT EXISTS `export_job_pages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `export_job_id` INT UNSIGNED NOT NULL,
    `document_id` INT UNSIGNED NOT NULL,
    `source_page_number` INT UNSIGNED NOT NULL,
    `output_order` INT UNSIGNED NOT NULL,
    `rotation_degrees` INT NOT NULL DEFAULT 0,
    `fit_portrait` TINYINT(1) NOT NULL DEFAULT 0,
    `source_filename_snapshot` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_job_pages_job` FOREIGN KEY (`export_job_id`) REFERENCES `export_jobs` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_job_pages_doc` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE RESTRICT,
    INDEX `idx_job_pages_order` (`export_job_id`, `output_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Bang outputs: Thong tin cac tep ket qua sau khi xuat thanh cong
CREATE TABLE IF NOT EXISTS `outputs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `export_job_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `original_filename` VARCHAR(255) NOT NULL,
    `stored_filename` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_size` BIGINT UNSIGNED NOT NULL,
    `total_pages` INT UNSIGNED NOT NULL DEFAULT 0,
    `download_token` VARCHAR(64) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_outputs_job` FOREIGN KEY (`export_job_id`) REFERENCES `export_jobs` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_outputs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_outputs_user` (`user_id`),
    INDEX `idx_outputs_token` (`download_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

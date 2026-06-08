-- ============================================================
-- Student Registration Management System Pro
-- Database Schema & Sample Data
-- ============================================================
-- Compatible with MySQL 5.7+ and MariaDB 10.4+
-- Run this file via phpMyAdmin > Import, or:
--   mysql -u root -p < database/schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS `student_registration_pro`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `student_registration_pro`;

-- Drop existing table for clean re-import
DROP TABLE IF EXISTS `students`;

CREATE TABLE `students` (
    `id`            INT(11)       UNSIGNED NOT NULL AUTO_INCREMENT,
    `full_name`     VARCHAR(120)  NOT NULL,
    `email`         VARCHAR(180)  NOT NULL,
    `phone`         VARCHAR(15)   NOT NULL,
    `gender`        ENUM('Male','Female','Other') NOT NULL,
    `date_of_birth` DATE          NOT NULL,
    `country`       VARCHAR(80)   NOT NULL,
    `skills`        TEXT          DEFAULT NULL,
    `address`       TEXT          DEFAULT NULL,
    `profile_image` VARCHAR(255)  DEFAULT NULL,
    `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE  KEY `uq_email`       (`email`),
    INDEX       `idx_full_name`  (`full_name`),
    INDEX       `idx_country`    (`country`),
    INDEX       `idx_gender`     (`gender`),
    INDEX       `idx_created_at` (`created_at`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Student records for the Registration Management System';

-- ── Sample Data ───────────────────────────────────────────────
INSERT INTO `students`
    (`full_name`, `email`, `phone`, `gender`, `date_of_birth`, `country`, `skills`, `address`, `profile_image`)
VALUES
    ('Aanya Sharma',   'aanya.sharma@example.com',  '9876543210', 'Female', '2001-03-14', 'India',
     'PHP, MySQL, HTML, CSS',           '12 MG Road, Mumbai, Maharashtra',        NULL),

    ('Rohan Mehta',    'rohan.mehta@example.com',   '8765432109', 'Male',   '2000-07-22', 'India',
     'JavaScript, React, Node.js, Git', '45 Brigade Road, Bengaluru, Karnataka',  NULL),

    ('Priya Nair',     'priya.nair@example.com',    '7654321098', 'Female', '2002-11-05', 'India',
     'Python, Django, REST APIs, SQL',  '7 Anna Nagar, Chennai, Tamil Nadu',      NULL),

    ('Carlos Rivera',  'carlos.rivera@example.com', '6543210987', 'Male',   '1999-06-30', 'Mexico',
     'Vue.js, Laravel, Docker, Linux',  '88 Paseo de la Reforma, Ciudad de México', NULL),

    ('Mei Lin',        'mei.lin@example.com',       '5432109876', 'Female', '2003-01-18', 'China',
     'Java, Spring Boot, MySQL, Redis', '22 Nanjing Road, Shanghai',              NULL),

    ('Arjun Patel',    'arjun.patel@example.com',   '9988776655', 'Male',   '1998-09-10', 'India',
     'AWS, DevOps, Kubernetes, Terraform', '3 Satellite Road, Ahmedabad, Gujarat', NULL),

    ('Sophie Williams','sophie.w@example.com',      '7788990011', 'Female', '2001-12-25', 'United Kingdom',
     'UX Design, Figma, HTML, SCSS',    '10 Downing Street, London',              NULL),

    ('Yuki Tanaka',    'yuki.tanaka@example.com',   '8899001122', 'Male',   '2000-04-02', 'Japan',
     'Go, gRPC, PostgreSQL, Docker',    '1-1 Shinjuku, Tokyo',                    NULL);

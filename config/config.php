<?php

/**
 * Application Configuration
 * Student Registration Management System Pro
 */

// ── Environment ──────────────────────────────────────────────
define('APP_NAME',    'Student Registration Pro');
define('APP_VERSION', '1.0.0');

// *** CHANGE THIS to match your XAMPP subfolder name ***
define('BASE_URL', 'http://localhost/STUDENT-MANAGEMENT-REGISTRATION-SYSTEM');

// ── Database Credentials ─────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'student_registration_pro');
define('DB_USER',    'root');
define('DB_PASS',    '');           // Change if your MySQL has a password
define('DB_CHARSET', 'utf8mb4');

// ── Upload Settings ───────────────────────────────────────────
define('UPLOAD_DIR',    __DIR__ . '/../uploads/');
define('UPLOAD_URL',    BASE_URL . '/uploads/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024);  // 2 MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_EXT',   ['jpg', 'jpeg', 'png', 'webp']);

// ── Pagination ────────────────────────────────────────────────
define('RECORDS_PER_PAGE', 10);

// ── Error Reporting (set to 0 in production) ──────────────────
ini_set('display_errors', 1);
error_reporting(E_ALL);

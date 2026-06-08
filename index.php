<?php

/**
 * Student Registration Management System
 * =========================================
 * Entry Point — index.php
 *
 * Boots the application:
 *   1. Load configuration constants
 *   2. Load shared view helper functions
 *   3. Register PSR-style class autoloader
 *   4. Start session
 *   5. Set security headers
 *   6. Dispatch to StudentController
 *
 * Requirements: PHP 8.0+, MySQL 5.7+ / MariaDB 10.4+
 */

declare(strict_types=1);

// ── 1. Configuration ──────────────────────────────────────────
require_once __DIR__ . '/config/config.php';

// ── 2. Shared View Helpers ────────────────────────────────────
require_once __DIR__ . '/config/helpers.php';

// ── 3. Class Autoloader ───────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $locations = [
        __DIR__ . '/classes/'     . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php',
    ];
    foreach ($locations as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

// ── 4. Session ────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── 5. Security Headers ───────────────────────────────────────
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// ── 6. Dispatch ───────────────────────────────────────────────
$controller = new StudentController();
$controller->handleRequest();

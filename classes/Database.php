<?php

/**
 * Database — PDO Singleton Wrapper
 *
 * Provides a single, shared PDO connection for the entire request lifecycle.
 * Prevents multiple connections and centralises error handling.
 */
class Database
{
    /** @var Database|null Singleton instance */
    private static ?Database $instance = null;

    /** @var PDO Active PDO connection */
    private PDO $connection;

    // ── Constructor (private — use getInstance) ──────────────
    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];

        try {
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('[DB] Connection failed: ' . $e->getMessage());
            http_response_code(500);
            die('<h2 style="font-family:sans-serif;color:#dc2626;padding:2rem;">
                    ⚠️ Database Connection Failed<br>
                    <small style="font-size:.75rem;color:#6b7280;">Check your DB credentials in config/config.php</small>
                </h2>');
        }
    }

    /** Prevent cloning of the singleton */
    private function __clone() {}

    // ── Public API ───────────────────────────────────────────

    /**
     * Retrieve (or lazily create) the singleton instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Expose the raw PDO connection for query execution.
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}

<?php

/**
 * Database Singleton - Manages PDO database connection.
 */

declare(strict_types=1);

final class Database
{
    // Singleton instance
    private static ?\PDO $pdo = null;

    // Initialize database connection
    public static function init(array $dbConfig): void
    {
        // Skip if already initialized
        if (self::$pdo !== null) {
            return;
        }
        // Build DSN and establish connection
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $dbConfig['host'],
            $dbConfig['port'],
            $dbConfig['name'],
            $dbConfig['charset']
        );
        // Create PDO instance with error handling
        self::$pdo = new \PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    }

    // Get PDO instance
    public static function pdo(): \PDO
    {
        if (self::$pdo === null) {
            throw new \RuntimeException('Database not initialized.');
        }
        return self::$pdo;
    }
}

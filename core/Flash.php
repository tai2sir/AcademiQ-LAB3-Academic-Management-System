<?php

/**
 * Flash Messages - Store one-time messages in session.
 */

declare(strict_types=1);

final class Flash
{
    // Store message by type in session
    public static function set(string $type, string $message): void
    {
        $_SESSION['_flash'][$type][] = $message;
    }

    // Retrieve and clear all flash messages
    /**
     * @return array<string, list<string>>
     */
    public static function get(): array
    {
        $f = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return is_array($f) ? $f : [];
    }
}

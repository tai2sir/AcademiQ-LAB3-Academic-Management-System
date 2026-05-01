<?php

/**
 * Authentication Manager - Handles user authentication, authorization, and session management.
 */

declare(strict_types=1);

final class Auth
{
    // Configuration storage
    private static array $config = [];

    // Initialize authentication system with config and database
    public static function bootstrap(array $config): void
    {
        self::$config = $config;
        // Initialize database connection
        Database::init($config['db']);

        // Start session if not already active
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name($config['app']['session_name']);
            session_start();
        }

        // Check for session timeout
        self::checkSessionTimeout();
    }

    // Check and enforce session timeout
    private static function checkSessionTimeout(): void
    {
        // Skip check if no active session
        if (!isset($_SESSION['user_id'])) {
            return;
        }
        // Get configured session lifetime
        $lifetime = (int) (self::$config['app']['session_lifetime'] ?? 1800);
        $now = time();
        // Logout if session expired
        if (isset($_SESSION['last_activity']) && ($now - (int) $_SESSION['last_activity']) > $lifetime) {
            self::logout();
            return;
        }
        // Update last activity timestamp
        $_SESSION['last_activity'] = $now;
    }

    // Update session activity timestamp
    public static function touchSession(): void
    {
        self::checkSessionTimeout();
        // Refresh activity time if logged in
        if (isset($_SESSION['user_id'])) {
            $_SESSION['last_activity'] = time();
        }
    }

    // Store user data in session after successful login
    public static function login(int $userId, string $name, string $email, string $role): void
    {
        // Regenerate session ID for security
        session_regenerate_id(true);
        // Store user information in session
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;
        $_SESSION['last_activity'] = time();
    }

    // Clear session data and destroy session
    public static function logout(): void
    {
        // Clear session data
        $_SESSION = [];
        // Delete session cookie
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], (bool) $p['secure'], (bool) $p['httponly']);
        }
        // Destroy session
        session_destroy();
    }

    // Check if user is logged in
    public static function check(): bool
    {
        return isset($_SESSION['user_id'], $_SESSION['user_role']);
    }

    public static function userId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    public static function name(): ?string
    {
        return $_SESSION['user_name'] ?? null;
    }

    public static function email(): ?string
    {
        return $_SESSION['user_email'] ?? null;
    }

    /**
     * Require logged-in user; redirect to login if not.
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            redirect(url('login'));
        }
        self::touchSession();
    }

    /**
     * Require logged-in user for JSON API (401, no redirect).
     */
    public static function requireLoginJson(): void
    {
        if (!self::check()) {
            json_response(['error' => 'Unauthorized'], 401);
        }
        self::touchSession();
    }

    /**
     * Require one of the given roles. Redirects or sends JSON error for API.
     */
    public static function requireRole(array $allowed, bool $json = false): void
    {
        if ($json) {
            self::requireLoginJson();
        } else {
            self::requireLogin();
        }
        $role = self::role();
        if ($role === null || !in_array($role, $allowed, true)) {
            if ($json) {
                json_response(['error' => 'Forbidden'], 403);
            }
            Flash::set('danger', 'You do not have access to this page.');
            redirect(url(self::defaultPageForRole($role) ?? 'login'));
        }
    }

    public static function defaultPageForRole(?string $role): ?string
    {
        return match ($role) {
            'admin' => 'admin_dashboard',
            'professor' => 'professor_grades',
            'student' => 'student_dashboard',
            default => 'login',
        };
    }
}

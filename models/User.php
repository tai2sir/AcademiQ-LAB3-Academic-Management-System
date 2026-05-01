<?php

/**
 * User Model - Manages user data access and operations.
 */

declare(strict_types=1);

final class User
{
    // Find user by email
    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Find user by ID
    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Get all users by role with optional search filter and pagination
    /**
     * @return list<array<string,mixed>>
     */
    public static function allByRole(string $role, ?string $search, int $limit, int $offset): array
    {
        $sql = 'SELECT id, name, email, role, created_at FROM users WHERE role = ?';
        $params = [$role];
        if ($search !== null && $search !== '') {
            $sql .= ' AND (name LIKE ? OR email LIKE ?)';
            $q = '%' . $search . '%';
            $params[] = $q;
            $params[] = $q;
        }
        $sql .= ' ORDER BY name ASC LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;
        $stmt = Database::pdo()->prepare($sql);
        $i = 1;
        foreach ($params as $p) {
            $type = is_int($p) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
            $stmt->bindValue($i++, $p, $type);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Count users by role with optional search filter
    public static function countByRole(string $role, ?string $search): int
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE role = ?';
        $params = [$role];
        if ($search !== null && $search !== '') {
            $sql .= ' AND (name LIKE ? OR email LIKE ?)';
            $q = '%' . $search . '%';
            $params[] = $q;
            $params[] = $q;
        }
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    // Create new user account
    public static function create(string $name, string $email, string $passwordHash, string $role): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$name, $email, $passwordHash, $role]);
        return (int) Database::pdo()->lastInsertId();
    }

    // Update user information (optionally with new password hash)
    public static function update(int $id, string $name, string $email, ?string $passwordHash): void
    {
        if ($passwordHash !== null) {
            $stmt = Database::pdo()->prepare('UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?');
            $stmt->execute([$name, $email, $passwordHash, $id]);
        } else {
            $stmt = Database::pdo()->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
            $stmt->execute([$name, $email, $id]);
        }
    }

    // Delete user account
    public static function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }

    // Check if email exists for other users (for uniqueness validation)
    public static function emailExistsForOther(string $email, int $excludeId): bool
    {
        $stmt = Database::pdo()->prepare('SELECT 1 FROM users WHERE email = ? AND id != ? LIMIT 1');
        $stmt->execute([$email, $excludeId]);
        return (bool) $stmt->fetchColumn();
    }

    // Count total admin users
    public static function countAdmins(): int
    {
        return (int) Database::pdo()->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    }
}

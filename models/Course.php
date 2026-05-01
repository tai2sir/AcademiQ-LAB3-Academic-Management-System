<?php

/**
 * Course Model - Manages course data and operations.
 */

declare(strict_types=1);

final class Course
{
    // Get all courses for a semester
    /**
     * @return list<array<string,mixed>>
     */
    public static function bySemester(int $semesterId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM courses WHERE semester_id = ? ORDER BY name ASC'
        );
        $stmt->execute([$semesterId]);
        return $stmt->fetchAll();
    }

    // Find course by ID
    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM courses WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Create new course
    public static function create(int $semesterId, string $name, float $credits): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO courses (semester_id, name, credits) VALUES (?, ?, ?)'
        );
        $stmt->execute([$semesterId, $name, $credits]);
        return (int) Database::pdo()->lastInsertId();
    }

    // Update course information
    public static function update(int $id, int $semesterId, string $name, float $credits): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE courses SET semester_id = ?, name = ?, credits = ? WHERE id = ?'
        );
        $stmt->execute([$semesterId, $name, $credits, $id]);
    }

    // Delete course
    public static function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('DELETE FROM courses WHERE id = ?');
        $stmt->execute([$id]);
    }

    // Check if course has professor assignments
    public static function hasAssignments(int $courseId): bool
    {
        $stmt = Database::pdo()->prepare('SELECT 1 FROM assignments WHERE course_id = ? LIMIT 1');
        $stmt->execute([$courseId]);
        return (bool) $stmt->fetchColumn();
    }

    // Check if course has any grades assigned
    public static function hasGrades(int $courseId): bool
    {
        $stmt = Database::pdo()->prepare('SELECT 1 FROM grades WHERE course_id = ? LIMIT 1');
        $stmt->execute([$courseId]);
        return (bool) $stmt->fetchColumn();
    }

    // Count total courses
    public static function count(): int
    {
        return (int) Database::pdo()->query('SELECT COUNT(*) FROM courses')->fetchColumn();
    }
}

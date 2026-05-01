<?php

/**
 * Assignment Model - Manages professor-to-course assignments for semesters.
 */

declare(strict_types=1);

final class Assignment
{
    // Get all courses assigned to professor in a semester
    /**
     * @return list<array<string,mixed>> rows with course_id, course_name, semester_id, professor_id
     */
    public static function forProfessorSemester(int $professorId, int $semesterId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT a.course_id, c.name AS course_name, c.credits, a.semester_id
             FROM assignments a
             INNER JOIN courses c ON c.id = a.course_id AND c.semester_id = a.semester_id
             WHERE a.professor_id = ? AND a.semester_id = ?
             ORDER BY c.name ASC'
        );
        $stmt->execute([$professorId, $semesterId]);
        return $stmt->fetchAll();
    }

    // Check if professor is assigned to course in semester
    public static function professorAssigned(int $professorId, int $courseId, int $semesterId): bool
    {
        $stmt = Database::pdo()->prepare(
            'SELECT 1 FROM assignments WHERE professor_id = ? AND course_id = ? AND semester_id = ?'
        );
        $stmt->execute([$professorId, $courseId, $semesterId]);
        return (bool) $stmt->fetchColumn();
    }

    // Sync professor assignments for a semester (replace all with provided list)
    /**
     * @param list<array{course_id:int, professor_id:int}> $rows
     */
    public static function syncSemester(int $semesterId, array $rows): void
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $del = $pdo->prepare('DELETE FROM assignments WHERE semester_id = ?');
            $del->execute([$semesterId]);
            $ins = $pdo->prepare(
                'INSERT INTO assignments (professor_id, course_id, semester_id) VALUES (?, ?, ?)'
            );
            foreach ($rows as $r) {
                $ins->execute([(int) $r['professor_id'], (int) $r['course_id'], $semesterId]);
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // Get all assignments for semester with professor and course details
    /**
     * @return list<array<string,mixed>>
     */
    public static function forSemesterWithDetails(int $semesterId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT a.professor_id, u.name AS professor_name, a.course_id, c.name AS course_name
             FROM assignments a
             INNER JOIN users u ON u.id = a.professor_id
             INNER JOIN courses c ON c.id = a.course_id
             WHERE a.semester_id = ?
             ORDER BY c.name ASC'
        );
        $stmt->execute([$semesterId]);
        return $stmt->fetchAll();
    }

    // Delete assignments for a course in a semester
    public static function deleteForCourseSemester(int $courseId, int $semesterId): void
    {
        $stmt = Database::pdo()->prepare(
            'DELETE FROM assignments WHERE course_id = ? AND semester_id = ?'
        );
        $stmt->execute([$courseId, $semesterId]);
    }
}

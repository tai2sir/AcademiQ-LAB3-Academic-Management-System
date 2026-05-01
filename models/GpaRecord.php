<?php

declare(strict_types=1);

final class GpaRecord
{
    public static function forStudentSemester(int $studentId, int $semesterId): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT gpa FROM gpa_records WHERE student_id = ? AND semester_id = ?'
        );
        $stmt->execute([$studentId, $semesterId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * @return list<array<string,mixed>>
     */
    public static function historyForStudent(int $studentId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT s.label, s.academic_year, g.semester_id, g.gpa, g.computed_at
             FROM gpa_records g
             INNER JOIN semesters s ON s.id = g.semester_id
             WHERE g.student_id = ?
             ORDER BY s.academic_year ASC, s.label ASC'
        );
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public static function deleteForStudentSemester(int $studentId, int $semesterId): void
    {
        $stmt = Database::pdo()->prepare(
            'DELETE FROM gpa_records WHERE student_id = ? AND semester_id = ?'
        );
        $stmt->execute([$studentId, $semesterId]);
    }

    /**
     * Remove GPA rows for students no longer enrolled in the semester.
     */
    public static function pruneNonEnrolled(int $semesterId): void
    {
        $stmt = Database::pdo()->prepare(
            'DELETE g FROM gpa_records g
             LEFT JOIN enrollments e ON e.student_id = g.student_id AND e.semester_id = g.semester_id
             WHERE g.semester_id = ? AND e.student_id IS NULL'
        );
        $stmt->execute([$semesterId]);
    }
}

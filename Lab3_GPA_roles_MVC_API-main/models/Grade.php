<?php

declare(strict_types=1);

final class Grade
{
    /**
     * @return list<array<string,mixed>>
     */
    public static function studentsForCourse(int $courseId, int $semesterId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT u.id AS student_id, u.name AS student_name, g.grade
             FROM enrollments e
             INNER JOIN users u ON u.id = e.student_id AND u.role = \'student\'
             LEFT JOIN grades g ON g.student_id = u.id AND g.course_id = ? AND g.semester_id = e.semester_id
             WHERE e.semester_id = ?
             ORDER BY u.name ASC'
        );
        $stmt->execute([$courseId, $semesterId]);
        return $stmt->fetchAll();
    }

    public static function upsert(
        int $studentId,
        int $courseId,
        int $semesterId,
        int $professorId,
        ?string $gradeLetter
    ): void {
        if ($gradeLetter === null || $gradeLetter === '') {
            self::delete($studentId, $courseId, $semesterId);
            self::recomputeGpa($studentId, $semesterId);
            return;
        }
        $stmt = Database::pdo()->prepare(
            'INSERT INTO grades (student_id, course_id, semester_id, professor_id, grade)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE grade = VALUES(grade), professor_id = VALUES(professor_id)'
        );
        $stmt->execute([$studentId, $courseId, $semesterId, $professorId, $gradeLetter]);
        self::recomputeGpa($studentId, $semesterId);
    }

    public static function delete(int $studentId, int $courseId, int $semesterId): void
    {
        $stmt = Database::pdo()->prepare(
            'DELETE FROM grades WHERE student_id = ? AND course_id = ? AND semester_id = ?'
        );
        $stmt->execute([$studentId, $courseId, $semesterId]);
    }

    /**
     * GPA = SUM(points * credits) / SUM(credits) for graded courses in semester.
     */
    public static function recomputeGpa(int $studentId, int $semesterId): void
    {
        $stmt = Database::pdo()->prepare(
            'SELECT g.grade, c.credits
             FROM grades g
             INNER JOIN courses c ON c.id = g.course_id AND c.semester_id = g.semester_id
             WHERE g.student_id = ? AND g.semester_id = ? AND g.grade IS NOT NULL'
        );
        $stmt->execute([$studentId, $semesterId]);
        $rows = $stmt->fetchAll();
        $num = 0.0;
        $den = 0.0;
        foreach ($rows as $r) {
            $pts = grade_to_points((string) $r['grade']);
            if ($pts === null) {
                continue;
            }
            $credits = (float) $r['credits'];
            $num += $pts * $credits;
            $den += $credits;
        }
        $gpa = $den > 0 ? round($num / $den, 2) : 0.0;
        $upsert = Database::pdo()->prepare(
            'INSERT INTO gpa_records (student_id, semester_id, gpa)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE gpa = VALUES(gpa)'
        );
        $upsert->execute([$studentId, $semesterId, $gpa]);
    }

    /**
     * Recompute GPA for all students enrolled in semester (e.g. after enrollment sync).
     */
    public static function recomputeAllForSemester(int $semesterId): void
    {
        $stmt = Database::pdo()->prepare('SELECT student_id FROM enrollments WHERE semester_id = ?');
        $stmt->execute([$semesterId]);
        foreach ($stmt->fetchAll(\PDO::FETCH_COLUMN) as $sid) {
            self::recomputeGpa((int) $sid, $semesterId);
        }
    }

    /**
     * Remove grade rows for students no longer enrolled in the semester.
     */
    public static function removeOrphansForSemester(int $semesterId): void
    {
        $stmt = Database::pdo()->prepare(
            'DELETE g FROM grades g
             LEFT JOIN enrollments e ON e.student_id = g.student_id AND e.semester_id = g.semester_id
             WHERE g.semester_id = ? AND e.student_id IS NULL'
        );
        $stmt->execute([$semesterId]);
    }

    /**
     * @return list<array<string,mixed>>
     */
    public static function forStudentSemester(int $studentId, int $semesterId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT c.name AS course_name, c.credits, g.grade, u.name AS professor_name
             FROM grades g
             INNER JOIN courses c ON c.id = g.course_id AND c.semester_id = g.semester_id
             INNER JOIN users u ON u.id = g.professor_id
             WHERE g.student_id = ? AND g.semester_id = ? AND g.grade IS NOT NULL
             ORDER BY c.name ASC'
        );
        $stmt->execute([$studentId, $semesterId]);
        return $stmt->fetchAll();
    }

    /**
     * All courses in the semester for an enrolled student, with optional grade and assigned professor.
     *
     * @return list<array<string,mixed>>
     */
    public static function studentSemesterCourses(int $studentId, int $semesterId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT c.name AS course_name, c.credits, g.grade, u.name AS professor_name
             FROM enrollments e
             INNER JOIN courses c ON c.semester_id = e.semester_id
             LEFT JOIN assignments a ON a.course_id = c.id AND a.semester_id = e.semester_id
             LEFT JOIN users u ON u.id = a.professor_id
             LEFT JOIN grades g ON g.student_id = e.student_id AND g.course_id = c.id AND g.semester_id = e.semester_id
             WHERE e.student_id = ? AND e.semester_id = ?
             ORDER BY c.name ASC'
        );
        $stmt->execute([$studentId, $semesterId]);
        return $stmt->fetchAll();
    }
}

<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

Auth::requireRole(['professor'], true);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = isset($_GET['action']) ? preg_replace('/[^a-z_]/', '', (string) $_GET['action']) : '';

if ($method === 'GET' && $action === 'courses') {
    $semesterId = isset($_GET['semester_id']) ? (int) $_GET['semester_id'] : 0;
    if ($semesterId < 1) {
        json_response(['error' => 'semester_id required'], 400);
    }
    if (Semester::find($semesterId) === null) {
        json_response(['error' => 'Semester not found'], 400);
    }
    $pid = (int) Auth::userId();
    $courses = Assignment::forProfessorSemester($pid, $semesterId);
    json_response(['courses' => $courses]);
}

if ($method === 'GET' && $action === 'students') {
    $semesterId = isset($_GET['semester_id']) ? (int) $_GET['semester_id'] : 0;
    $courseId = isset($_GET['course_id']) ? (int) $_GET['course_id'] : 0;
    if ($semesterId < 1 || $courseId < 1) {
        json_response(['error' => 'semester_id and course_id required'], 400);
    }
    $pid = (int) Auth::userId();
    if (!Assignment::professorAssigned($pid, $courseId, $semesterId)) {
        json_response(['error' => 'Not assigned to this course'], 403);
    }
    $students = Grade::studentsForCourse($courseId, $semesterId);
    json_response(['students' => $students]);
}

if ($method === 'POST' && $action === 'save') {
    $raw = file_get_contents('php://input');
    $data = is_string($raw) ? json_decode($raw, true) : null;
    if (!is_array($data)) {
        json_response(['error' => 'Invalid JSON body'], 400);
    }
    $semesterId = isset($data['semester_id']) ? (int) $data['semester_id'] : 0;
    $courseId = isset($data['course_id']) ? (int) $data['course_id'] : 0;
    $grades = $data['grades'] ?? null;
    if ($semesterId < 1 || $courseId < 1 || !is_array($grades)) {
        json_response(['error' => 'semester_id, course_id, and grades[] required'], 400);
    }
    $pid = (int) Auth::userId();
    if (!Assignment::professorAssigned($pid, $courseId, $semesterId)) {
        json_response(['error' => 'Not assigned to this course'], 403);
    }
    $allowed = ['A', 'B', 'C', 'D', 'F', ''];
    foreach ($grades as $row) {
        if (!is_array($row)) {
            json_response(['error' => 'Invalid grades payload'], 400);
        }
        $sid = isset($row['student_id']) ? (int) $row['student_id'] : 0;
        $g = isset($row['grade']) ? strtoupper(trim((string) $row['grade'])) : '';
        if ($sid < 1) {
            json_response(['error' => 'Invalid student_id'], 400);
        }
        if (!in_array($g, $allowed, true)) {
            json_response(['error' => 'Invalid grade letter'], 400);
        }
        if (!Enrollment::isEnrolled($sid, $semesterId)) {
            json_response(['error' => 'Student not enrolled in semester'], 400);
        }
    }
    $pdo = Database::pdo();
    $pdo->beginTransaction();
    try {
        foreach ($grades as $row) {
            $sid = (int) $row['student_id'];
            $g = strtoupper(trim((string) $row['grade']));
            $letter = $g === '' ? null : $g;
            Grade::upsert($sid, $courseId, $semesterId, $pid, $letter);
        }
        $pdo->commit();
    } catch (\Throwable $e) {
        $pdo->rollBack();
        json_response(['error' => 'Could not save grades'], 500);
    }
    json_response(['ok' => true, 'message' => 'Grades saved']);
}

json_response(['error' => 'Not found'], 404);

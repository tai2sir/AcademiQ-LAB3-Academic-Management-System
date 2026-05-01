<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

Auth::requireRole(['student'], true);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'GET') {
    json_response(['error' => 'Method not allowed'], 405);
}

$action = isset($_GET['action']) ? preg_replace('/[^a-z_]/', '', (string) $_GET['action']) : 'current';
$studentId = (int) Auth::userId();

if ($action === 'current') {
    $sem = Semester::active();
    if ($sem === null) {
        json_response([
            'active' => false,
            'message' => 'No active semester configured.',
        ]);
    }
    $sid = (int) $sem['id'];
    $enrolled = Enrollment::isEnrolled($studentId, $sid);
    $gpaRow = GpaRecord::forStudentSemester($studentId, $sid);
    $courses = $enrolled ? Grade::studentSemesterCourses($studentId, $sid) : [];
    json_response([
        'active' => true,
        'enrolled' => $enrolled,
        'semester' => [
            'id' => $sid,
            'label' => $sem['label'],
            'academic_year' => $sem['academic_year'],
        ],
        'gpa' => $gpaRow ? (float) $gpaRow['gpa'] : 0.0,
        'courses' => $courses,
    ]);
}

if ($action === 'history') {
    $rows = GpaRecord::historyForStudent($studentId);
    json_response(['history' => $rows]);
}

if ($action === 'export') {
    $rows = GpaRecord::historyForStudent($studentId);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="gpa_history.csv"');
    $out = fopen('php://output', 'w');
    if ($out === false) {
        json_response(['error' => 'Export failed'], 500);
    }
    fputcsv($out, ['Semester', 'Academic year', 'Semester ID', 'GPA', 'Computed at']);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['label'],
            $r['academic_year'],
            $r['semester_id'],
            $r['gpa'],
            $r['computed_at'],
        ]);
    }
    fclose($out);
    exit;
}

json_response(['error' => 'Unknown action'], 400);

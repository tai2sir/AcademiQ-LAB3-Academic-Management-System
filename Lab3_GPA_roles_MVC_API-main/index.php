<?php

/**
 * Application Router - Routes page requests to controllers.
 */

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

// Sanitize page parameter
$page = isset($_GET['page']) ? preg_replace('/[^a-z0-9_]/', '', (string) $_GET['page']) : 'home';

// Redirect home to appropriate user dashboard or login
if ($page === '' || $page === 'home') {
    if (Auth::check()) {
        redirect(url(Auth::defaultPageForRole(Auth::role()) ?? 'login'));
    }
    redirect(url('login'));
}

// Define route mappings to controllers and methods
$routes = [
    'login' => [AuthController::class, 'login'],
    'logout' => [AuthController::class, 'logout'],
    'admin_dashboard' => [AdminController::class, 'dashboard'],
    'admin_semesters' => [AdminController::class, 'semesters'],
    'admin_courses' => [AdminController::class, 'courses'],
    'admin_professors' => [AdminController::class, 'professors'],
    'admin_students' => [AdminController::class, 'students'],
    'admin_enrollments' => [AdminController::class, 'enrollments'],
    'admin_assignments' => [AdminController::class, 'assignments'],
    'professor_grades' => [ProfessorController::class, 'grades'],
    'student_dashboard' => [StudentController::class, 'dashboard'],
    'student_history' => [StudentController::class, 'history'],
];

// Handle 404 for unknown routes
if (!isset($routes[$page])) {
    http_response_code(404);
    echo 'Page not found.';
    exit;
}

// Instantiate controller and execute action
[$class, $method] = $routes[$page];
$controller = new $class();
$controller->$method();

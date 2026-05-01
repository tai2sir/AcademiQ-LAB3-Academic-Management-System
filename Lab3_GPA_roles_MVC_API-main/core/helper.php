<?php

declare(strict_types=1);

/**
 * Escape output for HTML.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Base path of the application (folder containing index.php).
 */
function base_path(): string
{
    static $path;
    if ($path === null) {
        $path = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
        if ($path === '' || $path === '.') {
            $path = '';
        }
    }
    return $path;
}

/**
 * URL to asset under public/.
 */
function asset(string $path): string
{
    $p = ltrim($path, '/');
    return base_path() . '/public/' . $p;
}

/**
 * URL to index.php with query.
 */
function url(string $page, array $params = []): string
{
    $params = array_merge(['page' => $page], $params);
    return base_path() . '/index.php?' . http_build_query($params);
}

/**
 * Redirect and exit.
 */
function redirect(string $location): void
{
    header('Location: ' . $location);
    exit;
}

/**
 * JSON response for APIs.
 */
function json_response(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_THROW_ON_ERROR);
    exit;
}

/**
 * Grade letter to points (4.0 scale).
 */
function grade_to_points(string $letter): ?float
{
    $map = [
        'A' => 4.0,
        'B' => 3.0,
        'C' => 2.0,
        'D' => 1.0,
        'F' => 0.0,
    ];
    $k = strtoupper(trim($letter));
    return $map[$k] ?? null;
}

/**
 * Allowed grade letters.
 */
function allowed_grades(): array
{
    return ['A', 'B', 'C', 'D', 'F', ''];
}

/**
 * @return array{page:int,per_page:int,total_pages:int,offset:int}
 */
function pagination_state(int $total, int $page, int $perPage): array
{
    $totalPages = max(1, (int) ceil($total / $perPage));
    if ($page < 1) {
        $page = 1;
    }
    if ($page > $totalPages) {
        $page = $totalPages;
    }
    return [
        'page' => $page,
        'per_page' => $perPage,
        'total_pages' => $totalPages,
        'offset' => ($page - 1) * $perPage,
    ];
}

/**
 * Generate SVG icon
 */
function svg_icon(string $name, array $attrs = []): string
{
    $icons = [
        'dashboard' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>',
        'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
        'users-single' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
        'professor' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H17.5A2.5 2.5 0 0 1 20 19.5v.5H4v-.5z"></path><path d="M6.5 9a4 4 0 1 1 11 0v2H6.5V9z"></path></svg>',
        'students' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6m-4-6v6m-12-6v6M2 10h20"></path><path d="M4 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2"></path><line x1="6" y1="6" x2="6" y2="4"></line><line x1="12" y1="6" x2="12" y2="4"></line><line x1="18" y1="6" x2="18" y2="4"></line></svg>',
        'courses' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17h11A2.5 2.5 0 0 1 20 19.5m-16-7.5l9-5.5 9 5.5m-9-5.5L5 12m14 0l-9-5.5"></path></svg>',
        'semesters' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><circle cx="12" cy="17" r="3"></circle></svg>',
        'enrollments' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>',
        'assignments' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 3 21 3 21 8"></polyline><line x1="21" y1="3" x2="12" y2="12"></line><polyline points="15 21 21 21 21 15"></polyline></svg>',
        'info' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
        'grades' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
        'chart' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
        'home' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
        'moon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>',
        'sun' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>',
        'key' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-5.9-2a9 9 0 1 0 10.7 14.2"></path><circle cx="11" cy="13" r="1"></circle><line x1="16" y1="8" x2="21" y2="3"></line></svg>',
        'logout' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14"></path><polyline points="16 8 21 3 21 8"></polyline><line x1="21" y1="3" x2="9" y2="15"></line></svg>',
    ];

    $defaultAttrs = [
        'class' => 'svg-icon',
        'width' => '24',
        'height' => '24',
        'viewBox' => '0 0 24 24',
    ];

    $attrs = array_merge($defaultAttrs, $attrs);
    $attrStr = '';
    foreach ($attrs as $key => $value) {
        $attrStr .= ' ' . e($key) . '="' . e((string) $value) . '"';
    }

    if (!isset($icons[$name])) {
        return '';
    }

    $svg = $icons[$name];
    // Inject attributes before the closing >
    $svg = preg_replace('/<svg\s/', '<svg' . $attrStr . ' ', $svg);
    return $svg;
}

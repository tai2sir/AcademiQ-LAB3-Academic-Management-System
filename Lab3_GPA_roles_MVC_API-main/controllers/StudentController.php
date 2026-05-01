<?php

declare(strict_types=1);

final class StudentController
{
    public function dashboard(): void
    {
        Auth::requireRole(['student']);
        $this->render('student/dashboard', [
            'title' => 'My grades',
        ]);
    }

    public function history(): void
    {
        Auth::requireRole(['student']);
        $this->render('student/history', [
            'title' => 'GPA history',
        ]);
    }

    /**
     * @param array<string,mixed> $data
     */
    private function render(string $view, array $data): void
    {
        $data['viewFile'] = __DIR__ . '/../views/' . $view . '.php';
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/layouts/student.php';
    }
}

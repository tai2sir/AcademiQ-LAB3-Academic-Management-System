<?php

declare(strict_types=1);

final class ProfessorController
{
    public function grades(): void
    {
        Auth::requireRole(['professor']);
        $semesters = Semester::all();
        $this->render('professor/grades', [
            'title' => 'Grade entry',
            'semesters' => $semesters,
        ]);
    }

    /**
     * @param array<string,mixed> $data
     */
    private function render(string $view, array $data): void
    {
        $data['viewFile'] = __DIR__ . '/../views/' . $view . '.php';
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/layouts/professor.php';
    }
}

<?php

declare(strict_types=1);

final class AdminController
{
    public function dashboard(): void
    {
        Auth::requireRole(['admin']);
        $stats = [
            'students' => User::countByRole('student', null),
            'professors' => User::countByRole('professor', null),
            'courses' => Course::count(),
            'semesters' => Semester::count(),
        ];
        $this->render('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
        ]);
    }

    public function semesters(): void
    {
        Auth::requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleSemestersPost();
            return;
        }
        $editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
        $edit = $editId ? Semester::find($editId) : null;
        $this->render('admin/semesters', [
            'title' => 'Semesters',
            'semesters' => Semester::all(),
            'edit' => $edit,
        ]);
    }

    private function handleSemestersPost(): void
    {
        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'create') {
            $label = trim((string) ($_POST['label'] ?? ''));
            $year = trim((string) ($_POST['academic_year'] ?? ''));
            $active = isset($_POST['is_active']);
            $errs = array_merge(
                Validator::required($label, 'Label'),
                Validator::required($year, 'Academic year')
            );
            if ($errs === [] && Semester::existsLabelYear($label, $year, null)) {
                $errs[] = 'This semester label and year already exist.';
            }
            if ($errs !== []) {
                Flash::set('danger', implode(' ', $errs));
                redirect(url('admin_semesters'));
            }
            Semester::create($label, $year, $active);
            Flash::set('success', 'Semester created.');
            redirect(url('admin_semesters'));
        }
        if ($action === 'update') {
            $id = (int) ($_POST['id'] ?? 0);
            $label = trim((string) ($_POST['label'] ?? ''));
            $year = trim((string) ($_POST['academic_year'] ?? ''));
            $active = isset($_POST['is_active']);
            $errs = array_merge(
                Validator::required($label, 'Label'),
                Validator::required($year, 'Academic year')
            );
            if ($errs === [] && Semester::existsLabelYear($label, $year, $id)) {
                $errs[] = 'This semester label and year already exist.';
            }
            if ($errs !== [] || $id < 1) {
                Flash::set('danger', $errs !== [] ? implode(' ', $errs) : 'Invalid semester.');
                redirect(url('admin_semesters'));
            }
            Semester::update($id, $label, $year, $active);
            Flash::set('success', 'Semester updated.');
            redirect(url('admin_semesters'));
        }
        if ($action === 'activate') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id < 1 || Semester::find($id) === null) {
                Flash::set('danger', 'Invalid semester.');
                redirect(url('admin_semesters'));
            }
            Semester::setActive($id);
            Flash::set('success', 'Active semester updated.');
            redirect(url('admin_semesters'));
        }
        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id < 1) {
                Flash::set('danger', 'Invalid semester.');
                redirect(url('admin_semesters'));
            }
            if (Semester::hasCourses($id) || Semester::hasEnrollments($id)) {
                Flash::set('danger', 'Cannot delete: courses or enrollments are linked to this semester.');
                redirect(url('admin_semesters'));
            }
            try {
                Semester::delete($id);
                Flash::set('success', 'Semester deleted.');
            } catch (\PDOException $e) {
                Flash::set('danger', 'Cannot delete: related records exist.');
            }
            redirect(url('admin_semesters'));
        }
        Flash::set('danger', 'Unknown action.');
        redirect(url('admin_semesters'));
    }

    public function courses(): void
    {
        Auth::requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCoursesPost();
            return;
        }
        $editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
        $edit = $editId ? Course::find($editId) : null;
        $stmt = Database::pdo()->query(
            'SELECT c.*, s.label AS semester_label, s.academic_year
             FROM courses c
             INNER JOIN semesters s ON s.id = c.semester_id
             ORDER BY s.academic_year DESC, s.label, c.name'
        );
        $courses = $stmt->fetchAll();
        $this->render('admin/courses', [
            'title' => 'Courses',
            'courses' => $courses,
            'semesters' => Semester::all(),
            'edit' => $edit,
        ]);
    }

    private function handleCoursesPost(): void
    {
        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'create') {
            $sid = (int) ($_POST['semester_id'] ?? 0);
            $name = trim((string) ($_POST['name'] ?? ''));
            $credits = (string) ($_POST['credits'] ?? '0');
            $errs = array_merge(
                Validator::intPositive((string) $sid, 'Semester'),
                Validator::required($name, 'Course name'),
                Validator::floatNonNegative($credits, 'Credits')
            );
            if ((float) $credits > 3) {
                $errs[] = 'Credits cannot exceed 3.';
            }
            if ($errs !== []) {
                Flash::set('danger', implode(' ', $errs));
                redirect(url('admin_courses'));
            }
            if (Semester::find($sid) === null) {
                Flash::set('danger', 'Invalid semester.');
                redirect(url('admin_courses'));
            }
            Course::create($sid, $name, (float) $credits);
            Flash::set('success', 'Course created.');
            redirect(url('admin_courses'));
        }
        if ($action === 'update') {
            $id = (int) ($_POST['id'] ?? 0);
            $sid = (int) ($_POST['semester_id'] ?? 0);
            $name = trim((string) ($_POST['name'] ?? ''));
            $credits = (string) ($_POST['credits'] ?? '0');
            $errs = array_merge(
                Validator::intPositive((string) $id, 'Course'),
                Validator::intPositive((string) $sid, 'Semester'),
                Validator::required($name, 'Course name'),
                Validator::floatNonNegative($credits, 'Credits')
            );
            if ((float) $credits > 3) {
                $errs[] = 'Credits cannot exceed 3.';
            }
            if ($errs !== [] || Course::find($id) === null || Semester::find($sid) === null) {
                Flash::set('danger', $errs !== [] ? implode(' ', $errs) : 'Invalid data.');
                redirect(url('admin_courses'));
            }
            Course::update($id, $sid, $name, (float) $credits);
            Flash::set('success', 'Course updated.');
            redirect(url('admin_courses'));
        }
        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id < 1) {
                Flash::set('danger', 'Invalid course.');
                redirect(url('admin_courses'));
            }
            try {
                $pdo = Database::pdo();
                $pdo->beginTransaction();
                // Delete related grades
                $stmt = $pdo->prepare('DELETE FROM grades WHERE course_id = ?');
                $stmt->execute([$id]);
                // Delete related assignments
                $stmt = $pdo->prepare('DELETE FROM assignments WHERE course_id = ?');
                $stmt->execute([$id]);
                // Delete course
                Course::delete($id);
                $pdo->commit();
                Flash::set('success', 'Course deleted.');
            } catch (\Throwable $e) {
                $pdo->rollBack();
                Flash::set('danger', 'Cannot delete: an error occurred.');
            }
            redirect(url('admin_courses'));
        }
        Flash::set('danger', 'Unknown action.');
        redirect(url('admin_courses'));
    }

    public function professors(): void
    {
        Auth::requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleProfessorsPost();
            return;
        }
        $q = trim((string) ($_GET['q'] ?? ''));
        $qParam = $q !== '' ? $q : null;
        $page = max(1, (int) ($_GET['p'] ?? 1));
        $perPage = 10;
        $total = User::countByRole('professor', $qParam);
        $pg = pagination_state($total, $page, $perPage);
        $rows = User::allByRole('professor', $qParam, $perPage, $pg['offset']);
        $editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
        $edit = $editId ? User::find($editId) : null;
        if ($edit !== null && ($edit['role'] ?? '') !== 'professor') {
            $edit = null;
        }
        $this->render('admin/professors', [
            'title' => 'Professors',
            'professors' => $rows,
            'edit' => $edit,
            'search' => $q,
            'pagination' => $pg,
            'total' => $total,
        ]);
    }

    private function handleProfessorsPost(): void
    {
        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'create') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $pass = (string) ($_POST['password'] ?? '');
            $errs = array_merge(
                Validator::required($name, 'Name'),
                Validator::required($email, 'Email'),
                Validator::email($email, 'Email'),
                Validator::minLength($pass, 8, 'Password')
            );
            if ($errs === [] && User::findByEmail($email) !== null) {
                $errs[] = 'Email already in use.';
            }
            if ($errs !== []) {
                Flash::set('danger', implode(' ', $errs));
                redirect(url('admin_professors'));
            }
            User::create($name, $email, password_hash($pass, PASSWORD_BCRYPT), 'professor');
            Flash::set('success', 'Professor created.');
            redirect(url('admin_professors'));
        }
        if ($action === 'update') {
            $id = (int) ($_POST['id'] ?? 0);
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $pass = (string) ($_POST['password'] ?? '');
            $user = $id > 0 ? User::find($id) : null;
            if ($user === null || ($user['role'] ?? '') !== 'professor') {
                Flash::set('danger', 'Invalid professor.');
                redirect(url('admin_professors'));
            }
            $errs = array_merge(
                Validator::required($name, 'Name'),
                Validator::required($email, 'Email'),
                Validator::email($email, 'Email')
            );
            if ($errs === [] && User::emailExistsForOther($email, $id)) {
                $errs[] = 'Email already in use.';
            }
            $hash = null;
            if ($pass !== '') {
                $errs = array_merge($errs, Validator::minLength($pass, 8, 'Password'));
                $hash = password_hash($pass, PASSWORD_BCRYPT);
            }
            if ($errs !== []) {
                Flash::set('danger', implode(' ', $errs));
                redirect(url('admin_professors', ['edit' => (string) $id]));
            }
            User::update($id, $name, $email, $hash);
            Flash::set('success', 'Professor updated.');
            redirect(url('admin_professors'));
        }
        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            $user = $id > 0 ? User::find($id) : null;
            if ($user === null || ($user['role'] ?? '') !== 'professor') {
                Flash::set('danger', 'Invalid professor.');
                redirect(url('admin_professors'));
            }
            if ($this->professorHasLinks($id)) {
                Flash::set('danger', 'Cannot delete: professor has assignments or grades.');
                redirect(url('admin_professors'));
            }
            try {
                User::delete($id);
                Flash::set('success', 'Professor deleted.');
            } catch (\PDOException $e) {
                Flash::set('danger', 'Cannot delete: related records exist.');
            }
            redirect(url('admin_professors'));
        }
        Flash::set('danger', 'Unknown action.');
        redirect(url('admin_professors'));
    }

    private function professorHasLinks(int $professorId): bool
    {
        $pdo = Database::pdo();
        $s = $pdo->prepare('SELECT 1 FROM assignments WHERE professor_id = ? LIMIT 1');
        $s->execute([$professorId]);
        if ($s->fetchColumn()) {
            return true;
        }
        $s = $pdo->prepare('SELECT 1 FROM grades WHERE professor_id = ? LIMIT 1');
        $s->execute([$professorId]);
        return (bool) $s->fetchColumn();
    }

    public function students(): void
    {
        Auth::requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleStudentsPost();
            return;
        }
        $q = trim((string) ($_GET['q'] ?? ''));
        $qParam = $q !== '' ? $q : null;
        $page = max(1, (int) ($_GET['p'] ?? 1));
        $perPage = 10;
        $total = User::countByRole('student', $qParam);
        $pg = pagination_state($total, $page, $perPage);
        $rows = User::allByRole('student', $qParam, $perPage, $pg['offset']);
        $editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
        $edit = $editId ? User::find($editId) : null;
        if ($edit !== null && ($edit['role'] ?? '') !== 'student') {
            $edit = null;
        }
        $this->render('admin/students', [
            'title' => 'Students',
            'students' => $rows,
            'edit' => $edit,
            'search' => $q,
            'pagination' => $pg,
            'total' => $total,
        ]);
    }

    private function handleStudentsPost(): void
    {
        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'create') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $pass = (string) ($_POST['password'] ?? '');
            $errs = array_merge(
                Validator::required($name, 'Name'),
                Validator::required($email, 'Email'),
                Validator::email($email, 'Email'),
                Validator::minLength($pass, 8, 'Password')
            );
            if ($errs === [] && User::findByEmail($email) !== null) {
                $errs[] = 'Email already in use.';
            }
            if ($errs !== []) {
                Flash::set('danger', implode(' ', $errs));
                redirect(url('admin_students'));
            }
            User::create($name, $email, password_hash($pass, PASSWORD_BCRYPT), 'student');
            Flash::set('success', 'Student created.');
            redirect(url('admin_students'));
        }
        if ($action === 'update') {
            $id = (int) ($_POST['id'] ?? 0);
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $pass = (string) ($_POST['password'] ?? '');
            $user = $id > 0 ? User::find($id) : null;
            if ($user === null || ($user['role'] ?? '') !== 'student') {
                Flash::set('danger', 'Invalid student.');
                redirect(url('admin_students'));
            }
            $errs = array_merge(
                Validator::required($name, 'Name'),
                Validator::required($email, 'Email'),
                Validator::email($email, 'Email')
            );
            if ($errs === [] && User::emailExistsForOther($email, $id)) {
                $errs[] = 'Email already in use.';
            }
            $hash = null;
            if ($pass !== '') {
                $errs = array_merge($errs, Validator::minLength($pass, 8, 'Password'));
                $hash = password_hash($pass, PASSWORD_BCRYPT);
            }
            if ($errs !== []) {
                Flash::set('danger', implode(' ', $errs));
                redirect(url('admin_students', ['edit' => (string) $id]));
            }
            User::update($id, $name, $email, $hash);
            Flash::set('success', 'Student updated.');
            redirect(url('admin_students'));
        }
        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            $user = $id > 0 ? User::find($id) : null;
            if ($user === null || ($user['role'] ?? '') !== 'student') {
                Flash::set('danger', 'Invalid student.');
                redirect(url('admin_students'));
            }
            try {
                User::delete($id);
                Flash::set('success', 'Student deleted.');
            } catch (\PDOException $e) {
                Flash::set('danger', 'Cannot delete: related records exist.');
            }
            redirect(url('admin_students'));
        }
        Flash::set('danger', 'Unknown action.');
        redirect(url('admin_students'));
    }

    public function enrollments(): void
    {
        Auth::requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEnrollmentsPost();
            return;
        }
        $semesterId = isset($_GET['semester_id']) ? (int) $_GET['semester_id'] : 0;
        $semesters = Semester::all();
        $students = User::allByRole('student', null, 500, 0);
        $selected = $semesterId > 0 ? Enrollment::studentIdsForSemester($semesterId) : [];
        $this->render('admin/enrollments', [
            'title' => 'Enrollments',
            'semesters' => $semesters,
            'students' => $students,
            'semester_id' => $semesterId,
            'selected' => $selected,
        ]);
    }

    private function handleEnrollmentsPost(): void
    {
        $sid = (int) ($_POST['semester_id'] ?? 0);
        if ($sid < 1 || Semester::find($sid) === null) {
            Flash::set('danger', 'Select a valid semester.');
            redirect(url('admin_enrollments'));
        }
        $ids = $_POST['student_ids'] ?? [];
        if (!is_array($ids)) {
            $ids = [];
        }
        $clean = [];
        foreach ($ids as $id) {
            $clean[] = (int) $id;
        }
        Enrollment::syncSemester($sid, $clean);
        Grade::removeOrphansForSemester($sid);
        GpaRecord::pruneNonEnrolled($sid);
        Grade::recomputeAllForSemester($sid);
        Flash::set('success', 'Enrollments saved.');
        redirect(url('admin_enrollments', ['semester_id' => (string) $sid]));
    }

    public function assignments(): void
    {
        Auth::requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAssignmentsPost();
            return;
        }
        $semesterId = isset($_GET['semester_id']) ? (int) $_GET['semester_id'] : 0;
        $semesters = Semester::all();
        $courses = $semesterId > 0 ? Course::bySemester($semesterId) : [];
        $professors = User::allByRole('professor', null, 500, 0);
        $existing = [];
        if ($semesterId > 0) {
            foreach (Assignment::forSemesterWithDetails($semesterId) as $row) {
                $existing[(int) $row['course_id']] = (int) $row['professor_id'];
            }
        }
        $this->render('admin/assignments', [
            'title' => 'Professor assignments',
            'semesters' => $semesters,
            'courses' => $courses,
            'professors' => $professors,
            'semester_id' => $semesterId,
            'existing' => $existing,
        ]);
    }

    private function handleAssignmentsPost(): void
    {
        $sid = (int) ($_POST['semester_id'] ?? 0);
        if ($sid < 1 || Semester::find($sid) === null) {
            Flash::set('danger', 'Select a valid semester.');
            redirect(url('admin_assignments'));
        }
        $map = $_POST['professor'] ?? [];
        if (!is_array($map)) {
            $map = [];
        }
        $rows = [];
        foreach ($map as $courseId => $profId) {
            $cid = (int) $courseId;
            $pid = (int) $profId;
            if ($cid < 1) {
                continue;
            }
            $course = Course::find($cid);
            if ($course === null || (int) $course['semester_id'] !== $sid) {
                continue;
            }
            if ($pid < 1) {
                continue;
            }
            $p = User::find($pid);
            if ($p === null || ($p['role'] ?? '') !== 'professor') {
                continue;
            }
            $rows[] = ['course_id' => $cid, 'professor_id' => $pid];
        }
        Assignment::syncSemester($sid, $rows);
        Flash::set('success', 'Assignments saved.');
        redirect(url('admin_assignments', ['semester_id' => (string) $sid]));
    }

    /**
     * @param array<string,mixed> $data
     */
    private function render(string $view, array $data): void
    {
        $data['viewFile'] = __DIR__ . '/../views/' . $view . '.php';
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/layouts/admin.php';
    }
}

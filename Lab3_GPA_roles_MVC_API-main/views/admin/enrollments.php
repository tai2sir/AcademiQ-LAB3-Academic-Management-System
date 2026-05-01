<?php
declare(strict_types=1);
/** @var list<array<string,mixed>> $semesters */
/** @var list<array<string,mixed>> $students */
/** @var int $semester_id */
/** @var list<int> $selected */
$sel = array_flip($selected);
?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Enroll students into a semester</div>
    <div class="card-body">
        <form method="get" action="<?= e(url('admin_enrollments')) ?>" class="row g-3 align-items-end mb-4">
            <input type="hidden" name="page" value="admin_enrollments">
            <div class="col-md-6">
                <label class="form-label">Semester</label>
                <select class="form-select form-select-lg" name="semester_id" onchange="this.form.submit()">
                    <option value="">Select semester…</option>
                    <?php foreach ($semesters as $s): ?>
                        <option value="<?= e((string) $s['id']) ?>" <?= $semester_id === (int) $s['id'] ? 'selected' : '' ?>>
                            <?= e((string) $s['label']) ?> · <?= e((string) $s['academic_year']) ?>
                            <?= (int) $s['is_active'] === 1 ? ' (active)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if ($semester_id > 0): ?>
            <form method="post" action="<?= e(url('admin_enrollments')) ?>">
                <input type="hidden" name="semester_id" value="<?= e((string) $semester_id) ?>">
                <div class="row g-3">
                    <?php foreach ($students as $st): ?>
                        <div class="col-md-6 col-xl-4">
                            <div class="form-check enrollment-check p-3 rounded border">
                                <input class="form-check-input" type="checkbox" name="student_ids[]" value="<?= e((string) $st['id']) ?>"
                                       id="st_<?= e((string) $st['id']) ?>" <?= isset($sel[(int) $st['id']]) ? 'checked' : '' ?>>
                                <label class="form-check-label w-100" for="st_<?= e((string) $st['id']) ?>">
                                    <span class="fw-semibold d-block"><?= e((string) $st['name']) ?></span>
                                    <span class="small text-muted"><?= e((string) $st['email']) ?></span>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4">Save enrollments</button>
                </div>
            </form>
        <?php else: ?>
            <p class="text-muted mb-0">Choose a semester to manage enrollments.</p>
        <?php endif; ?>
    </div>
</div>

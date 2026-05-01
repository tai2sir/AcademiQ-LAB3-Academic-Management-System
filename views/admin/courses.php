<?php
declare(strict_types=1);
/** @var list<array<string,mixed>> $courses */
/** @var list<array<string,mixed>> $semesters */
/** @var ?array<string,mixed> $edit */
?>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold"><?= $edit ? 'Edit course' : 'New course' ?></div>
            <div class="card-body">
                <?php if ($edit): ?>
                    <form method="post" class="vstack gap-3">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= e((string) $edit['id']) ?>">
                        <div>
                            <label class="form-label">Semester</label>
                            <select class="form-select" name="semester_id" required>
                                <?php foreach ($semesters as $s): ?>
                                    <option value="<?= e((string) $s['id']) ?>" <?= ((int) $edit['semester_id'] === (int) $s['id']) ? 'selected' : '' ?>>
                                        <?= e((string) $s['label']) ?> · <?= e((string) $s['academic_year']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Name</label>
                            <input class="form-control" name="name" value="<?= e((string) $edit['name']) ?>" required>
                        </div>
                        <div>
                            <label class="form-label">Credits</label>
                            <input class="form-control" type="number" step="1" min="0" max="3" name="credits" value="<?= e((string) $edit['credits']) ?>" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary">Save</button>
                            <a class="btn btn-outline-secondary" href="<?= e(url('admin_courses')) ?>">Cancel</a>
                        </div>
                    </form>
                <?php else: ?>
                    <form method="post" class="vstack gap-3">
                        <input type="hidden" name="action" value="create">
                        <div>
                            <label class="form-label">Semester</label>
                            <select class="form-select" name="semester_id" required>
                                <?php foreach ($semesters as $s): ?>
                                    <option value="<?= e((string) $s['id']) ?>">
                                        <?= e((string) $s['label']) ?> · <?= e((string) $s['academic_year']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Name</label>
                            <input class="form-control" name="name" required>
                        </div>
                        <div>
                            <label class="form-label">Credits</label>
                            <input class="form-control" type="number" step="1" min="0" max="3" name="credits" value="3" required>
                        </div>
                        <button class="btn btn-primary">Create</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Courses</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 data-table">
                    <thead>
                    <tr>
                        <th>Course</th>
                        <th>Semester</th>
                        <th>Credits</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($courses as $c): ?>
                        <tr>
                            <td><?= e((string) $c['name']) ?></td>
                            <td><?= e((string) $c['semester_label']) ?> · <?= e((string) $c['academic_year']) ?></td>
                            <td><?= e((string) $c['credits']) ?></td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-primary" href="<?= e(url('admin_courses', ['edit' => (string) $c['id']])) ?>">Edit</a>
                                <form method="post" class="d-inline" onsubmit="return confirm('Delete course?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e((string) $c['id']) ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

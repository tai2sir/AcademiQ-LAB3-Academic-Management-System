<?php
declare(strict_types=1);
/** @var list<array<string,mixed>> $semesters */
/** @var list<array<string,mixed>> $courses */
/** @var list<array<string,mixed>> $professors */
/** @var int $semester_id */
/** @var array<int,int> $existing */
?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Assign professors to courses</div>
    <div class="card-body">
        <form method="get" action="<?= e(url('admin_assignments')) ?>" class="row g-3 align-items-end mb-4">
            <input type="hidden" name="page" value="admin_assignments">
            <div class="col-md-6">
                <label class="form-label">Semester</label>
                <select class="form-select form-select-lg" name="semester_id" onchange="this.form.submit()">
                    <option value="">Select semester…</option>
                    <?php foreach ($semesters as $s): ?>
                        <option value="<?= e((string) $s['id']) ?>" <?= $semester_id === (int) $s['id'] ? 'selected' : '' ?>>
                            <?= e((string) $s['label']) ?> · <?= e((string) $s['academic_year']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if ($semester_id > 0 && $courses !== []): ?>
            <form method="post" action="<?= e(url('admin_assignments')) ?>">
                <input type="hidden" name="semester_id" value="<?= e((string) $semester_id) ?>">
                <div class="table-responsive">
                    <table class="table table-hover align-middle data-table">
                        <thead>
                        <tr>
                            <th>Course</th>
                            <th>Credits</th>
                            <th>Professor</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($courses as $c): ?>
                            <?php $cid = (int) $c['id']; ?>
                            <tr>
                                <td class="fw-semibold"><?= e((string) $c['name']) ?></td>
                                <td><?= e((string) $c['credits']) ?></td>
                                <td style="min-width: 220px;">
                                    <select class="form-select" name="professor[<?= e((string) $cid) ?>]">
                                        <option value="0">— Unassigned —</option>
                                        <?php foreach ($professors as $pr): ?>
                                            <?php $pid = (int) $pr['id']; ?>
                                            <option value="<?= e((string) $pid) ?>" <?= (($existing[$cid] ?? 0) === $pid) ? 'selected' : '' ?>>
                                                <?= e((string) $pr['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4 mt-2">Save assignments</button>
            </form>
        <?php elseif ($semester_id > 0): ?>
            <p class="text-muted mb-0">No courses for this semester yet.</p>
        <?php else: ?>
            <p class="text-muted mb-0">Select a semester.</p>
        <?php endif; ?>
    </div>
</div>

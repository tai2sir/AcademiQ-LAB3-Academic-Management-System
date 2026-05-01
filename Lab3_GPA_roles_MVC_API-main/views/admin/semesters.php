<?php
declare(strict_types=1);
/** @var list<array<string,mixed>> $semesters */
/** @var ?array<string,mixed> $edit */
?>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold"><?= $edit ? 'Edit semester' : 'New semester' ?></div>
            <div class="card-body">
                <?php if ($edit): ?>
                    <form method="post" class="vstack gap-3">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= e((string) $edit['id']) ?>">
                        <div>
                            <label class="form-label">Label</label>
                            <input class="form-control" name="label" value="<?= e((string) $edit['label']) ?>" required>
                        </div>
                        <div>
                            <label class="form-label">Academic year</label>
                            <input class="form-control" name="academic_year" value="<?= e((string) $edit['academic_year']) ?>" required>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active_e" <?= ((int) $edit['is_active'] === 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active_e">Active (only one should be active)</label>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary">Save</button>
                            <a class="btn btn-outline-secondary" href="<?= e(url('admin_semesters')) ?>">Cancel</a>
                        </div>
                    </form>
                <?php else: ?>
                    <form method="post" class="vstack gap-3">
                        <input type="hidden" name="action" value="create">
                        <div>
                            <label class="form-label">Label</label>
                            <input class="form-control" name="label" placeholder="Fall" required>
                        </div>
                        <div>
                            <label class="form-label">Academic year</label>
                            <input class="form-control" name="academic_year" placeholder="2025-2026" required>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active_c">
                            <label class="form-check-label" for="is_active_c">Set as active semester</label>
                        </div>
                        <button class="btn btn-primary">Create</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>All semesters</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 data-table">
                    <thead>
                    <tr>
                        <th>Label</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($semesters as $s): ?>
                        <tr>
                            <td><?= e((string) $s['label']) ?></td>
                            <td><?= e((string) $s['academic_year']) ?></td>
                            <td>
                                <?php if ((int) $s['is_active'] === 1): ?>
                                    <span class="badge rounded-pill text-bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill text-bg-light border">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-primary" href="<?= e(url('admin_semesters', ['edit' => (string) $s['id']])) ?>">Edit</a>
                                <form method="post" class="d-inline" onsubmit="return confirm('Activate this semester? Others will be deactivated.');">
                                    <input type="hidden" name="action" value="activate">
                                    <input type="hidden" name="id" value="<?= e((string) $s['id']) ?>">
                                    <button class="btn btn-sm btn-outline-success" type="submit">Activate</button>
                                </form>
                                <form method="post" class="d-inline" onsubmit="return confirm('Delete this semester?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e((string) $s['id']) ?>">
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

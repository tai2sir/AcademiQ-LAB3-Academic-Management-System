<?php
declare(strict_types=1);
/** @var list<array<string,mixed>> $semesters */
?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100 filter-card">
            <div class="card-header bg-white fw-semibold">Selection</div>
            <div class="card-body vstack gap-3">
                <div>
                    <label class="form-label">Semester</label>
                    <select id="pg-semester" class="form-select form-select-lg">
                        <option value="">Choose semester…</option>
                        <?php foreach ($semesters as $s): ?>
                            <option value="<?= e((string) $s['id']) ?>">
                                <?= e((string) $s['label']) ?> · <?= e((string) $s['academic_year']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Course</label>
                    <select id="pg-course" class="form-select form-select-lg" disabled>
                        <option value="">Select semester first…</option>
                    </select>
                </div>
                <div id="pg-status" class="small text-muted"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="fw-semibold">Students</span>
                <button type="button" id="pg-save" class="btn btn-primary rounded-pill px-4" disabled>Save grades</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 data-table" id="pg-table">
                    <thead>
                    <tr>
                        <th>Student</th>
                        <th style="min-width: 140px;">Grade</th>
                    </tr>
                    </thead>
                    <tbody id="pg-tbody">
                    <tr><td colspan="2" class="text-muted p-4">Select a course to load students.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

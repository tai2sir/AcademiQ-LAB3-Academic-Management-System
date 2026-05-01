<?php
declare(strict_types=1);
?>
<div id="sd-loading" class="text-muted">Loading your dashboard…</div>
<div id="sd-content" class="d-none">
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div id="sd-gpa-card" class="gpa-hero card border-0 shadow text-white">
                <div class="card-body p-4 p-md-5">
                    <div class="small text-white-50 text-uppercase fw-semibold mb-1">Current semester GPA</div>
                    <div class="display-4 fw-bold" id="sd-gpa-value">—</div>
                    <div class="mt-2 opacity-90" id="sd-semester-label"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h3 class="h6 fw-semibold">How it works</h3>
                    <p class="text-muted small mb-0">Grades update when your instructors save them. GPA uses the 4.0 scale weighted by credits: A=4, B=3, C=2, D=1, F=0.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Courses this semester</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 data-table">
                <thead>
                <tr>
                    <th>Course</th>
                    <th>Credits</th>
                    <th>Professor</th>
                    <th>Grade</th>
                </tr>
                </thead>
                <tbody id="sd-courses-body">
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="sd-empty" class="alert alert-info border-0 shadow-sm d-none mb-0"></div>

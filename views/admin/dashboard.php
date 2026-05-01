<?php
declare(strict_types=1);
/** @var array{students:int,professors:int,courses:int,semesters:int} $stats */
?>
<div class="dashboard-header">
    <div class="dashboard-title">
        <h1>Dashboard</h1>
        <p class="text-muted">System overview and key metrics</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card-compact stat-card-compact--violet">
        <div class="stat-compact-top">
            <span class="stat-icon"><?php echo svg_icon('students', ['class' => 'svg-icon-24']) ?></span>
            <span class="stat-value-compact"><?= e((string) $stats['students']) ?></span>
        </div>
        <div class="stat-label-compact">Students</div>
    </div>
    <div class="stat-card-compact stat-card-compact--blue">
        <div class="stat-compact-top">
            <span class="stat-icon"><?php echo svg_icon('professor', ['class' => 'svg-icon-24']) ?></span>
            <span class="stat-value-compact"><?= e((string) $stats['professors']) ?></span>
        </div>
        <div class="stat-label-compact">Professors</div>
    </div>
    <div class="stat-card-compact stat-card-compact--teal">
        <div class="stat-compact-top">
            <span class="stat-icon"><?php echo svg_icon('courses', ['class' => 'svg-icon-24']) ?></span>
            <span class="stat-value-compact"><?= e((string) $stats['courses']) ?></span>
        </div>
        <div class="stat-label-compact">Courses</div>
    </div>
    <div class="stat-card-compact stat-card-compact--amber">
        <div class="stat-compact-top">
            <span class="stat-icon"><?php echo svg_icon('semesters', ['class' => 'svg-icon-24']) ?></span>
            <span class="stat-value-compact"><?= e((string) $stats['semesters']) ?></span>
        </div>
        <div class="stat-label-compact">Semesters</div>
    </div>
</div>

<div class="dashboard-info card border-0 shadow-sm mt-4">
    <div class="card-body">
        <div class="info-content">
            <div class="info-icon"><?php echo svg_icon('info', ['class' => 'svg-icon-20']) ?></div>
            <div class="info-text">
                <h3 class="h6 fw-semibold mb-1">Quick Start</h3>
                <p class="mb-0 text-muted">Use the sidebar menu to manage semesters, courses, professors, students, enrollments, and assignments. Student GPA updates automatically when grades are submitted.</p>
            </div>
        </div>
    </div>
</div>

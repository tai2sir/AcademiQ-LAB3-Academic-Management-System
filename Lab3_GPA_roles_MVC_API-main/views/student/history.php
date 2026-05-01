<?php
declare(strict_types=1);
?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="fw-semibold">GPA trend</span>
                <a class="btn btn-sm btn-outline-primary rounded-pill" id="sh-csv" href="#">Export CSV</a>
            </div>
            <div class="card-body">
                <canvas id="sh-chart" height="120"></canvas>
                <div id="sh-chart-empty" class="text-muted small d-none">No history yet.</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold">Semester breakdown</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" id="sh-list"></ul>
            </div>
        </div>
    </div>
</div>

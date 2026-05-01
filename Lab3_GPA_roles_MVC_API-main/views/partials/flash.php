<?php
$flashes = Flash::get();
foreach ($flashes as $type => $messages) {
    foreach ($messages as $msg) {
        $alert = match ($type) {
            'success' => 'success',
            'danger' => 'danger',
            'warning' => 'warning',
            'info' => 'info',
            default => 'secondary',
        };
        ?>
        <div class="alert alert-<?= e($alert) ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= e($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
    }
}

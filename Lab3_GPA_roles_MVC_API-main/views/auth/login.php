<?php
declare(strict_types=1);
?>
<div class="card shadow-lg border-0 login-card">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="login-badge mb-2">AcademiQ</div>
            <h2 class="h4 fw-bold">Sign in to continue</h2>
            <p class="text-muted small mb-0">Secure session · Role-based access</p>
        </div>
        <form method="post" action="<?= e(url('login')) ?>" class="needs-validation" novalidate>
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input type="email" class="form-control form-control-lg" id="email" name="email" required autocomplete="username">
            </div>
            <div class="mb-4">
                <label class="form-label" for="password">Password</label>
                <input type="password" class="form-control form-control-lg" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill">Sign in</button>
        </form>
        <p class="text-center text-muted small mt-4 mb-0">
            Demo: <span class="fw-semibold">admin@gpa.local</span> / <span class="fw-semibold">password</span>
        </p>
    </div>
</div>

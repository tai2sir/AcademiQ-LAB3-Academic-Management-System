<?php declare(strict_types=1);
/** @var string $roleLabel */
/** @var string $title */
$name = Auth::name() ?? '';
$initials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', trim($name)), 0, 2)));
?>
<nav class="app-navbar">
    <!-- Logo + Name -->
    <a href="<?= e(url(Auth::defaultPageForRole(Auth::role()) ?? 'login')) ?>" class="navbar-brand-name d-flex align-items-center gap-2 text-decoration-none">
        <div class="navbar-logo">AQ</div>
        <span class="navbar-brand-name">Academi<span>Q</span></span>
    </a>

    <div class="navbar-spacer"></div>

    <!-- Page title (center) -->
    <span class="navbar-page-title"><?= e($title) ?></span>

    <div class="navbar-spacer"></div>

    <!-- Theme toggle -->
    <button class="theme-toggle" id="themeToggle" title="Toggle theme">
        <span class="theme-icon-light"><?php echo svg_icon('moon', ['class' => 'svg-icon-20']) ?></span>
        <span class="theme-icon-dark" style="display:none;"><?php echo svg_icon('sun', ['class' => 'svg-icon-20']) ?></span>
    </button>

    <!-- User dropdown -->
    <div class="dropdown">
        <a class="user-dropdown-toggle dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="user-avatar"><?= e($initials ?: '?') ?></div>
            <div class="d-none d-sm-flex flex-column" style="line-height:1.2">
                <span><?= e($name) ?></span>
                <span class="user-role-badge"><?= e($roleLabel) ?></span>
            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><h6 class="dropdown-header"><?= e($name) ?></h6></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#"><?php echo svg_icon('key', ['class' => 'nav-icon']) ?>Change Password</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="<?= e(url('logout')) ?>"><?php echo svg_icon('logout', ['class' => 'nav-icon']) ?>Sign out</a></li>
        </ul>
    </div>
</nav>

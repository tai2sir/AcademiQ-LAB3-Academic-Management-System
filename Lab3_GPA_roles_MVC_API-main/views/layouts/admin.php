<?php declare(strict_types=1);
/** @var string $viewFile */
/** @var string $title */
$roleLabel = 'Administration';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>
      const t = localStorage.getItem('academiq-theme') || 'light';
      document.documentElement.setAttribute('data-theme', t);
    </script>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · AcademiQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="app-body">
<div class="app-wrapper">

    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <div class="app-body-inner">
        <aside class="sidebar">
            <span class="sidebar-section-label">Menu</span>
            <nav class="sidebar-nav">
                <a class="nav-link <?= str_contains($_SERVER['QUERY_STRING']??'','admin_dashboard') ? 'active' : '' ?>" href="<?= e(url('admin_dashboard')) ?>"><?php echo svg_icon('dashboard', ['class' => 'nav-icon']) ?> Dashboard</a>
                <a class="nav-link <?= str_contains($_SERVER['QUERY_STRING']??'','admin_semesters') ? 'active' : '' ?>" href="<?= e(url('admin_semesters')) ?>"><?php echo svg_icon('semesters', ['class' => 'nav-icon']) ?> Semesters</a>
                <a class="nav-link <?= str_contains($_SERVER['QUERY_STRING']??'','admin_courses') ? 'active' : '' ?>" href="<?= e(url('admin_courses')) ?>"><?php echo svg_icon('courses', ['class' => 'nav-icon']) ?> Courses</a>
                <a class="nav-link <?= str_contains($_SERVER['QUERY_STRING']??'','admin_professors') ? 'active' : '' ?>" href="<?= e(url('admin_professors')) ?>"><?php echo svg_icon('professor', ['class' => 'nav-icon']) ?> Professors</a>
                <a class="nav-link <?= str_contains($_SERVER['QUERY_STRING']??'','admin_students') ? 'active' : '' ?>" href="<?= e(url('admin_students')) ?>"><?php echo svg_icon('users-single', ['class' => 'nav-icon']) ?> Students</a>
                <a class="nav-link <?= str_contains($_SERVER['QUERY_STRING']??'','admin_enrollments') ? 'active' : '' ?>" href="<?= e(url('admin_enrollments')) ?>"><?php echo svg_icon('enrollments', ['class' => 'nav-icon']) ?> Enrollments</a>
                <a class="nav-link <?= str_contains($_SERVER['QUERY_STRING']??'','admin_assignments') ? 'active' : '' ?>" href="<?= e(url('admin_assignments')) ?>"><?php echo svg_icon('assignments', ['class' => 'nav-icon']) ?> Assignments</a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="content-pad">
                <?php require __DIR__ . '/../partials/flash.php'; ?>
                <?php require $viewFile; ?>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
  const t=localStorage.getItem('academiq-theme')||'light';
  document.documentElement.setAttribute('data-theme',t);
  updateThemeIcon(t);
  document.getElementById('themeToggle')?.addEventListener('click',function(){
    const cur=document.documentElement.getAttribute('data-theme')||'light';
    const next=cur==='dark'?'light':'dark';
    document.documentElement.setAttribute('data-theme',next);
    localStorage.setItem('academiq-theme',next);
    updateThemeIcon(next);
  });
  function updateThemeIcon(theme){
    const lightIcon=document.querySelector('.theme-icon-light');
    const darkIcon=document.querySelector('.theme-icon-dark');
    if(theme==='dark'){
      if(lightIcon)lightIcon.style.display='none';
      if(darkIcon)darkIcon.style.display='block';
    }else{
      if(lightIcon)lightIcon.style.display='block';
      if(darkIcon)darkIcon.style.display='none';
    }
  }
})();
</script>
</body>
</html>

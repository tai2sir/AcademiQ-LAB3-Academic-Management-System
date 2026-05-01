<?php declare(strict_types=1);
/** @var string $viewFile */
/** @var string $title */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · AcademiQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="guest-bg d-flex align-items-center min-vh-100">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <?php require __DIR__ . '/../partials/flash.php'; ?>
            <?php require $viewFile; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
  const t=localStorage.getItem('academiq-theme')||'light';
  document.documentElement.setAttribute('data-theme',t);
})();
</script>
</body>
</html>

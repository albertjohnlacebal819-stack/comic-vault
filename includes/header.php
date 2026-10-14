<?php
if(!isset($pageTitle)) {
    $pageTitle = "Comic Books";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo h($pageTitle); ?> | Comic Vault</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $base; ?>/assets/style.css" rel="stylesheet">
</head>
<body class="role-<?php echo $role; ?>">
<nav class="navbar navbar-expand-md navbar-light site-nav">
  <div class="container">
    <a class="navbar-brand" href="<?php echo $base; ?>/<?php echo ($role == 'admin') ? 'admin/dashboard.php' : 'index.php'; ?>">
      <span class="brand-mark"></span>Comic Vault
      <?php if($role == 'admin') { ?><span class="role-tag">ADMIN</span><?php } ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto">
        <?php if($role == 'admin') { ?>
          <li class="nav-item"><a class="nav-link<?php echo activeClass('dashboard.php'); ?>" href="<?php echo $base; ?>/admin/dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link<?php echo activeClass('index.php'); ?>" href="<?php echo $base; ?>/index.php">Discover</a></li>
          <li class="nav-item"><a class="nav-link<?php echo activeClass('users.php'); ?>" href="<?php echo $base; ?>/admin/users.php">Users</a></li>
        <?php } elseif($role == 'user') { ?>
          <li class="nav-item"><a class="nav-link<?php echo activeClass('index.php'); ?>" href="<?php echo $base; ?>/index.php">Discover</a></li>
          <li class="nav-item"><a class="nav-link<?php echo activeClass('mycollection.php'); ?>" href="<?php echo $base; ?>/mycollection.php">My Collection</a></li>
        <?php } else { ?>
          <li class="nav-item"><a class="nav-link<?php echo activeClass('index.php'); ?>" href="<?php echo $base; ?>/index.php">Discover</a></li>
        <?php } ?>
      </ul>
      <ul class="navbar-nav ms-auto align-items-md-center gap-md-2">
        <?php if($role != 'guest') { ?>
          <li class="nav-item"><span class="nav-user-name me-md-2"><?php echo h($_SESSION['name']); ?></span></li>
          <li class="nav-item"><a class="btn btn-line btn-sm" href="<?php echo $base; ?>/logout.php">Log out</a></li>
        <?php } else { ?>
          <li class="nav-item"><a class="nav-link<?php echo activeClass('login.php'); ?>" href="<?php echo $base; ?>/login.php">Log in</a></li>
          <li class="nav-item"><a class="btn btn-accent btn-sm" href="<?php echo $base; ?>/register.php">Sign up</a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
</nav>
<main class="container my-4">
<?php showMessage(); ?>

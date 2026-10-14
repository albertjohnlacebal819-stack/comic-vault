<?php
include 'connection.php';

if($logged_in) {
    redirectTo(($role == 'admin') ? "admin/dashboard.php" : "mycollection.php");
}

$error = "";

if(isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    $user = mysqli_fetch_assoc($result);

    if($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        mysqli_query($conn, "UPDATE users SET last_login = NOW() WHERE id = " . (int)$user['id']);

        setMessage("Welcome, " . $user['name'] . "!", "success");
        redirectTo(($user['role'] == 'admin') ? "admin/dashboard.php" : "mycollection.php");
    } else {
        $error = "Wrong email or password.";
    }
}

$pageTitle = "Login";
include 'includes/header.php';
?>
<div class="col-md-6 col-lg-4 mx-auto pt-md-4">
  <div class="text-center mb-4">
    <div class="eyebrow">Welcome back</div>
    <h1 class="h2 mb-0">Log in</h1>
  </div>
  <div class="panel panel-pad">
    <?php if($error != "") { ?><div class="alert flash flash-danger"><?php echo h($error); ?></div><?php } ?>
    <form method="POST" class="needs-validation" novalidate>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required autofocus>
        <div class="invalid-feedback">Enter a valid email.</div>
      </div>
      <div class="mb-4">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
        <div class="invalid-feedback">Password is required.</div>
      </div>
      <button type="submit" name="login" class="btn btn-accent w-100">Log in</button>
    </form>
  </div>
  <p class="text-center text-muted small mt-3">New here? <a href="<?php echo $base; ?>/register.php">Create an account</a></p>
</div>
<?php include 'includes/footer.php'; ?>

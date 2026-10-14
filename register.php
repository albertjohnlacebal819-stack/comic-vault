<?php
include 'connection.php';

if($logged_in) {
    redirectTo("mycollection.php");
}

$error = "";
$name = "";
$email = "";

if(isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if($name == "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $error = "Fill in all fields (password at least 6 characters).";
    } else {
        $name_sql = mysqli_real_escape_string($conn, $name);
        $email_sql = mysqli_real_escape_string($conn, $email);

        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email_sql'");
        if(mysqli_num_rows($check) > 0) {
            $error = "That email is already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users(name, email, password_hash) VALUES('$name_sql', '$email_sql', '$hash')";
            mysqli_query($conn, $sql);

            setMessage("Account created. Please log in.", "success");
            redirectTo("login.php");
        }
    }
}

$pageTitle = "Register";
include 'includes/header.php';
?>
<div class="col-md-6 col-lg-4 mx-auto pt-md-4">
  <div class="text-center mb-4">
    <div class="eyebrow">Join the vault</div>
    <h1 class="h2 mb-0">Create account</h1>
  </div>
  <div class="panel panel-pad">
    <?php if($error != "") { ?><div class="alert flash flash-danger"><?php echo h($error); ?></div><?php } ?>
    <form method="POST" class="needs-validation" novalidate>
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo h($name); ?>" required autofocus>
        <div class="invalid-feedback">Name is required.</div>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo h($email); ?>" required>
        <div class="invalid-feedback">Enter a valid email.</div>
      </div>
      <div class="mb-4">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" minlength="6" required>
        <div class="invalid-feedback">At least 6 characters.</div>
      </div>
      <button type="submit" name="register" class="btn btn-accent w-100">Create account</button>
    </form>
  </div>
  <p class="text-center text-muted small mt-3">Already have an account? <a href="<?php echo $base; ?>/login.php">Log in</a></p>
</div>
<?php include 'includes/footer.php'; ?>

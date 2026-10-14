<?php
include '../connection.php';

if(!$logged_in) {
    setMessage("Please log in to manage comics.", "warning");
    redirectTo("login.php");
}
if($role != 'admin') {
    http_response_code(403);
    die("Admins only.");
}

$userCount = getCount($conn, "SELECT COUNT(*) FROM users WHERE role = 'user'");
$adminCount = getCount($conn, "SELECT COUNT(*) FROM users WHERE role = 'admin'");
$comicCount = getCount($conn, "SELECT COUNT(*) FROM comics");
$genreCount = getCount($conn, "SELECT COUNT(DISTINCT genre) FROM comics");

$stats = array(
    "Registered users" => $userCount,
    "Admins" => $adminCount,
    "Total comics" => $comicCount,
    "Genres" => $genreCount
);

$recentUsers = array();
$result = mysqli_query($conn, "SELECT name, email, role, last_login FROM users ORDER BY created_at DESC, id DESC LIMIT 5");
while($row = mysqli_fetch_assoc($result)) {
    $recentUsers[] = $row;
}

$recentComics = array();
$result = mysqli_query($conn, "SELECT comics.id, comics.title, comics.genre, users.name AS owner FROM comics JOIN users ON users.id = comics.user_id ORDER BY comics.id DESC LIMIT 5");
while($row = mysqli_fetch_assoc($result)) {
    $recentComics[] = $row;
}

$pageTitle = "Admin Dashboard";
include '../includes/header.php';
?>
<div class="page-head">
  <div>
    <div class="eyebrow">Admin</div>
    <h1>Dashboard</h1>
    <p>Overview of the whole vault.</p>
  </div>
  <div class="d-flex gap-2">
    <a href="<?php echo $base; ?>/index.php" class="btn btn-ink">Manage comics</a>
    <a href="<?php echo $base; ?>/admin/users.php" class="btn btn-line">Manage users</a>
  </div>
</div>

<div class="row g-3 mb-4">
  <?php foreach($stats as $label => $number) { ?>
    <div class="col-6 col-md-3">
      <div class="panel stat">
        <div class="stat-number"><?php echo $number; ?></div>
        <div class="stat-label"><?php echo $label; ?></div>
      </div>
    </div>
  <?php } ?>
</div>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="panel h-100"><div class="panel-title">Newest users</div>
      <div class="table-responsive"><table class="table align-middle">
        <thead><tr><th>Name</th><th>Role</th><th>Last login</th></tr></thead>
        <tbody>
        <?php foreach($recentUsers as $u) { ?>
          <tr>
            <td><?php echo h($u['name']); ?><div class="small text-muted"><?php echo h($u['email']); ?></div></td>
            <td><span class="chip <?php if($u['role'] == 'admin') { echo 'chip-admin'; } ?>"><?php echo $u['role']; ?></span></td>
            <td class="small text-muted"><?php echo $u['last_login'] ? date('M j, g:i A', strtotime($u['last_login'])) : "Never"; ?></td>
          </tr>
        <?php } ?>
        </tbody>
      </table></div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="panel h-100"><div class="panel-title">Latest comics</div>
      <div class="table-responsive"><table class="table align-middle">
        <thead><tr><th>Title</th><th>Genre</th><th>Owner</th></tr></thead>
        <tbody>
        <?php foreach($recentComics as $c) { ?>
          <tr>
            <td><a href="<?php echo $base; ?>/comic.php?id=<?php echo $c['id']; ?>" class="text-decoration-none fw-semibold" style="color:var(--ink)"><?php echo h($c['title']); ?></a></td>
            <td class="text-muted"><?php echo h($c['genre']); ?></td>
            <td class="text-muted"><?php echo h($c['owner']); ?></td>
          </tr>
        <?php } ?>
        </tbody>
      </table></div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>

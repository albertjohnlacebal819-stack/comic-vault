<?php
include 'connection.php';

$q = "";
$genre = "";
$status = "";
if(isset($_GET['q'])) { $q = trim($_GET['q']); }
if(isset($_GET['genre'])) { $genre = $_GET['genre']; }
if(isset($_GET['status'])) { $status = $_GET['status']; }

$conditions = array();
if($q != "") {
    $s = mysqli_real_escape_string($conn, $q);
    $conditions[] = "(comics.title LIKE '%$s%' OR comics.author LIKE '%$s%' OR comics.publisher LIKE '%$s%')";
}
if($genre != "") {
    $g = mysqli_real_escape_string($conn, $genre);
    $conditions[] = "comics.genre = '$g'";
}
if(isset($status_labels[$status])) {
    $conditions[] = "comics.status = '$status'";
}

$searching = count($conditions) > 0;
if($role != 'admin') {
    $conditions[] = "comics.user_id IN (SELECT id FROM users WHERE role = 'admin')";
}
$where = "";
if(count($conditions) > 0) {
    $where = "WHERE " . implode(" AND ", $conditions);
}

$limit = 12;
if($role == 'user') {
    $limit = 24;
}

if($role == 'admin') {
    $order = $searching ? "comics.title" : "comics.id DESC";
    $sql = "SELECT comics.*, COALESCE(comics.cover, src.cover) AS display_cover FROM comics LEFT JOIN comics src ON src.id = comics.source_id $where ORDER BY $order";
} else {
    $order = $searching ? "title" : "RAND() LIMIT $limit";
    $sql = "SELECT * FROM comics WHERE id IN (SELECT MIN(comics.id) FROM comics $where GROUP BY comics.title, comics.author, comics.issue_number) ORDER BY $order";
}

$result = mysqli_query($conn, $sql);
$comics = array();
while($row = mysqli_fetch_assoc($result)) {
    $comics[] = $row;
}

$genres = array();
if($role == 'admin') {
    $result = mysqli_query($conn, "SELECT DISTINCT genre FROM comics ORDER BY genre");
} else {
    $result = mysqli_query($conn, "SELECT DISTINCT genre FROM comics WHERE user_id IN (SELECT id FROM users WHERE role = 'admin') ORDER BY genre");
}
while($row = mysqli_fetch_assoc($result)) {
    $genres[] = $row['genre'];
}

$pageTitle = "Discover";
include 'includes/header.php';
?>
<div class="page-head">
  <div>
    <?php if($role == 'admin') { ?>
      <div class="eyebrow">Admin</div>
      <h1>Discover</h1>
      <p><?php echo $searching ? "Search results" : count($comics) . " comics in total. You can add, edit and delete any of them."; ?></p>
    <?php } elseif($role == 'user') { ?>
      <div class="eyebrow">Welcome back</div>
      <h1>Hello, <?php echo h($_SESSION['name']); ?></h1>
      <p><?php echo $searching ? "Search results" : "Here are " . count($comics) . " random picks. Use &ldquo;+ My Collection&rdquo; to save one to your own shelf."; ?></p>
    <?php } else { ?>
      <div class="eyebrow">The vault</div>
      <h1>Discover comics</h1>
      <p><?php echo $searching ? "Search results" : "A random pick from the vault. Log in to build your own collection."; ?></p>
    <?php } ?>
  </div>
  <div class="d-flex gap-2">
    <?php if($role == 'admin') { ?>
      <a href="<?php echo $base; ?>/form.php?from=discover" class="btn btn-accent">+ Add comic</a>
    <?php } elseif(!$searching) { ?>
      <a href="<?php echo $base; ?>/index.php" class="btn btn-line">&#8635; Shuffle</a>
    <?php } ?>
  </div>
</div>

<?php include 'includes/filters.php'; ?>
<?php
$manage = ($role == 'admin');
$addable = ($role == 'user');
include 'includes/comic_grid.php';
?>
<?php include 'includes/footer.php'; ?>

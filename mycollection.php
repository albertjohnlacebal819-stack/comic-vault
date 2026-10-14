<?php
include 'connection.php';

if(!$logged_in) {
    setMessage("Please log in to manage comics.", "warning");
    redirectTo("login.php");
}

$q = "";
$genre = "";
$status = "";
if(isset($_GET['q'])) { $q = trim($_GET['q']); }
if(isset($_GET['genre'])) { $genre = $_GET['genre']; }
if(isset($_GET['status'])) { $status = $_GET['status']; }

$sql = "SELECT comics.*, COALESCE(comics.cover, src.cover) AS display_cover FROM comics LEFT JOIN comics src ON src.id = comics.source_id WHERE comics.user_id = $my_id";
if($q != "") {
    $s = mysqli_real_escape_string($conn, $q);
    $sql = $sql . " AND (comics.title LIKE '%$s%' OR comics.author LIKE '%$s%' OR comics.publisher LIKE '%$s%')";
}
if($genre != "") {
    $g = mysqli_real_escape_string($conn, $genre);
    $sql = $sql . " AND comics.genre = '$g'";
}
if(isset($status_labels[$status])) {
    $sql = $sql . " AND comics.status = '$status'";
}
$sql = $sql . " ORDER BY comics.title";

$result = mysqli_query($conn, $sql);
$comics = array();
while($row = mysqli_fetch_assoc($result)) {
    $comics[] = $row;
}

$genres = array();
$result = mysqli_query($conn, "SELECT DISTINCT genre FROM comics WHERE user_id = $my_id ORDER BY genre");
while($row = mysqli_fetch_assoc($result)) {
    $genres[] = $row['genre'];
}

$pageTitle = "My Collection";
include 'includes/header.php';
?>
<div class="page-head">
  <div>
    <div class="eyebrow">Your shelf</div>
    <h1>My Collection</h1>
    <p><?php echo count($comics); ?> comic<?php echo (count($comics) == 1) ? "" : "s"; ?> shown</p>
  </div>
  <a href="<?php echo $base; ?>/form.php" class="btn btn-accent">+ Add comic</a>
</div>

<?php include 'includes/filters.php'; ?>
<?php
$manage = true;
$addable = false;
$suffix = "&from=my";
include 'includes/comic_grid.php';
?>
<?php include 'includes/footer.php'; ?>

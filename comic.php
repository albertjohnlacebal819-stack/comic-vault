<?php
include 'connection.php';

$id = 0;
if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
}

$sql = "SELECT comics.*, users.name AS owner, users.role AS owner_role, COALESCE(comics.cover, src.cover) AS display_cover FROM comics JOIN users ON users.id = comics.user_id LEFT JOIN comics src ON src.id = comics.source_id WHERE comics.id = $id";
$result = mysqli_query($conn, $sql);
$c = mysqli_fetch_assoc($result);

if(!$c || ($c['owner_role'] != 'admin' && $role != 'admin' && $c['user_id'] != $my_id)) {
    http_response_code(404);
    die("Comic not found.");
}

$fromMy = (isset($_GET['from']) && $_GET['from'] == 'my' && $logged_in);

$pageTitle = $c['title'];
include 'includes/header.php';
?>
<a href="<?php echo $base; ?>/<?php echo $fromMy ? 'mycollection.php' : 'index.php'; ?>" class="back-link">&larr; Back to <?php echo $fromMy ? 'My Collection' : 'Discover'; ?></a>
<div class="row g-4 g-lg-5 mt-1">
  <div class="col-12 col-md-4 col-lg-3"><div class="detail-cover"><?php echo showCover($c); ?></div></div>
  <div class="col-12 col-md-8 col-lg-9">
    <div class="eyebrow"><?php echo h($c['genre']); ?></div>
    <h1 class="detail-title"><?php echo h($c['title']); ?></h1>
    <p class="text-muted mb-2">by <?php echo h($c['author']); ?></p>
    <span class="status status-<?php echo $c['status']; ?> small"><?php echo $status_labels[$c['status']]; ?></span>
    <span class="text-muted small ms-2">&middot; in <?php echo h($c['owner']); ?>'s collection</span>

    <dl class="facts">
      <dt>Publisher</dt><dd><?php echo $c['publisher'] ? h($c['publisher']) : "-"; ?></dd>
      <dt>Issue</dt><dd><?php echo $c['issue_number'] ? "#" . $c['issue_number'] : "-"; ?></dd>
      <dt>Year</dt><dd><?php echo $c['year'] ? $c['year'] : "-"; ?></dd>
      <dt>Price</dt><dd><?php echo ($c['price'] !== null) ? "$" . number_format($c['price'], 2) : "-"; ?></dd>
    </dl>

    <?php if($c['description']) { ?>
      <p style="max-width:42rem;line-height:1.7"><?php echo nl2br(h($c['description'])); ?></p>
    <?php } ?>
  </div>
</div>
<?php include 'includes/footer.php'; ?>

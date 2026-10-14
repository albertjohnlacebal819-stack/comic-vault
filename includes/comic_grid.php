<?php
if(!isset($suffix)) {
    $suffix = "";
}
if(count($comics) == 0) {
    echo '<div class="empty">No comics found.</div>';
}
?>
<div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-6 g-4">
  <?php foreach($comics as $c) { ?>
    <div class="col">
      <div class="comic-card h-100">
        <a href="<?php echo $base; ?>/comic.php?id=<?php echo $c['id'] . $suffix; ?>" class="text-decoration-none"><?php echo showCover($c); ?></a>
        <div class="comic-body">
          <a href="<?php echo $base; ?>/comic.php?id=<?php echo $c['id'] . $suffix; ?>" class="comic-title"><?php echo h($c['title']); ?></a>
          <div class="comic-meta">
            <?php echo h($c['author']); ?>
            <?php if($c['issue_number']) { echo "&middot; #" . $c['issue_number']; } ?>
          </div>
          <div class="comic-tags">
            <span><?php echo h($c['genre']); ?></span>
            <span class="status status-<?php echo $c['status']; ?>"><?php echo $status_labels[$c['status']]; ?></span>
          </div>

          <?php if($manage) { ?>
          <div class="comic-actions">
            <a href="<?php echo $base; ?>/form.php?id=<?php echo $c['id'] . $suffix; ?>" class="btn btn-sm btn-line">Edit</a>
            <form method="POST" action="<?php echo $base; ?>/delete.php" class="js-confirm" data-msg="Delete this comic?">
              <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
              <input type="hidden" name="from" value="<?php echo ($suffix != '') ? 'my' : ''; ?>">
              <button class="btn btn-sm btn-line-danger">Delete</button>
            </form>
          </div>
          <?php } elseif($addable && $c['user_id'] != $my_id) { ?>
          <div class="comic-actions">
            <form method="POST" action="<?php echo $base; ?>/add_to_collection.php">
              <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
              <button class="btn btn-sm btn-line">+ My Collection</button>
            </form>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  <?php } ?>
</div>

<form method="GET" class="filters row g-2 mb-4">
  <div class="col-12 col-md-5">
    <input type="search" name="q" class="form-control" placeholder="Search title, author or publisher" value="<?php echo h($q); ?>">
  </div>
  <div class="col-6 col-md-3">
    <select name="genre" class="form-select">
      <option value="">All genres</option>
      <?php foreach($genres as $g) { ?>
        <option value="<?php echo h($g); ?>" <?php if($genre == $g) { echo "selected"; } ?>><?php echo h($g); ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="col-6 col-md-2">
    <select name="status" class="form-select">
      <option value="">Any status</option>
      <?php foreach($status_labels as $key => $label) { ?>
        <option value="<?php echo $key; ?>" <?php if($status == $key) { echo "selected"; } ?>><?php echo $label; ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="col-12 col-md-2 d-grid"><button class="btn btn-ink">Search</button></div>
</form>

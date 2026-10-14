<?php
include 'connection.php';

if(!$logged_in) {
    setMessage("Please log in to manage comics.", "warning");
    redirectTo("login.php");
}

$id = 0;
if(isset($_GET['id'])) { $id = (int)$_GET['id']; }
if(isset($_POST['id'])) { $id = (int)$_POST['id']; }

$from = "";
if(isset($_GET['from'])) { $from = $_GET['from']; }
if(isset($_POST['from'])) { $from = $_POST['from']; }
if($from != "my" && $from != "discover") { $from = ""; }

if($from == "my") {
    $backTo = "mycollection.php";
} elseif($from == "discover" || $id > 0) {
    $backTo = "index.php";
} else {
    $backTo = "mycollection.php";
}

$c = array("title" => "", "author" => "", "publisher" => "", "genre" => "Superhero", "issue_number" => "",
           "year" => "", "price" => "", "status" => "owned", "cover" => null, "description" => "");

if($id > 0) {
    $result = mysqli_query($conn, "SELECT * FROM comics WHERE id = $id");
    $c = mysqli_fetch_assoc($result);
    if(!$c) {
        http_response_code(404);
        die("Comic not found.");
    }
    if($role != 'admin' && $c['user_id'] != $my_id) {
        http_response_code(403);
        die("You can only edit your own comics.");
    }
}

$error = "";

if(isset($_POST['save'])) {
    $c['title'] = trim($_POST['title']);
    $c['author'] = trim($_POST['author']);
    $c['publisher'] = trim($_POST['publisher']);
    $c['genre'] = trim($_POST['genre']);
    $c['description'] = trim($_POST['description']);
    $c['status'] = $_POST['status'];
    $c['issue_number'] = trim($_POST['issue_number']);
    $c['year'] = trim($_POST['year']);
    $c['price'] = trim($_POST['price']);

    if($c['title'] == "" || $c['author'] == "") {
        $error = "Title and author are required.";
    } elseif(!isset($status_labels[$c['status']])) {
        $error = "Invalid status.";
    } elseif($c['issue_number'] != "" && (!ctype_digit($c['issue_number']) || $c['issue_number'] > 99999)) {
        $error = "Issue number must be a whole number between 0 and 99999.";
    } elseif($c['year'] != "" && (!ctype_digit($c['year']) || $c['year'] < 1901 || $c['year'] > 2155)) {
        $error = "Year must be between 1901 and 2155.";
    } elseif($c['price'] != "" && (!is_numeric($c['price']) || $c['price'] < 0 || $c['price'] > 999999.99)) {
        $error = "Price must be a number between 0 and 999999.99.";
    }

    $cover = $c['cover'];
    if($error == "" && !empty($_FILES['cover']['name']) && $_FILES['cover']['error'] == UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
        $allowed = array("jpg", "jpeg", "png", "gif", "webp");

        if(!in_array($ext, $allowed) || !@getimagesize($_FILES['cover']['tmp_name'])) {
            $error = "Cover must be a JPG, PNG, GIF or WEBP image.";
        } elseif($_FILES['cover']['size'] > 2 * 1024 * 1024) {
            $error = "Cover must be 2 MB or smaller.";
        } else {
            $new_name = uniqid() . "." . $ext;
            if(move_uploaded_file($_FILES['cover']['tmp_name'], __DIR__ . "/uploads/" . $new_name)) {
                if($cover) {
                    @unlink(__DIR__ . "/uploads/" . $cover);
                }
                $cover = $new_name;
            } else {
                $error = "Could not save the cover. Check that the uploads folder is writable.";
            }
        }
    }

    if($error == "") {
        $title = mysqli_real_escape_string($conn, $c['title']);
        $author = mysqli_real_escape_string($conn, $c['author']);
        $genre = ($c['genre'] == "") ? "Other" : mysqli_real_escape_string($conn, $c['genre']);
        $status = $c['status'];
        $publisher = ($c['publisher'] == "") ? "NULL" : "'" . mysqli_real_escape_string($conn, $c['publisher']) . "'";
        $description = ($c['description'] == "") ? "NULL" : "'" . mysqli_real_escape_string($conn, $c['description']) . "'";
        $issue = ($c['issue_number'] == "") ? "NULL" : (int)$c['issue_number'];
        $year = ($c['year'] == "") ? "NULL" : (int)$c['year'];
        $price = ($c['price'] == "") ? "NULL" : (float)$c['price'];
        $cover_sql = $cover ? "'" . $cover . "'" : "NULL";

        if($id > 0) {
            $sql = "UPDATE comics SET title='$title', author='$author', publisher=$publisher, genre='$genre',
                    issue_number=$issue, year=$year, price=$price, status='$status', cover=$cover_sql,
                    description=$description WHERE id=$id";
            mysqli_query($conn, $sql);
            setMessage("Comic updated.", "success");
        } else {
            $sql = "INSERT INTO comics(user_id, title, author, publisher, genre, issue_number, year, price, status, cover, description)
                    VALUES($my_id, '$title', '$author', $publisher, '$genre', $issue, $year, $price, '$status', $cover_sql, $description)";
            mysqli_query($conn, $sql);
            setMessage("Comic added to your collection.", "success");
        }
        redirectTo($backTo);
    }
}

$pageTitle = ($id > 0) ? "Edit Comic" : "Add Comic";
include 'includes/header.php';
?>
<div class="col-lg-8 mx-auto">
  <a href="<?php echo $base; ?>/<?php echo $backTo; ?>" class="back-link">&larr; Back</a>
  <div class="panel panel-pad mt-3">
    <h1 class="h2 mb-4"><?php echo $pageTitle; ?></h1>
    <?php if($error != "") { ?><div class="alert flash flash-danger"><?php echo h($error); ?></div><?php } ?>

    <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
      <input type="hidden" name="id" value="<?php echo $id; ?>">
      <input type="hidden" name="from" value="<?php echo $from; ?>">
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label">Title *</label>
          <input type="text" name="title" class="form-control" maxlength="150" value="<?php echo h($c['title']); ?>" required>
          <div class="invalid-feedback">Title is required.</div>
        </div>
        <div class="col-md-4">
          <label class="form-label">Issue #</label>
          <input type="number" name="issue_number" class="form-control" min="0" max="99999" value="<?php echo h($c['issue_number']); ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Author *</label>
          <input type="text" name="author" class="form-control" maxlength="100" value="<?php echo h($c['author']); ?>" required>
          <div class="invalid-feedback">Author is required.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Publisher</label>
          <input type="text" name="publisher" class="form-control" maxlength="100" value="<?php echo h($c['publisher']); ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Genre</label>
          <input type="text" name="genre" class="form-control" list="genres" maxlength="50" value="<?php echo h($c['genre']); ?>">
          <datalist id="genres">
            <option value="Superhero"><option value="Fantasy"><option value="Sci-Fi"><option value="Horror">
            <option value="Manga"><option value="Comedy"><option value="Mystery"><option value="Other">
          </datalist>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label">Year</label>
          <input type="number" name="year" class="form-control" min="1901" max="2155" value="<?php echo h($c['year']); ?>">
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Price ($)</label>
          <input type="number" name="price" class="form-control" min="0" max="999999.99" step="0.01" value="<?php echo h($c['price']); ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <?php foreach($status_labels as $key => $label) { ?>
              <option value="<?php echo $key; ?>" <?php if($c['status'] == $key) { echo "selected"; } ?>><?php echo $label; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3"><?php echo h($c['description']); ?></textarea>
        </div>
        <div class="col-12">
          <label class="form-label">Cover image <span class="text-muted">(optional, max 2 MB)</span></label>
          <input type="file" name="cover" id="coverInput" class="form-control" accept="image/*">
          <div class="mt-2 d-flex align-items-end gap-3">
            <?php if($c['cover']) { ?>
              <img src="<?php echo $base; ?>/uploads/<?php echo h($c['cover']); ?>" class="preview" alt="Current cover">
            <?php } ?>
            <img id="coverPreview" class="preview d-none" alt="New cover preview">
          </div>
        </div>
      </div>
      <div class="mt-4 d-flex gap-2">
        <button type="submit" name="save" class="btn btn-accent"><?php echo ($id > 0) ? "Save changes" : "Add comic"; ?></button>
        <a href="<?php echo $base; ?>/<?php echo $backTo; ?>" class="btn btn-line">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php include 'includes/footer.php'; ?>

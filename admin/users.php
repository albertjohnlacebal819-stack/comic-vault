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

if(isset($_POST['user_id'])) {
    $uid = (int)$_POST['user_id'];

    if($uid == $my_id) {
        setMessage("You cannot change or delete your own account.", "warning");
    } elseif(isset($_POST['action']) && $_POST['action'] == 'delete') {
        $result = mysqli_query($conn, "SELECT cover FROM comics WHERE user_id = $uid AND cover IS NOT NULL");
        while($row = mysqli_fetch_assoc($result)) {
            @unlink(__DIR__ . "/../uploads/" . $row['cover']);
        }
        mysqli_query($conn, "DELETE FROM users WHERE id = $uid");
        setMessage("User and their comics deleted.", "success");
    } else {
        $new_role = "user";
        if(isset($_POST['role']) && $_POST['role'] == 'admin') {
            $new_role = "admin";
        }
        mysqli_query($conn, "UPDATE users SET role = '$new_role' WHERE id = $uid");
        setMessage("Role updated.", "success");
    }
    redirectTo("admin/users.php");
}

$users = array();
$sql = "SELECT users.id, users.name, users.email, users.role, users.created_at, users.last_login, COUNT(comics.id) AS comic_count
        FROM users LEFT JOIN comics ON comics.user_id = users.id
        GROUP BY users.id ORDER BY users.created_at, users.id";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

$pageTitle = "Users";
include '../includes/header.php';
?>
<div class="page-head">
  <div>
    <div class="eyebrow">Admin</div>
    <h1>Users</h1>
    <p><?php echo count($users); ?> registered account<?php echo (count($users) == 1) ? "" : "s"; ?></p>
  </div>
</div>

<div class="panel">
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr><th>Name</th><th>Email</th><th>Comics</th><th>Joined</th><th>Last login</th><th>Role</th><th class="text-end">Actions</th></tr>
      </thead>
      <tbody>
      <?php foreach($users as $u) { ?>
        <?php $self = ($u['id'] == $my_id); ?>
        <tr>
          <td class="fw-semibold"><?php echo h($u['name']); ?><?php if($self) { echo ' <span class="chip chip-you ms-1">you</span>'; } ?></td>
          <td class="text-muted"><?php echo h($u['email']); ?></td>
          <td><?php echo $u['comic_count']; ?></td>
          <td class="text-nowrap small text-muted"><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
          <td class="text-nowrap small text-muted"><?php echo $u['last_login'] ? date('M j, Y g:i A', strtotime($u['last_login'])) : "Never"; ?></td>
          <td>
            <form method="POST" class="d-flex gap-1">
              <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
              <select name="role" class="form-select form-select-sm w-auto" <?php if($self) { echo "disabled"; } ?>>
                <option value="user" <?php if($u['role'] == 'user') { echo "selected"; } ?>>user</option>
                <option value="admin" <?php if($u['role'] == 'admin') { echo "selected"; } ?>>admin</option>
              </select>
              <button class="btn btn-sm btn-line" <?php if($self) { echo "disabled"; } ?>>Save</button>
            </form>
          </td>
          <td class="text-end">
            <form method="POST" class="js-confirm" data-msg="Delete this user and all their comics?">
              <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
              <input type="hidden" name="action" value="delete">
              <button class="btn btn-sm btn-line-danger" <?php if($self) { echo "disabled"; } ?>>Delete</button>
            </form>
          </td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>

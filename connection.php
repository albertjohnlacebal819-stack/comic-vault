<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "comics";
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), "/");
if(basename($base) == "admin") {
    $base = dirname($base);
}
if($base == "/" || $base == "\\") {
    $base = "";
}

$conn = mysqli_connect($servername, $username, $password, $database);

if(!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

$logged_in = isset($_SESSION['user_id']);
$my_id = 0;
$role = "guest";
if($logged_in) {
    $my_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
}

$status_labels = array("owned" => "Owned", "wishlist" => "Wishlist", "reading" => "Reading");

function h($text) {
    return htmlspecialchars((string)$text);
}

function redirectTo($page) {
    global $base;
    header("Location: " . $base . "/" . $page);
    exit;
}

function setMessage($text, $type) {
    $_SESSION['msg'] = $text;
    $_SESSION['msg_type'] = $type;
}

function showMessage() {
    if(isset($_SESSION['msg'])) {
        echo '<div class="alert flash flash-' . $_SESSION['msg_type'] . ' alert-dismissible fade show">';
        echo h($_SESSION['msg']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['msg']);
        unset($_SESSION['msg_type']);
    }
}

function activeClass($file) {
    if(basename($_SERVER['SCRIPT_NAME']) == $file) {
        return " active";
    }
    return "";
}

function getCount($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_row($result);
    return $row[0];
}

function showCover($comic) {
    global $base;
    $file = $comic['cover'];
    if(isset($comic['display_cover'])) {
        $file = $comic['display_cover'];
    }
    if($file) {
        return '<img src="' . $base . '/uploads/' . h($file) . '" class="cover" alt="Cover of ' . h($comic['title']) . '" loading="lazy">';
    }
    $hue = abs(crc32($comic['title'])) % 360;
    $letter = strtoupper(substr($comic['title'], 0, 1));
    return '<div class="cover cover-empty" style="background:hsl(' . $hue . ' 36% 90%);color:hsl(' . $hue . ' 32% 40%)"><span>' . h($letter) . '</span></div>';
}
?>

<?php
include 'connection.php';

if(!$logged_in) {
    setMessage("Please log in to manage comics.", "warning");
    redirectTo("login.php");
}

if(isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    $result = mysqli_query($conn, "SELECT * FROM comics WHERE id = $id");
    $row = mysqli_fetch_assoc($result);

    if($row && ($role == 'admin' || $row['user_id'] == $my_id)) {
        if($row['cover']) {
            $copies = mysqli_query($conn, "SELECT id FROM comics WHERE source_id = $id AND cover IS NULL");
            while($copy = mysqli_fetch_assoc($copies)) {
                $ext = pathinfo($row['cover'], PATHINFO_EXTENSION);
                $copy_name = uniqid() . "." . $ext;
                copy(__DIR__ . "/uploads/" . $row['cover'], __DIR__ . "/uploads/" . $copy_name);
                mysqli_query($conn, "UPDATE comics SET cover = '$copy_name' WHERE id = " . (int)$copy['id']);
            }
            @unlink(__DIR__ . "/uploads/" . $row['cover']);
        }
        mysqli_query($conn, "DELETE FROM comics WHERE id = $id");
        setMessage("Comic deleted.", "success");
    } else {
        setMessage("You cannot delete that comic.", "danger");
    }

    if(isset($_POST['from']) && $_POST['from'] == 'my') {
        redirectTo("mycollection.php");
    }
}
redirectTo("index.php");

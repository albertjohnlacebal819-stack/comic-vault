<?php
include 'connection.php';

if(!$logged_in) {
    setMessage("Please log in to manage comics.", "warning");
    redirectTo("login.php");
}
if($role == 'admin') {
    setMessage("Admins manage Discover directly.", "warning");
    redirectTo("index.php");
}

if(isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $result = mysqli_query($conn, "SELECT comics.*, users.role AS owner_role FROM comics JOIN users ON users.id = comics.user_id WHERE comics.id = $id");
    $c = mysqli_fetch_assoc($result);

    if(!$c || $c['owner_role'] != 'admin') {
        setMessage("Comic not found.", "danger");
    } elseif($c['user_id'] == $my_id) {
        setMessage("That comic is already yours.", "warning");
    } else {
        $title = mysqli_real_escape_string($conn, $c['title']);
        $author = mysqli_real_escape_string($conn, $c['author']);
        $issue = ($c['issue_number'] === null) ? "NULL" : (int)$c['issue_number'];

        $check = mysqli_query($conn, "SELECT id FROM comics WHERE user_id = $my_id AND title = '$title' AND author = '$author' AND issue_number <=> $issue");

        if(mysqli_num_rows($check) > 0) {
            setMessage('"' . $c['title'] . '" is already in your collection.', "warning");
        } else {
            $publisher = ($c['publisher'] === null) ? "NULL" : "'" . mysqli_real_escape_string($conn, $c['publisher']) . "'";
            $genre = mysqli_real_escape_string($conn, $c['genre']);
            $year = ($c['year'] === null) ? "NULL" : (int)$c['year'];
            $price = ($c['price'] === null) ? "NULL" : (float)$c['price'];
            $description = ($c['description'] === null) ? "NULL" : "'" . mysqli_real_escape_string($conn, $c['description']) . "'";

            $sql = "INSERT INTO comics(user_id, source_id, title, author, publisher, genre, issue_number, year, price, status, description)
                    VALUES($my_id, $id, '$title', '$author', $publisher, '$genre', $issue, $year, $price, 'wishlist', $description)";
            mysqli_query($conn, $sql);

            setMessage('"' . $c['title'] . '" was added to My Collection (as Wishlist).', "success");
        }
    }
}
redirectTo("index.php");

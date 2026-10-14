<?php
include 'connection.php';

unset($_SESSION['user_id']);
unset($_SESSION['name']);
unset($_SESSION['role']);

setMessage("You have been logged out.", "success");
redirectTo("index.php");

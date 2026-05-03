<?php
include_once "../config.php";

if (isset($_SESSION['user_id'])) {
    $conn->query("UPDATE users SET status = 'offline' WHERE id = " . $_SESSION['user_id']);
}

// თუ გვინდა მოწყობილობამ "დაგვივიწყოს" (მაგ. სხვისი ტელეფონით შევედით)
if (isset($_GET['clear_cookie'])) {
    setcookie("remember_user", "", time() - 3600, "/");
}

session_destroy();
header("Location: login.php");
exit();
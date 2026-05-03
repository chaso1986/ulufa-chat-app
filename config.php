<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db   = "chat_app";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ქართული სიმბოლოების სწორად ასახვისთვის
$conn->set_charset("utf8mb4");

// მომხმარებლის ონლაინ სტატუსის განახლების ფუნქცია
function updateLastSeen($user_id, $conn) {
    $stmt = $conn->prepare("UPDATE users SET last_seen = NOW(), status = 'online' WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}
?>
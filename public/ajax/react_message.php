<?php
include_once "../config.php";

if (!isset($_SESSION['user_id']) || !isset($_POST['message_id'])) {
    exit("error");
}

$user_id = $_SESSION['user_id'];
$message_id = $_POST['message_id'];
$reaction = $_POST['reaction']; // ჩვენს შემთხვევაში 'heart'

// ვამოწმებთ, უკვე ხომ არ აქვს ამ იუზერს რეაქცია ამ მესიჯზე[cite: 7]
$check = $conn->prepare("SELECT id FROM message_reactions WHERE message_id = ? AND user_id = ?");
$check->bind_param("ii", $message_id, $user_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    // თუ უკვე დაგულებულია, წავშალოთ (Toggle ფუნქცია)[cite: 7]
    $del = $conn->prepare("DELETE FROM message_reactions WHERE message_id = ? AND user_id = ?");
    $del->bind_param("ii", $message_id, $user_id);
    $del->execute();
    echo "removed";
} else {
    // თუ არა - დავამატოთ[cite: 7]
    $ins = $conn->prepare("INSERT INTO message_reactions (message_id, user_id, reaction_type) VALUES (?, ?, ?)");
    $ins->bind_param("iis", $message_id, $user_id, $reaction);
    $ins->execute();
    echo "added";
}
?>
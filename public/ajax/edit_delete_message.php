<?php
include_once "../config.php";

if (!isset($_SESSION['user_id'])) exit("Unauthorized");

$user_id = $_SESSION['user_id'];
$action = $_POST['action'];
$message_id = $_POST['message_id'];

// ვამოწმებთ, რომ მომხმარებელი მხოლოდ საკუთარ შეტყობინებას შლის/არედაქტირებს
$check = $conn->prepare("SELECT sender_id FROM messages WHERE id = ?");
$check->bind_param("i", $message_id);
$check->execute();
$res = $check->get_result()->fetch_assoc();

if (!$res || $res['sender_id'] != $user_id) exit("Access Denied");

if ($action == 'delete') {
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
} elseif ($action == 'edit') {
    $new_msg = htmlspecialchars($_POST['new_message']);
    $stmt = $conn->prepare("UPDATE messages SET message = ?, is_edited = 1 WHERE id = ?");
    $stmt->bind_param("si", $new_msg, $message_id);
    $stmt->execute();
}
?>
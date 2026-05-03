<?php
include_once "../config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = htmlspecialchars($_POST['message']);
    $file_path = null;

    // ფაილის ატვირთვა (თუ არის)
    if (!empty($_FILES['file']['name'])) {
        $file_path = time() . '_' . $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], "../assets/uploads/" . $file_path);
    }

    if (!empty($message) || !empty($file_path)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, file_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $sender_id, $receiver_id, $message, $file_path);
        $stmt->execute();
    }
}
?>
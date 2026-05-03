<?php
include_once "../config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_COOKIE['remember_user'])) {
    $input_pin = $_POST['pin'];
    $user_id = $_COOKIE['remember_user'];

    // ბაზიდან ვიღებთ მომხმარებლის ჰეშირებულ პინს
    $stmt = $conn->prepare("SELECT id, username, pin FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($input_pin, $user['pin'])) {
        // თუ პინი სწორია, ვქმნით სესიას
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // სტატუსის განახლება
        $conn->query("UPDATE users SET status = 'online' WHERE id = " . $user['id']);
        
        header("Location: ../chat/index.php");
        exit();
    } else {
        // თუ არასწორია, ვაბრუნებთ პინის გვერდზე შეცდომით
        header("Location: pin_login.php?error=1");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
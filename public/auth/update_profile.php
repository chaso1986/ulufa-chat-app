<?php
include_once "../config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    // 1. სახელის განახლება
    $stmt = $conn->prepare("UPDATE users SET username = ?, first_name = ?, last_name = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $first_name, $last_name, $user_id);
    $stmt->execute();
    $_SESSION['username'] = $username;

    // 2. პაროლის შეცვლა (თუ შევსებულია)
    if (!empty($_POST['new_password'])) {
        $pass = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $pass, $user_id);
        $stmt->execute();
    }

    // 3. PIN-ის შეცვლა (თუ შევსებულია)
    if (!empty($_POST['new_pin'])) {
        $pin = password_hash($_POST['new_pin'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET pin = ? WHERE id = ?");
        $stmt->bind_param("si", $pin, $user_id);
        $stmt->execute();
    }

    // 4. ავატარის ატვირთვა
    if (!empty($_FILES['avatar']['name'])) {
        $file_name = time() . "_" . $_FILES['avatar']['name'];
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], "../assets/uploads/" . $file_name)) {
            $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->bind_param("si", $file_name, $user_id);
            $stmt->execute();
        }
    }

    header("Location: profile_edit.php?success=1");
}
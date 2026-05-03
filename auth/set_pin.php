<?php
include_once "../config.php";

// თუ მომხმარებელი არ არის ავტორიზებული, არ დავუშვათ პინის დაყენება
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pin = password_hash($_POST['pin'], PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users SET pin = ? WHERE id = ?");
    $stmt->bind_param("si", $pin, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        // HttpOnly ქუქი მეტი უსაფრთხოებისთვის
        setcookie("remember_user", $_SESSION['user_id'], time() + (86400 * 30), "/", "", false, true); 
        header("Location: ../chat/index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIN კოდის დაყენება</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .pin-card { border: none; border-radius: 15px; }
        .pin-input { letter-spacing: 15px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-11 col-sm-8 col-md-5 col-lg-4 text-center">
                <div class="card pin-card shadow p-4">
                    <div class="mb-3 text-primary">
                        <i class="fas fa-lock-open fa-3x"></i>
                    </div>
                    <h4 class="fw-bold mb-3">დააყენეთ PIN კოდი</h4>
                    <p class="text-muted small mb-4">შემდეგში შესვლისას მხოლოდ ეს კოდი დაგჭირდებათ</p>
                    
                    <form method="POST">
                        <input type="number" name="pin" 
                               class="form-control form-control-lg text-center pin-input mb-4" 
                               placeholder="0000"
                               inputmode="numeric"
                               pattern="\d*"
                               oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);"
                               required autofocus>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">შენახვა</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
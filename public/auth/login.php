<?php
include_once "../config.php";

// თუ მომხმარებელი უკვე "დამახსოვრებულია", პირდაპირ PIN-ზე გადავიყვანოთ
if (isset($_COOKIE['remember_user']) && !isset($_SESSION['user_id'])) {
    header("Location: pin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // სტატუსის განახლება
        $conn->query("UPDATE users SET status = 'online' WHERE id = " . $user['id']);
        
        header("Location: ../chat/index.php");
        exit();
    } else {
        $error = "მონაცემები არასწორია!";
    }
}
?>

<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- აუცილებელია რესპონსიულობისთვის -->
    <title>შესვლა</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .login-card { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- vh-100 და align-items-center ვერტიკალურად აცენტრებს ფორმას -->
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-11 col-sm-8 col-md-6 col-lg-4"> 
                <div class="card login-card p-4">
                    <h3 class="text-center fw-bold mb-4">შესვლა</h3>
                    <?php if(isset($error)) echo "<div class='alert alert-danger py-2 small'>$error</div>"; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <input type="text" name="phone" class="form-control py-2" placeholder="მომხმარებელი" required>
                        </div>
                        <div class="mb-4">
                            <input type="password" name="password" class="form-control py-2" placeholder="პაროლი" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">შესვლა</button>
                    </form>
                    <p class="mt-4 text-center mb-0 text-muted">
                        არ გაქვთ ანგარიში? <a href="register.php" class="text-decoration-none">რეგისტრაცია</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
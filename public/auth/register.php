<?php
include_once "../config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $first_name = $_POST['first_name']; // დაემატა სახელი
    $last_name = $_POST['last_name'];   // დაემატა გვარი
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    if (!preg_match("/^\+995\d{9}$/", $phone)) {
        $error = "ტელეფონის ნომერი უნდა იყოს ფორმატში: +995XXXXXXXXX";
    } else {
        $avatar = "default.png";
        if (!empty($_FILES['avatar']['name'])) {
            $avatar = time() . '_' . basename($_FILES['avatar']['name']);
            move_uploaded_file($_FILES['avatar']['tmp_name'], "../assets/uploads/" . $avatar);
        }

        try {
            // INSERT ბრძანებაში დაემატა first_name და last_name
            $stmt = $conn->prepare("INSERT INTO users (username, first_name, last_name, phone, password, avatar) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $username, $first_name, $last_name, $phone, $password, $avatar);
            
            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error = "ეს ნომერი ან მომხმარებლის სახელი უკვე დაკავებულია!";
            } else {
                $error = "დაფიქსირდა შეცდომა, სცადეთ თავიდან.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>რეგისტრაცია</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .register-card { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <!-- col-11 ნიშნავს, რომ მობილურზე ეკრანის 90%-ს დაიკავებს -->
            <div class="col-11 col-sm-8 col-md-6 col-lg-4">
                <div class="card register-card p-4">
                    <h3 class="text-center fw-bold mb-4">რეგისტრაცია</h3>
                    <?php if(isset($error)) echo "<div class='alert alert-danger py-2 small'>$error</div>"; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
    <!-- მომხმარებლის სახელი -->
    <input type="text" name="username" class="form-control mb-3 py-2" placeholder="მომხმარებლის სახელი" required>

    <!-- სახელი და გვარი ერთ ხაზზე -->
    <div class="row g-2 mb-3">
        <div class="col-6">
            <input type="text" name="first_name" class="form-control py-2" placeholder="სახელი" required>
        </div>
        <div class="col-6">
            <input type="text" name="last_name" class="form-control py-2" placeholder="გვარი" required>
        </div>
    </div>

    <input type="text" name="phone" class="form-control mb-3 py-2" placeholder="+995XXXXXXXXX" required>
    <input type="password" name="password" class="form-control mb-3 py-2" placeholder="პაროლი" required>
    
    <div class="mb-3">
        <label class="form-label small text-muted">პროფილის სურათი</label>
        <input type="file" name="avatar" class="form-control">
    </div>
    
    <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm">რეგისტრაცია</button>
</form>
                    <p class="mt-4 text-center mb-0 text-muted">
                        უკვე გაქვთ ანგარიში? <a href="login.php" class="text-decoration-none">შესვლა</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
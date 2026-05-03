<?php
include_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, first_name, last_name, avatar FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>პროფილის რედაქტირება</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background-color: #f0f2f5; }
        .profile-card { border: none; border-radius: 15px; }
        .avatar-wrapper { position: relative; display: inline-block; }
        .change-photo-btn {
            position: absolute; bottom: 5px; right: 5px;
            background: #2563eb; color: white; border-radius: 50%;
            width: 35px; height: 35px; display: flex; align-items: center;
            justify-content: center; border: 3px solid white; cursor: pointer;
        }
        .alert { border: none; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-5">
            
            <div class="d-flex align-items-center mb-4">
                <a href="../chat/index.php" class="btn btn-light rounded-circle shadow-sm me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h4 class="m-0 fw-bold">პროფილი</h4>
            </div>

            <!-- შეტყობინებების ბლოკი[cite: 5] -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4 rounded-pill px-4 shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i> მონაცემები განახლდა!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-pill px-4 shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> მოხდა შეცდომა!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card profile-card shadow-sm p-4">
                <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                    
                    <div class="text-center mb-4">
                        <div class="avatar-wrapper">
                            <img id="avatar-preview" src="../assets/uploads/<?= $user['avatar'] ?>" 
                                 class="rounded-circle shadow-sm border" 
                                 style="width: 120px; height: 120px; object-fit: cover;">
                            <label for="avatar-input" class="change-photo-btn shadow">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="avatar-input" name="avatar" hidden accept="image/*" onchange="previewAvatar(this)">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">მომხმარებლის სახელი</label>
                        <input type="text" name="username" class="form-control py-2 rounded-3" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold text-muted">სახელი</label>
                            <input type="text" name="first_name" class="form-control py-2 rounded-3" 
                                   value="<?= htmlspecialchars($user['first_name']) ?>" placeholder="სახელი">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold text-muted">გვარი</label>
                            <input type="text" name="last_name" class="form-control py-2 rounded-3" 
                                   value="<?= htmlspecialchars($user['last_name']) ?>" placeholder="გვარი">
                        </div>
                    </div>

                    <hr class="my-4 text-muted opacity-25">

                    <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-shield-alt me-2"></i> უსაფრთხოება</h6>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted">ახალი პაროლი</label>
                        <input type="password" name="new_password" class="form-control py-2 rounded-3" placeholder="••••••••">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-muted">ახალი PIN კოდი (4 ციფრი)</label>
                        <input type="number" name="new_pin" class="form-control py-2 rounded-3" placeholder="0000" 
                               inputmode="numeric" pattern="\d*" oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow">
                        ცვლილებების შენახვა
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap-ის JS აუცილებელია Alert-ის ფუნქციონირებისთვის[cite: 5] -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// შეტყობინების ავტომატური გაქრობა 3 წამში
setTimeout(function() {
    let alertElement = document.querySelector('.alert');
    if (alertElement) {
        let bsAlert = new bootstrap.Alert(alertElement);
        bsAlert.close();
    }
}, 3000);
</script>

</body>
</html>
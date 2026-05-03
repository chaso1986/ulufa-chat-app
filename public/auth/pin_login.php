<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIN შესვლა</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .pin-card { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        /* ციფრებს შორის დაშორება მესენჯერის სტილში */
        .pin-input { 
            letter-spacing: 10px; 
            font-weight: bold; 
            border-radius: 12px;
            border: 2px solid #dee2e6;
        }
        .pin-input:focus {
            border-color: #0d6efd;
            box-shadow: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- vh-100 აცენტრებს ფორმას ვერტიკალურად მთელ ეკრანზე -->
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-11 col-sm-8 col-md-5 col-lg-4">
                <div class="card pin-card p-4 text-center">
                    <div class="mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="fas fa-key fa-2x"></i>
                        </div>
                    </div>
                    
                    <h4 class="fw-bold mb-2">შეიყვანეთ PIN</h4>
                    <p class="text-muted small mb-4">უსაფრთხო შესვლისთვის</p>

                    <?php if(isset($_GET['error'])): ?>
                        <div class="alert alert-danger py-2 small">არასწორი PIN კოდი!</div>
                    <?php endif; ?>

                    <form method="POST" action="verify_pin.php">
                        <!-- pattern="\d*" და inputmode="numeric" მობილურზე მხოლოდ ციფრების კლავიატურას ხსნის -->
                        <input type="number" name="pin" 
                               pattern="\d*" inputmode="numeric" 
                               class="form-control form-control-lg text-center pin-input fs-1 mb-4" 
                               placeholder="••••"
                               oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);"
                               required autofocus>
                        
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-3 mb-3 rounded-pill">
                            შესვლა
                        </button>
                    </form>

                    <a href="logout.php?clear_cookie=1" class="text-decoration-none small text-secondary">
                        <i class="fas fa-user-friends me-1"></i> სხვა ანგარიშით შესვლა
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- FontAwesome იკონკებისთვის -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
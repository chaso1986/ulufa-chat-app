<?php
include_once "../config.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// წამოვიღოთ სახელი და გვარი სათაურისთვის
$stmt = $conn->prepare("SELECT first_name, last_name, username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$current_user = $stmt->get_result()->fetch_assoc();

// შევადგინოთ გამოსაჩენი სახელი
$display_name = (!empty($current_user['first_name'])) 
    ? $current_user['first_name'] . " " . $current_user['last_name'] 
    : $current_user['username'];
?>

<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messenger Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="manifest" href="/chat-app/manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <style>
        @media (max-width: 767px) {
            #sidebar {
                position: fixed;
                top: 0; left: 0;
                width: 100%;
                height: 100%;
                z-index: 100;
                background: white;
                display: flex !important;
                flex-direction: column;
                transition: transform 0.25s ease;
            }
            #sidebar.hidden {
                transform: translateX(-100%);
                pointer-events: none;
            }
            #main_chat_area {
                position: fixed;
                top: 0; left: 0;
                width: 100%;
                height: 100%;
                z-index: 99;
                display: none !important;
            }
            #main_chat_area.active {
                display: flex !important;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row vh-100">
            <!-- Sidebar -->
            <div class="col-md-4 col-lg-3 border-end bg-white p-0 d-flex flex-column" id="sidebar">
                <!-- Sidebar-ის ბოლოში -->
                <div class="p-3 border-top mt-auto bg-light">
                    <a href="../auth/profile_edit.php" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-user-cog"></i> პროფილი
                    </a>
                </div>
                <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold">ჩატები</h5>
                    <small class="text-primary fw-semibold"><?= htmlspecialchars($display_name) ?></small>
                    
                    <a href="../auth/logout.php" class="btn btn-sm btn-danger d-flex align-items-center gap-1">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="ms-1">გამოსვლა</span>
                    </a>
                </div>
                <div class="p-2">
                    <input type="text" id="search_user" class="form-control rounded-pill" placeholder="ძებნა...">
                </div>
                <div id="user_list" class="flex-grow-1 overflow-auto">
                    <!-- მომხმარებლები AJAX-ით -->
                </div>
            </div>

            <!-- Chat Window -->
            <div class="col-md-8 col-lg-9 bg-light p-0 d-flex flex-column" id="main_chat_area">
                <div class="flex-grow-1 d-flex align-items-center justify-content-center text-muted">
                    <h5>აირჩიეთ მომხმარებელი საუბრის დასაწყებად</h5>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/chat.js"></script>
    <script>
        // მობილური: ჩატში შესვლისას sidebar-ი იმალება
        function showChat() {
            if (window.innerWidth < 768) {
                $('#sidebar').addClass('hidden');
                $('#main_chat_area').addClass('active');
            }
        }

        // მობილური: უკან დაბრუნება sidebar-ზე
        function showSidebar() {
            if (window.innerWidth < 768) {
                $('#sidebar').removeClass('hidden');
                $('#main_chat_area').removeClass('active');
            }
        }

        // chat.js-ში loadChat() გამოძახებისას showChat() დავამატე —
        // თუ chat.js-ში loadChat ფუნქცია გაქვს, იქ დაამატე: showChat();
        // ან override გავაკეთოთ აქ:
        $(document).on('click', '#user_list .list-group-item, #user_list [data-user-id]', function() {
            showChat();
        });
    </script>

    <!-- Edit Modal -->
<div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:white; border-radius:16px; padding:24px; width:300px; max-width:90%;">
    <div style="width:48px; height:48px; border-radius:50%; background:#eff6ff; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; font-size:20px;">✏️</div>
    <p style="font-size:16px; font-weight:600; text-align:center; margin:0 0 6px; color:#111827;">შეტყობინების რედაქტირება</p>
    <p style="font-size:13px; color:#6b7280; text-align:center; margin:0 0 16px;">შეცვალეთ ტექსტი და დაადასტურეთ</p>
    <textarea id="editTextarea" rows="3" style="width:100%; box-sizing:border-box; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-size:14px; font-family:inherit; resize:none; outline:none; background:#f9fafb; margin-bottom:16px;"></textarea>
    <div style="display:flex; gap:8px;">
      <button onclick="closeEditModal()" style="flex:1; padding:10px; border:1px solid #e5e7eb; border-radius:10px; background:white; font-size:14px; cursor:pointer;">გაუქმება</button>
      <button onclick="confirmEdit()" style="flex:1; padding:10px; border:none; border-radius:10px; background:#2563eb; color:white; font-size:14px; font-weight:500; cursor:pointer;">შენახვა</button>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:white; border-radius:16px; padding:24px; width:300px; max-width:90%;">
    <div style="width:48px; height:48px; border-radius:50%; background:#fef2f2; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; font-size:20px;">🗑️</div>
    <p style="font-size:16px; font-weight:600; text-align:center; margin:0 0 6px; color:#111827;">წაშლის დადასტურება</p>
    <p style="font-size:13px; color:#6b7280; text-align:center; margin:0 0 20px;">ნამდვილად გსურთ შეტყობინების წაშლა?</p>
    <div style="display:flex; gap:8px;">
      <button onclick="closeDeleteModal()" style="flex:1; padding:10px; border:1px solid #e5e7eb; border-radius:10px; background:white; font-size:14px; cursor:pointer;">გაუქმება</button>
      <button onclick="confirmDelete()" style="flex:1; padding:10px; border:none; border-radius:10px; background:#ef4444; color:white; font-size:14px; font-weight:500; cursor:pointer;">წაშლა</button>
    </div>
  </div>
</div>
</body>
</html>
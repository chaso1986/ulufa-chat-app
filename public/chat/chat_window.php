<?php
include_once "../config.php";
$receiver_id = $_POST['receiver_id'];

// წამოვიღოთ სახელი, გვარი და იუზერნეიმი
$stmt = $conn->prepare("SELECT username, first_name, last_name, avatar, status FROM users WHERE id = ?");
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// სახელის ფორმირება
$display_name = (!empty($user['first_name'])) 
    ? htmlspecialchars($user['first_name'] . " " . $user['last_name']) 
    : htmlspecialchars($user['username']);
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="manifest" href="/chat-app/manifest.json">
<meta name="theme-color" content="#0d6efd">
<!-- ჩატის ჰედერი -->
<div class="p-3 border-bottom bg-white d-flex align-items-center sticky-top">
    <button class="btn d-md-none me-2" onclick="showSidebar()"><i class="fas fa-arrow-left"></i></button>
    <img src="../assets/uploads/<?= $user['avatar'] ?>" class="rounded-circle me-2" style="width:40px; height:40px; object-fit:cover;">
    <div style="min-width: 0;">
        <!-- აქ უკვე გამოჩნდება სახელი და გვარი[cite: 7] -->
        <h6 class="m-0 text-truncate fw-bold"><?= $display_name ?></h6>
        <small class="text-muted">
            <i class="fas fa-circle <?= $user['status'] == 'online' ? 'text-success' : 'text-secondary' ?>" style="font-size: 8px;"></i>
            <?= $user['status'] == 'online' ? 'აქტიურია' : 'ხაზგარეშეა' ?>
        </small>
    </div>
</div>
<!-- შეტყობინებების კონტეინერი -->
<div id="chat_box" class="flex-grow-1 p-3 overflow-auto d-flex flex-column" style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-attachment: fixed;">
    <!-- შეტყობინებები ჩაიტვირთება აქ -->
</div>

            <!-- შეტყობინების გაგზავნის ფორმა - გასწორებული და მობილურზე ოპტიმიზებული -->
<div class="chat-input-area" style="padding: 10px 12px; background: white; border-top: 1px solid #e5e7eb;">
    
    <!-- ფოტოს პრევიუს ბლოკი (ფორმის გარეთ, რომ არ დაარღვიოს ფლექსი) -->
    <div id="preview_container" style="display: none; padding: 8px; position: relative; width: fit-content;">
        <img id="image_preview" src="" style="max-height: 100px; border-radius: 10px; border: 1px solid #ddd;">
        <button type="button" onclick="clearImage()" style="position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; border: none; border-radius: 50%; width: 22px; height: 22px; font-size: 12px; cursor: pointer;">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <form id="chat_form" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 8px; width: 100%;">
        
        <!-- ფაილის არჩევა -->
        <label for="file_input" style="flex-shrink: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #6b7280; border-radius: 50%; background: #f3f4f6;">
            <i class="fas fa-paperclip"></i>
            <input type="file" id="file_input" name="file" accept="image/*" style="display:none;" onchange="previewImage(this)">
        </label>

        <!-- ტექსტის შეყვანა -->
        <div style="flex: 1; min-width: 0;">
            <textarea id="message_input" name="message"
               style="
                   width: 100%;
                   height: 40px;
                   max-height: 100px;
                   padding: 10px 15px;
                   border: 1px solid #e5e7eb;
                   border-radius: 20px;
                   outline: none;
                   font-size: 16px;
                   background: #f9fafb;
                   box-sizing: border-box;
                   resize: none;
                   font-family: inherit;
                   line-height: 1.4;
                   overflow-y: auto;
               "
               placeholder="ჩაწერეთ..."
               autocomplete="off"
               rows="1"
               oninput="this.style.height = '40px'; this.style.height = Math.min(this.scrollHeight, 100) + 'px';"></textarea>
        </div>

        <!-- გაგზავნის ღილაკი -->
        <button type="submit" style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; border: none; background: #2563eb; color: white; display: flex; align-items: center; justify-content: center; cursor: pointer;">
            <i class="fas fa-paper-plane"></i>
        </button>

    </form>
</div>

<script>
function previewImage(input) {
    const container = document.getElementById('preview_container');
    const preview = document.getElementById('image_preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function clearImage() {
    const input = document.getElementById('file_input');
    const container = document.getElementById('preview_container');
    
    input.value = ""; 
    container.style.display = 'none';
}

document.getElementById('chat_form').addEventListener('submit', function() {
    setTimeout(clearImage, 100);
});
</script>
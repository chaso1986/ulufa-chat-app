<?php
include_once "../config.php";

if (!isset($_SESSION['user_id']) || !isset($_POST['receiver_id'])) {
    exit("არასწორი მოთხოვნა");
}

$my_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];

// 1. მონიშნე შეტყობინებები წაკითხულად
$conn->query("UPDATE messages SET is_seen = 1 WHERE sender_id = $receiver_id AND receiver_id = $my_id");

// 2. წამოიღე მიმოწერა + სახელები + რეაქციები (LEFT JOIN-ით)
$sql = "SELECT m.*, u.first_name, u.last_name, u.username,
               (SELECT reaction_type FROM message_reactions WHERE message_id = m.id AND user_id = $my_id) as my_reaction,
               (SELECT COUNT(*) FROM message_reactions WHERE message_id = m.id AND reaction_type = 'heart') as hearts_count
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE (m.sender_id = $my_id AND m.receiver_id = $receiver_id) OR 
              (m.sender_id = $receiver_id AND m.receiver_id = $my_id) 
        ORDER BY m.created_at ASC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $is_my_msg = ($row['sender_id'] == $my_id);
        $align = $is_my_msg ? 'sent' : 'received';
        
        $displayName = (!empty($row['first_name']) || !empty($row['last_name'])) 
                       ? trim($row['first_name'] . " " . $row['last_name']) 
                       : $row['username'];

        // ფაილის ლოგიკა
        $file_html = "";
        if (!empty($row['file_path'])) {
            $file_url = "../assets/uploads/" . $row['file_path'];
            $file_ext = strtolower(pathinfo($row['file_path'], PATHINFO_EXTENSION));
            if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $file_html = "<div class='mt-2 mb-1'><img src='{$file_url}' class='img-fluid rounded' style='max-width: 100%; max-height: 250px; cursor: pointer;' onclick='window.open(\"{$file_url}\", \"_blank\")'></div>";
            } else {
                $file_html = "<br><a href='{$file_url}' target='_blank' class='small text-white'><i class='fas fa-paperclip'></i> ფაილი</a>";
            }
        }
        
        $edited_label = (!empty($row['is_edited'])) ? "<small style='opacity:0.6; font-size: 10px;'> (რედაქტირებული)</small>" : "";

        // რეაქციის ვიზუალი (გული)
        $reaction_html = "";
        if ($row['hearts_count'] > 0) {
            $reaction_html = "<div class='reaction-badge shadow-sm'>❤️ " . ($row['hearts_count'] > 1 ? $row['hearts_count'] : "") . "</div>";
        }
        
        echo "
        <div class='message-wrapper d-flex flex-column'>
            " . (!$is_my_msg ? "<small class='text-muted mb-1 ms-2' style='font-size: 11px;'>".htmlspecialchars($displayName)."</small>" : "") . "
            
            <div class='message {$align} position-relative' 
                 id='msg-{$row['id']}' 
                 ondblclick='toggleHeart({$row['id']})' 
                 style='user-select: none; cursor: pointer;'>
                
                {$file_html}
                " . (!empty($row['message']) ? "<span class='msg-text'>" . htmlspecialchars($row['message']) . "</span>" : "") . "
                {$edited_label}

                <div class='msg-time text-end mt-1' style='font-size:10px; opacity:0.7;'>
                    " . date('H:i', strtotime($row['created_at'])) . "
                </div>

                <!-- რეაქციის ბეიჯი -->
                {$reaction_html}

                " . ($is_my_msg ? "
                <div class='msg-actions'>
                    <i class='fas fa-pen me-2' onclick='editMsg({$row['id']})'></i>
                    <i class='fas fa-trash' onclick='deleteMsg({$row['id']})'></i>
                </div>" : "") . "
            </div>
        </div>";
    }
} else {
    echo "<div class='text-center text-muted mt-5'>შეტყობინებები არ არის.</div>";
}
?>
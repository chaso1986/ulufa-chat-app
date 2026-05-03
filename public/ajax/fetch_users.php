<?php
include_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    exit();
}

$my_id = $_SESSION['user_id'];

// წამოვიღოთ ყველა მომხმარებელი გარდა საკუთარი თავისა
$sql = "SELECT id, username, first_name, last_name, avatar FROM users WHERE id != $my_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="list-group list-group-flush">';
    while ($row = $result->fetch_assoc()) {
        
        // სახელის ფორმირება: თუ სახელი/გვარი არსებობს, ვიყენებთ მათ, თუ არა - username-ს
        $display_name = (!empty($row['first_name'])) 
            ? htmlspecialchars($row['first_name'] . " " . $row['last_name']) 
            : htmlspecialchars($row['username']);
            
        $avatar = !empty($row['avatar']) ? $row['avatar'] : 'default_avatar.png';

        echo "
        <a href='#' class='list-group-item list-group-item-action d-flex align-items-center p-3 border-0' 
           onclick='loadChat({$row['id']})' data-user-id='{$row['id']}'>
            <div class='position-relative'>
                <img src='../assets/uploads/{$avatar}' class='rounded-circle me-3' style='width: 45px; height: 45px; object-fit: cover;'>
            </div>
            <div class='flex-grow-1'>
                <div class='d-flex justify-content-between align-items-center'>
                    <h6 class='mb-0 fw-bold'>{$display_name}</h6>
                </div>
                <small class='text-muted'>დააწკაპუნეთ საუბრისთვის</small>
            </div>
        </a>";
    }
    echo '</div>';
} else {
    echo '<div class="text-center p-3 text-muted">მომხმარებლები ვერ მოიძებნა</div>';
}
?>
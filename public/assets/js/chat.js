let current_receiver_id = null;

$(document).ready(function() {
    loadUsers();
    
    // მომხმარებლების სიის განახლება 3 წამში ერთხელ
    setInterval(loadUsers, 3000);

    // შეტყობინებების ავტომატური განახლება
    setInterval(function() {
        if(current_receiver_id) fetchMessages();
    }, 1000);
});

function loadUsers() {
    $.get('../ajax/fetch_users.php', function(data) {
        $('#user_list').html(data);
    });
}

function loadChat(id) {
    current_receiver_id = id;
    $.post('chat_window.php', { receiver_id: id }, function(data) {
        $('#main_chat_area').html(data);
        fetchMessages();
        showChat(); // ← აქ
    });
}

function showChat() {
    if ($(window).width() < 768) {
        $('#sidebar').addClass('hidden');
        $('#main_chat_area').addClass('active');
    }
}

function showSidebar() {
    if ($(window).width() < 768) {
        $('#sidebar').removeClass('hidden');
        $('#main_chat_area').removeClass('active');
    }
}

function fetchMessages() {
    let chatBox = $('#chat_box');
    
    // 1. ვამოწმებთ, არის თუ არა მომხმარებელი ბოლოში
    // scrollHeight (მთლიანი სიმაღლე) - scrollTop (რამდენადაა ჩამოწეული) 
    // თუ ეს უდრის clientHeight-ს (რასაც ვხედავთ), ე.ი. ბოლოში ვართ.
    // +50 პიქსელს ვამატებთ "ცდომილებისთვის".
    let isAtBottom = (chatBox[0].scrollHeight - chatBox.scrollTop()) <= (chatBox.outerHeight() + 50);

    $.post('../ajax/fetch_messages.php', { receiver_id: current_receiver_id }, function(data) {
        chatBox.html(data);

        // 2. მხოლოდ იმ შემთხვევაში ჩამოვწიოთ სკროლი, თუ მომხმარებელი ისედაც ბოლოში იყო
        // ან თუ ეს ჩატის პირველი ჩატვირთვაა
        if (isAtBottom) {
            chatBox.scrollTop(chatBox[0].scrollHeight);
        }
    });
}

let editingMsgId = null;
let deletingMsgId = null;

function editMsg(id) {
    editingMsgId = id;
    let currentText = $('#msg-' + id + ' .msg-text').text();
    $('#editTextarea').val(currentText);
    $('#editModal').css('display', 'flex');
}

function closeEditModal() {
    $('#editModal').css('display', 'none');
    editingMsgId = null;
}

function confirmEdit() {
    let newText = $('#editTextarea').val().trim();
    if (!newText || !editingMsgId) return;
    $.post('../ajax/edit_delete_message.php', {
        action: 'edit',
        message_id: editingMsgId,
        new_message: newText
    }, function() {
        closeEditModal();
        fetchMessages();
    });
}

function deleteMsg(id) {
    deletingMsgId = id;
    $('#deleteModal').css('display', 'flex');
}

function closeDeleteModal() {
    $('#deleteModal').css('display', 'none');
    deletingMsgId = null;
}

function confirmDelete() {
    if (!deletingMsgId) return;
    $.post('../ajax/edit_delete_message.php', {
        action: 'delete',
        message_id: deletingMsgId
    }, function() {
        closeDeleteModal();
        fetchMessages();
    });
}

function toggleHeart(msgId) {
    $.post('../ajax/react_message.php', { message_id: msgId, reaction: 'heart' }, function(data) {
        // ფუნქციის სახელი უნდა იყოს fetchMessages()
        fetchMessages(); 
    });
}

function sendReaction(msgId, type) {
    $.post('../ajax/react_message.php', { message_id: msgId, reaction: type }, function(response) {
        if(response === 'success') {
            loadChat(current_receiver_id); // ჩატის განახლება
        }
    });
}
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/chat-app/sw.js')
    .then(() => console.log('SW registered'));
}

// შეტყობინების გაგზავნა
$(document).on('submit', '#chat_form', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    formData.append('receiver_id', current_receiver_id);

    $.ajax({
        url: '../ajax/send_message.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function() {
            $('#message_input').val('');
            $('#file_input').val('');
            fetchMessages();
        }
    });
});
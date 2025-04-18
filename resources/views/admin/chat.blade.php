@extends('layouts.admin')

@section('title', 'Dashboard Chat Admin')

@section('content')
<div class="d-flex">
  <!-- Sidebar: Danh sách phòng chat -->
  <div id="customer-list">
    <h2>Phòng Chat</h2>
    @if(isset($usersInfo) && $usersInfo->count())
      @foreach ($usersInfo as $user)
        <!-- Gửi luôn cả id và tên của khách hàng -->
        <button class="customer-btn" onclick="selectCustomer({{ $user->id }}, '{{ $user->name }}')">
          {{ $user->name }}
        </button>
      @endforeach
    @else
      <p>Không có tin nhắn.</p>
    @endif
  </div>

  <!-- Container chat chính -->
  <div id="chat-container">
    <h1 id="chat-title">Chọn phòng chat để trả lời</h1>
    <div id="chat-box">
      <!-- Lịch sử tin nhắn load từ DB sẽ hiển thị ở đây -->
    </div>
    <form id="reply-form">
      <input type="text" id="reply-message" placeholder="Nhập tin nhắn trả lời..." autocomplete="off" required>
      <button type="submit">Gửi</button>
    </form>
  </div>
</div>

<!-- CSS được thêm trực tiếp -->
<style>
  /* Sidebar chứa danh sách phòng chat */
  #customer-list {
    width: 250px;
    background: #f4f4f4;
    padding: 10px;
    overflow-y: auto;
    border-right: 1px solid #ccc;
  }
  #customer-list h2 {
    font-size: 18px;
    margin-top: 0;
  }
  .customer-btn {
    display: block;
    width: 100%;
    padding: 10px;
    margin-bottom: 5px;
    background: #007bff;
    color: white;
    text-align: left;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  .customer-btn:hover {
    background: #0056b3;
  }
  /* Phần chat chính */
  #chat-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 10px;
  }
  #chat-title {
    margin: 0 0 10px 0;
  }
  #chat-box {
    flex: 1;
    border: 1px solid #ccc;
    padding: 10px;
    overflow-y: auto; /* Thêm thanh cuộn khi quá nhiều tin nhắn */
    max-height: 400px; /* Giới hạn chiều cao */
    min-height: 300px; /* Đảm bảo không bị quá nhỏ */
    background: #fff; /* Giúp tách biệt với nền */
  }

  .message {
    margin-bottom: 10px;
  }
  #reply-form {
    display: flex;
  }
  #reply-message {
    flex: 1;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  #reply-form button {
    margin-left: 5px;
    padding: 8px 15px;
    border: none;
    background: #28a745;
    color: white;
    border-radius: 4px;
    cursor: pointer;
  }
  #reply-form button:hover {
    background: #218838;
  }
</style>

<!-- Script được thêm trực tiếp -->
<script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
<script>
  // Lấy id và tên của admin từ server
  const adminId = parseInt("{{ Auth::id() }}");
  const adminName = "{{ Auth::user()->name }}";

  // Kết nối tới Socket.IO server
  const socket = io('http://localhost:3000');

  // currentCustomerId: ID của khách hàng được chọn
  // currentRoomId: room id được tính từ adminId và customerId
  let currentCustomerId = null;
  let currentRoomId = null;

  // Hàm tính room id theo công thức: "chat_{min(adminId, customerId)}_{max(adminId, customerId)}"
  function getRoomId(customerId) {
    let ids = [adminId, customerId].sort((a, b) => a - b);
    return 'chat_' + ids.join('_');
  }

  function appendMessage(data) {
    const chatBox = document.getElementById('chat-box');
    const div = document.createElement('div');
    div.classList.add('message');
    
    let senderName = "Unknown";

    // Kiểm tra nếu tin nhắn được gửi từ admin
    if (data.sender && data.sender.toLowerCase() === "admin") {
        senderName = "Admin";
    } else if (data.user && typeof data.user === "object" && data.user.name) {
        senderName = data.user.name;
    } else if (data.user) {
        senderName = data.user;
    }

    div.innerHTML = `<strong>${senderName}:</strong> ${data.message}`;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}


  // Hàm load lịch sử tin nhắn từ DB qua AJAX dựa trên room_id
  function loadChatHistory(roomId) {
    console.log("Loading chat history for room: " + roomId);
    fetch('/admin/chat/messages?room_id=' + encodeURIComponent(roomId), {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    .then(response => response.json())
    .then(data => {
      console.log("Received chat history:", data);
      const chatBox = document.getElementById('chat-box');
      chatBox.innerHTML = "";
      if (data.status === 'success' && data.messages.length > 0) {
        data.messages.forEach(msg => {
          appendMessage(msg);
        });
      } else {
        chatBox.innerHTML = "<p>Không có tin nhắn.</p>";
      }
    })
    .catch(error => console.error('Error loading chat history:', error));
  }

  // Khi kết nối tới socket server, admin không join room ngay từ connect,
  // mà sẽ join room khi chọn khách hàng
  socket.on('connect', () => {
    console.log("Socket connected: " + socket.id);
  });

  // Lắng nghe tin nhắn realtime từ server theo room id
  socket.on('message', (payload) => {
    console.log('Admin nhận tin nhắn:', payload);
    // Xử lý payload có thể lồng dữ liệu trong payload.data.data hoặc payload.data
    let messageData;
    if (payload.data && payload.data.data) {
      messageData = payload.data.data;
    } else if (payload.data) {
      messageData = payload.data;
    } else {
      messageData = payload;
    }
    // Kiểm tra nếu payload.channel khớp với currentRoomId
    if (currentRoomId && payload.channel === currentRoomId) {
      appendMessage(messageData);
    }
  });

  // Hàm chọn phòng chat, nhận thêm tên khách hàng, tính roomId, join room và load lịch sử chat
  function selectCustomer(customerId, customerName) {
    currentCustomerId = customerId;
    currentRoomId = getRoomId(customerId);
    document.getElementById('chat-title').innerText = "Phòng Chat của " + customerName;
    document.getElementById('chat-box').innerHTML = "";
    // Tham gia room mới
    socket.emit('joinRoom', currentRoomId);
    // Load lịch sử tin nhắn dựa trên roomId
    loadChatHistory(currentRoomId);
  }

  // Xử lý gửi tin nhắn phản hồi từ admin qua AJAX
  document.getElementById('reply-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!currentCustomerId || !currentRoomId) {
      alert("Vui lòng chọn phòng chat cần trả lời.");
      return;
    }
    const message = document.getElementById('reply-message').value;
    if (!message.trim()) return;
    // Gửi tin nhắn qua AJAX, kèm chat_user_id (ID khách hàng) và room_id
    fetch('/admin/send-reply', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ 
        message: message, 
        chat_user_id: currentCustomerId, 
        room_id: currentRoomId 
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        document.getElementById('reply-message').value = '';
      }
    })
    .catch(error => console.error('Error sending reply:', error));
  });
</script>
@endsection

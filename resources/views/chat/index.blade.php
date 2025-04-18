<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Chat với Admin</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Import Socket.IO client từ CDN -->
  <script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
  <style>
    #chat-box {
      border: 1px solid #ccc;
      height: 300px;
      overflow-y: scroll;
      padding: 10px;
      margin-bottom: 10px;
    }
    .message {
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <h1>Chat với Admin</h1>
  <!-- Hộp chat hiển thị tin nhắn đã được load từ DB -->
  <div id="chat-box">
    @if(isset($messages) && $messages->count())
      @foreach ($messages as $msg)
        <div class="message">
          <!-- Nếu tin nhắn của user đăng nhập thì hiển thị "Tôi", ngược lại hiển thị sender -->
          <strong>
            {{ (isset($msg->user) && $msg->user->id == Auth::id()) ? 'Tôi' : $msg->user->name }}:
          </strong>
          {{ $msg->message }}
        </div>
      @endforeach
    @else
      <p>Không có tin nhắn.</p>
    @endif
  </div>
  
  <form id="chat-form">
    <input type="text" id="message" placeholder="Nhập tin nhắn..." autocomplete="off" required>
    <button type="submit">Gửi</button>
  </form>

  <script>
    // Lấy thông tin user từ Auth
    const userId = "{{ Auth::id() }}";
    const userName = "{{ Auth::user()->name }}";
    // Room id được tính từ phía controller (ví dụ: "chat_3_5")
    const roomId = "{{ $roomId }}";
  
    // Kết nối tới Socket.IO server
    const socket = io('http://localhost:3000');
  
    // Khi kết nối, tham gia phòng chat theo roomId
    socket.on('connect', () => {
      socket.emit('joinRoom', roomId);
      console.log('Đã tham gia phòng:', roomId);
    });
  
    // Hàm thêm tin nhắn vào hộp chat
    function appendMessage(data) {
      const chatBox = document.getElementById('chat-box');
      const div = document.createElement('div');
      div.classList.add('message');
      
      // Lấy sender từ data.sender nếu có, nếu không dùng data.user
      let sender = data.sender || data.user;
      if (sender === userName) {
        sender = "Tôi";
      }
      
      div.innerHTML = `<strong>${sender}:</strong> ${data.message}`;
      chatBox.appendChild(div);
      chatBox.scrollTop = chatBox.scrollHeight;
    }
  
    // Lắng nghe sự kiện 'message' từ server theo roomId
    socket.on('message', (payload) => {
      console.log('Nhận payload:', payload);
      // Xử lý cấu trúc lồng: ưu tiên payload.data.data nếu có
      let messageData;
      if (payload.data && payload.data.data) {
        messageData = payload.data.data;
      } else if (payload.data) {
        messageData = payload.data;
      } else {
        messageData = payload;
      }
  
      // Kiểm tra nếu payload.channel khớp với roomId
      if (payload.channel === roomId) {
        appendMessage(messageData);
      }
    });
  
    // (Tuỳ chọn) Lắng nghe 'direct-message' nếu server phát sự kiện này
    socket.on('direct-message', (data) => {
      console.log('Nhận tin nhắn trực tiếp:', data);
      appendMessage(data);
    });
  
    // Gửi tin nhắn qua AJAX khi form được submit
    document.getElementById('chat-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const message = document.getElementById('message').value;
      if (!message.trim()) return;
  
      
      fetch('/send-message', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        // Gửi kèm cả room_id để server xử lý đúng phòng chat
        body: JSON.stringify({ message: message, room_id: roomId })
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          document.getElementById('message').value = '';
        }
      })
      .catch(error => console.error('Error:', error));
    });
  </script>
  
</body>
</html>

const io = require('socket.io')(3000, {  
    cors: {
        origin: "*",
    }
});
const Redis = require('ioredis');
const redis = new Redis();

// Sử dụng psubscribe để lắng nghe tất cả các channel có pattern "chat_*"
// (Vì room id của chúng ta có định dạng "chat_{id1}_{id2}")
redis.psubscribe('chat_*', (err, count) => {
    if (err) {
        console.error("Lỗi khi psubscribe Redis:", err);
        return;
    }
    console.log(`Đã psubscribe thành công ${count} channel.`);
});

// Lắng nghe sự kiện từ tất cả các channel phù hợp với pattern "chat_*"
redis.on('pmessage', (pattern, channel, message) => {
    console.log(`Nhận message từ Redis channel '${channel}':`, message);
    try {
        const data = JSON.parse(message);
        console.log("Parsed data:", data);
        // Phát sự kiện đến room có tên bằng channel (room id)
        io.to(channel).emit('message', { channel, data });
    } catch (error) {
        console.error('Lỗi khi parse message:', error);
    }
});

io.on('connection', (socket) => {
    console.log(`Client kết nối: ${socket.id}`);
    
    // Cho phép client tham gia phòng (room) theo room id mà chúng ta tính toán từ backend
    socket.on('joinRoom', (room) => {
        socket.join(room);
        console.log(`Socket ${socket.id} đã tham gia room: ${room}`);
    });
    
    socket.on('disconnect', () => {
        console.log(`Client ngắt kết nối: ${socket.id}`);
    });
});

console.log('Socket.IO server đang chạy trên port 3000');

<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;      // Tên của người gửi
    public $message;   // Nội dung tin nhắn
    public $userId;    // ID của người gửi (cũ, dùng cho mục đích khác nếu cần)
    public $roomId;    // Room ID dùng để định danh channel phát

    public function __construct($user, $message, $userId, $roomId)
    {
        $this->user    = $user;
        $this->message = $message;
        $this->userId  = $userId;
        $this->roomId  = $roomId;
    }

    public function broadcastOn()
    {
        // Phát tin nhắn lên channel dựa trên roomId, ví dụ "chat_3_5"
        return new Channel($this->roomId);
    }
}

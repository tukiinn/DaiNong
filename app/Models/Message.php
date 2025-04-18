<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // Các trường có thể gán hàng loạt
    protected $fillable = [
        'user_id',    // ID của user gửi tin (hoặc nhận nếu cần)
        'sender',     // 'user' hoặc 'admin'
        'message',    // Nội dung tin nhắn
        'room_id',
    ];

    /**
     * Mối quan hệ: Tin nhắn thuộc về 1 user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

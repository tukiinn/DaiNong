<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log; // Import Log facade
use App\Http\Controllers\Controller;
use App\Models\User; // Model User dùng để lấy thông tin user
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class AdminChatController extends Controller
{
    // Hiển thị giao diện dashboard chat cho admin
    public function index()
    {
        // Lấy danh sách user đã gửi tin nhắn mà không phải từ admin
        $userIds = Message::where('sender', '!=', 'admin')
                     ->select('user_id')
                     ->distinct()
                     ->pluck('user_id');
        $usersInfo = User::whereIn('id', $userIds)->get();
        return view('admin.chat', compact('usersInfo'));
    }
    
    // Phương thức load lịch sử chat theo room_id
    public function getChatMessages(Request $request)
    {
        $roomId = $request->query('room_id');

        
        if (!$roomId) {
        
            return response()->json(['status' => 'error', 'message' => 'Room ID missing'], 400);
        }
        
        // Eager load quan hệ user để lấy thông tin tên người dùng
        $messages = Message::with('user')
                     ->where('room_id', $roomId)
                     ->orderBy('created_at', 'asc')
                     ->get();
        
        return response()->json(['status' => 'success', 'messages' => $messages]);
    }
    

    // Xử lý phản hồi tin nhắn từ admin
    public function replyMessage(Request $request)
    {

        
        $request->validate([
            'message'      => 'required|string',
            'chat_user_id' => 'required|exists:users,id',
            'room_id'      => 'required|string'
        ]);
    
        $admin = Auth::user();
        
        if (!$admin) {
            return response()->json(['status' => 'error', 'message' => 'Admin not authenticated'], 401);
        }
    
        // Đảm bảo tên hiển thị là "Admin" khi gửi tin nhắn
        $adminName = 'Admin';
        
    
        // Lưu tin nhắn vào DB
        $message = Message::create([
            'user_id' => $admin->id,  // ID của admin
            'sender'  => 'admin', // Đánh dấu sender là admin
            'message' => $request->message,
            'room_id' => $request->room_id,
        ]);
    
    // Broadcast event với tên "Admin" chỉ gửi đến các client khác, không gửi cho client hiện tại (admin)
    broadcast(new MessageSent($adminName, $request->message, $admin->id, $request->room_id))->toOthers();

    
        return response()->json(['status' => 'success']);
    }
    
}

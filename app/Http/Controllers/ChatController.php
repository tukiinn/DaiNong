<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class ChatController extends Controller
{
    /**
     * Hiển thị giao diện chat cho user.
     * Load tin nhắn từ DB theo room id để bao gồm tin nhắn của cả 2 bên.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }
        // Tìm admin (giả sử admin có role = 'admin')
        $adminId =  5;

        // Tính room id dựa trên ID của user và admin (sắp xếp tăng dần)
        $ids = [$user->id, $adminId];
        sort($ids);
        $roomId = 'chat_' . implode('_', $ids);

        // Lấy tin nhắn theo room_id, bao gồm tin nhắn của cả user và admin
        $messages = Message::where('room_id', $roomId)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.index', compact('messages', 'roomId'));
    }

    public function getRoomId(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Giả sử admin có ID = 5
        $adminId = 5;
        $ids = [$user->id, $adminId];
        sort($ids);
        $roomId = 'chat_' . implode('_', $ids);
    
        return response()->json(['roomId' => $roomId]);
    }
    public function getMessages(Request $request)
    {
        $roomId = $request->query('room_id');
    
        if (!$roomId) {
            return response()->json(['status' => 'error', 'message' => 'Thiếu Room ID'], 400);
        }
    
        $messages = Message::where('room_id', $roomId)
            ->orderBy('created_at', 'asc')
            ->with('user:id,name') // Lấy thông tin user
            ->get();
    
        // Chuyển đổi dữ liệu để đồng bộ với frontend
        $messages = $messages->map(function ($msg) {
            return [
                'id'      => $msg->id,
                'message' => $msg->message,
                'user_id' => $msg->user_id,
                'sender'  => $msg->sender === 'admin' ? 'Admin' : ($msg->user->name ?? 'Người dùng'),
                'created_at' => $msg->created_at->toDateTimeString(),
            ];
        });
    
        return response()->json(['status' => 'success', 'messages' => $messages]);
    }
    
    
    
    public function getUserName($id)
    {
        $user = User::find($id);
        return response()->json(['name' => $user->name ?? 'Người dùng'], $user ? 200 : 404);
    }
    
    /**
     * Xử lý gửi tin nhắn từ user.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
    
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }
    
        // Xác định xem người gửi có phải là admin hay không
        $isAdmin = $user->role === 'admin'; // Giả sử role của admin là 'admin'
    
        // Tìm admin (giả sử admin có role = 'admin')
        $adminId = 5;
    
        // Tính room id dựa trên ID của user và admin (sắp xếp tăng dần)
        $ids = [$user->id, $adminId];
        sort($ids);
        $roomId = 'chat_' . implode('_', $ids);
    
        // Lưu tin nhắn vào DB
        $message = Message::create([
            'user_id' => $user->id,
            'sender'  => $isAdmin ? 'admin' : 'user',
            'message' => $request->message,
            'room_id' => $roomId,
        ]);
    
        // Lưu vào Redis cho realtime
        $messageData = [
            'user'      => $isAdmin ? 'Admin' : $user->name,
            'message'   => $request->message,
            'timestamp' => now()->toDateTimeString(),
        ];
        Redis::lpush("chat:{$roomId}", json_encode($messageData));
        Redis::ltrim("chat:{$roomId}", 0, 49);
    
        // Gửi sự kiện realtime qua Socket.IO
        event(new MessageSent($isAdmin ? 'Admin' : $user->name, $request->message, $user->id, $roomId));
    
        return response()->json(['status' => 'success']);
    }
    
    
}

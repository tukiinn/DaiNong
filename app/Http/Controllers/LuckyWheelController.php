<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Voucher;

class LuckyWheelController extends Controller
{
    /**
     * Hiển thị trang vòng quay may mắn.
     */
    public function index()
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để tham gia vòng quay.');
        } 
        return view('lucky-wheel');
    }
    public function storeSpin(Request $request)
    {
        // Validate dữ liệu đầu vào cho spin result
        $validated = $request->validate([
            'result' => 'required|string',
        ]);
    
        if (Auth::check()) {
            $userId = Auth::id();
            // Trừ lượt quay nếu remaining_spins > 0
            $updated = DB::table('users')
                ->where('id', $userId)
                ->where('remaining_spins', '>', 0)
                ->decrement('remaining_spins', 1);
    
            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không còn lượt quay!'
                ], 422);
            }
        }
    
        // Lưu kết quả lượt quay vào bảng user_spins
        DB::table('user_spins')->insert([
            'user_id'    => Auth::check() ? Auth::user()->id : null,
            'prize'      => $validated['result'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        return response()->json([
            'success' => true,
        ]);
    }
    



}

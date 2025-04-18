<?php

namespace App\Listeners;

use App\Models\Cart;
use Illuminate\Auth\Events\Login;

class SyncCartListener
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
    
        // Lấy giỏ hàng từ session
        $cartItems = session()->get('cart', []);
    
        foreach ($cartItems as $cartItem) {
            // Kiểm tra xem sản phẩm đã có trong giỏ hàng của user chưa, so sánh theo product_id và size (nếu tồn tại)
            $existingCartItem = Cart::where('user_id', $user->id)
                                    ->where('product_id', $cartItem['product_id'])
                                    ->where('size', isset($cartItem['size']) ? $cartItem['size'] : null)
                                    ->first();
    
            if ($existingCartItem) {
                // Nếu đã tồn tại, chỉ tăng số lượng thêm 1 đơn vị (hoặc số lượng từ session)
                $existingCartItem->quantity += $cartItem['so_luong'];
                $existingCartItem->save();
            } else {
                // Nếu chưa có, tạo mới mục giỏ hàng với thông tin product_id, quantity và size
                Cart::create([
                    'user_id'    => $user->id,
                    'product_id' => $cartItem['product_id'],
                    'quantity'   => $cartItem['so_luong'],
                    'size'       => $cartItem['size'] ?? null,
                ]);
            }
        }
    
        // Xóa giỏ hàng khỏi session sau khi merge
        session()->forget('cart');
    }
    
}
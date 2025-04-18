<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // Hiển thị danh sách đơn hàng của người dùng hiện tại  
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem đơn hàng.');
        }
        $userId = Auth::id();
        $orders = Order::where('user_id', $userId)
    ->orderBy('created_at', 'desc')
    ->paginate(6);

        return view('orders.index', compact('orders'));
    }


    public function createOrder(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để mua hàng.');
        }
        $userId = Auth::id();
    
        // Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            'ten_khach_hang'  => 'required|string|min:6|max:20',
            'so_dien_thoai'   => 'required|digits:10|numeric',
            'dia_chi'         => 'required|string|max:255',
        ], [
            'ten_khach_hang.required' => 'Tên khách hàng là bắt buộc.',
            'ten_khach_hang.min'      => 'Tên khách hàng phải có ít nhất 6 ký tự.',
            'ten_khach_hang.max'      => 'Tên khách hàng không được vượt quá 20 ký tự.',
            'so_dien_thoai.required'  => 'Số điện thoại là bắt buộc.',
            'so_dien_thoai.digits'     => 'Số điện thoại phải có đúng 10 chữ số.',
            'so_dien_thoai.numeric'   => 'Số điện thoại chỉ được chứa các số.',
            'dia_chi.required'        => 'Địa chỉ là bắt buộc.',
            'dia_chi.max'             => 'Địa chỉ không được vượt quá 255 ký tự.',
        ]);
    
        // Lấy giỏ hàng của người dùng
        $cartItems = Cart::where('user_id', $userId)->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống.');
        }
    
        // Kiểm tra số lượng sản phẩm trong kho
        $errors = [];
        foreach ($cartItems as $cart) {
            $product = Product::find($cart->product_id);
            if (!$product) {
                $errors[] = 'Sản phẩm không xác định.';
                continue;
            }
    
           // Xác định hệ số cân nặng dựa vào size (chỉ áp dụng cho sản phẩm bán theo kg)
$weightFactor = 1;
if (optional($product->unit)->unit_name === 'kg' && $cart->size) {
    switch ($cart->size) {
        case '500g':
            $weightFactor = 0.5;
            break;
        case '250g':
            $weightFactor = 0.25;
            break;
        case '1kg':
        default:
            $weightFactor = 1;
            break;
    }
}

    
            // So sánh số lượng tồn kho với tổng khối lượng cần đặt
            if ($product->stock_quantity < $cart->quantity * $weightFactor) {
                $errors[] = 'Sản phẩm ' . $product->product_name . ' không đủ số lượng trong kho.';
            }
        }
        if (!empty($errors)) {
            return redirect()->route('cart.index')->with('error', implode('<br>', $errors));
        }
    
        $finalTotal = $request->input('final_total');
        $voucherCode = $request->input('voucher_code'); // Lấy mã voucher nếu có
    
        try {
            DB::transaction(function() use ($request, $validated, $userId, $cartItems, $finalTotal, $voucherCode, &$order) {
                // Tạo đơn hàng với trạng thái "pending"
                $order = Order::create([
                    'user_id'                => $userId,
                    'ten_khach_hang'         => $validated['ten_khach_hang'],
                    'so_dien_thoai'          => $validated['so_dien_thoai'],
                    'dia_chi'                => $validated['dia_chi'],
                    'tong_tien'              => $finalTotal,
                    'phuong_thuc_thanh_toan' => $request->phuong_thuc_thanh_toan,
                    'trang_thai'             => 'pending',
                    'payment_status'         => 'pending'
                ]);
    
                // Xử lý voucher nếu có
                if ($voucherCode) {
                    Log::info("Processing voucher trong createOrder", ['voucher_code' => $voucherCode]);
                    // Lấy voucher với khóa bản ghi để tránh race condition
                    $voucher = Voucher::where('code', $voucherCode)->lockForUpdate()->first();
                    if (!$voucher) {
                        throw new \Exception('Voucher không hợp lệ!');
                    }
                    if ($voucher->used >= $voucher->max_usage) {
                        throw new \Exception('Voucher đã hết lượt sử dụng!');
                    }
                    Log::info("Voucher trước khi increment", ['used' => $voucher->used]);
                    $voucher->increment('used');
                    $voucher->refresh();
                    Log::info("Voucher sau khi increment", ['used' => $voucher->used]);
                    // Lưu thông tin voucher vào đơn hàng (nếu cần)
                    $order->voucher_id = $voucher->id;
                    $order->save();
                }
    
                // Thêm sản phẩm vào đơn hàng và cập nhật tồn kho
                foreach ($cartItems as $cart) {
                    $product = Product::find($cart->product_id);
                    if (!$product) {
                        continue;
                    }
    
                // Xác định hệ số cân nặng dựa vào size
$weightFactor = 1;
if (optional($product->unit)->unit_name === 'kg' && $cart->size) {
    switch ($cart->size) {
        case '500g':
            $weightFactor = 0.5;
            break;
        case '250g':
            $weightFactor = 0.25;
            break;
        case '1kg':
        default:
            $weightFactor = 1;
            break;
    }
}

    
                    // Kiểm tra lại số lượng tồn kho để đảm bảo nhất quán
                    if ($product->stock_quantity < $cart->quantity * $weightFactor) {
                        throw new \Exception('Sản phẩm ' . $product->product_name . ' không đủ số lượng trong kho.');
                    }
    
                    // Cập nhật tồn kho
                    $product->stock_quantity -= $cart->quantity * $weightFactor;
                    $product->save();
    
                 // Lấy giá cơ bản: nếu có discount_price và lớn hơn 0 thì dùng, nếu không thì dùng price
$basePrice = ($product->discount_price && $product->discount_price > 0) ? $product->discount_price : $product->price;
// Tính giá theo size (weightFactor)
$computedPrice = $basePrice * $weightFactor;

    
                    // Tạo chi tiết đơn hàng, lưu luôn thông tin size nếu có, giá và tổng tiền được tính theo size
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $product->id,
                        'name'       => $product->product_name,
                        'gia'        => $computedPrice, // Lưu giá đã tính theo size
                        'so_luong'   => $cart->quantity,  
                        'size'       => $cart->size,        
                        'thanh_tien' => $computedPrice * $cart->quantity,
                    ]);
                }

             if ($finalTotal <= 0) {
                $order->payment_status = 'paid';
                $order->save();   
            }     
                // Xóa giỏ hàng sau khi đơn hàng được tạo thành công
                Cart::where('user_id', $userId)->delete();
            });
    
            // Lấy đơn hàng kèm thông tin chi tiết sản phẩm
            $order = Order::with('orderItems.product')->find($order->id);
            return view('cart.checkout.thankyouCOD', compact('order'));
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }
    
    

    // Hiển thị chi tiết đơn hàng
    public function showOrder($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem chi tiết đơn hàng.');
        }
        $userId = Auth::id();
        
        // Lấy đơn hàng kèm thông tin voucher qua quan hệ (nếu đơn hàng có voucher áp dụng)
        $order = Order::with('voucher')->where('id', $id)->where('user_id', $userId)->firstOrFail();
        $orderItems = OrderItem::where('order_id', $order->id)->get();
        
        // Tính toán text hiển thị discount nếu voucher tồn tại
        $discountText = null;
        if ($order->voucher) {
            if ($order->voucher->type === 'percentage') {
                $discountText = '-' . $order->voucher->discount . '%';
            } else { // fixed
                $discountText = '-' . number_format($order->voucher->discount, 0) . '₫';
            }
        }
        
        return view('orders.show', compact('order', 'orderItems', 'discountText'));
    }
    
    public function cancelOrder(Request $request, $id)
{
    // Kiểm tra đăng nhập
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để hủy đơn hàng.');
    }
    $userId = Auth::id();

    // Lấy đơn hàng của người dùng, đảm bảo đơn hàng tồn tại và thuộc về user
    $order = Order::with('orderItems')->where('id', $id)->where('user_id', $userId)->first();
    if (!$order) {
        return redirect()->route('orders.index')->with('error', 'Đơn hàng không tồn tại.');
    }

    // Chỉ cho phép hủy nếu đơn hàng đang ở trạng thái pending (hoặc trạng thái cho phép hủy)
    if ($order->trang_thai !== 'pending' || $order->phuong_thuc_thanh_toan == 'VNPay') {
        return redirect()->route('orders.show', $order->id)
                         ->with('error', 'Đơn hàng này không được phép hủy.');
    }
    

    try {
        DB::transaction(function() use ($order) {
            // Cập nhật trạng thái đơn hàng thành cancelled
            $order->trang_thai = 'cancelled';
            $order->save();

            // Khôi phục số lượng tồn kho cho từng sản phẩm trong đơn hàng
            foreach ($order->orderItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                   // Tính lại hệ số cân nặng dựa vào size nếu sản phẩm bán theo kg
$weightFactor = 1;
if (optional($product->unit)->unit_name === 'kg' && !empty($item->size)) {
    $sizeMapping = [
        '500g' => 0.5,
        '250g' => 0.25,
        '1kg' => 1
    ];
    $weightFactor = $sizeMapping[$item->size] ?? 1; // Mặc định là 1 nếu không khớp
}

                    // Số lượng tồn kho được khôi phục (tính theo khối lượng thực tế)
                    $restoreQuantity = $item->so_luong * $weightFactor;
                    $product->stock_quantity += $restoreQuantity;
                    $product->save();
                }
            }

            // Nếu đơn hàng áp dụng voucher, giảm số lượt sử dụng voucher (nếu quy định cho phép)
            if ($order->voucher_id) {
                $voucher = Voucher::find($order->voucher_id);
                if ($voucher && $voucher->used > 0) {
                    $voucher->decrement('used');
                }
            }
        });

        return redirect()->route('orders.show', $order->id)
                         ->with('success', 'Đơn hàng đã được hủy thành công.');
    } catch (\Exception $e) {
        return redirect()->route('orders.show', $order->id)
                         ->with('error', 'Có lỗi xảy ra khi hủy đơn hàng: ' . $e->getMessage());
    }
}


}

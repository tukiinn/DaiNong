<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
class PaypalController extends Controller
{

    private function createOrder($request)
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

    // Lấy phương thức thanh toán từ form
    $paymentMethod = $request->input('phuong_thuc_thanh_toan');

    // Lấy giỏ hàng từ database
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

      // Xác định hệ số cân nặng dựa vào size (áp dụng cho sản phẩm bán theo kg)
$weightFactor = 1;
if (optional($product->unit)->unit_name === 'kg' && !empty($cart->size)) {
    $sizeMapping = [
        '500g' => 0.5,
        '250g' => 0.25,
        '1kg' => 1
    ];
    $weightFactor = $sizeMapping[$cart->size] ?? 1; // Mặc định là 1 nếu không khớp
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
    $voucherCode = $request->input('voucher_code');

    try {
        DB::transaction(function() use ($request, $validated, $userId, $cartItems, $finalTotal, $voucherCode, $paymentMethod, &$order) {
            // Tạo đơn hàng với trạng thái "pending"
            $order = Order::create([
                'user_id'                => $userId,
                'ten_khach_hang'         => $validated['ten_khach_hang'],
                'so_dien_thoai'          => $validated['so_dien_thoai'],
                'dia_chi'                => $validated['dia_chi'],
                'tong_tien'              => $finalTotal,
                'phuong_thuc_thanh_toan' => $paymentMethod, 
                'trang_thai'             => 'pending',
                'payment_status'         => 'pending',
            ]);

            // Xử lý voucher nếu có (tăng lượt dùng, không lưu voucher_id vào đơn hàng)
            if ($voucherCode) {
                Log::info("Processing voucher trong createOrder", ['voucher_code' => $voucherCode]);
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
                $order->voucher_id = $voucher->id;
                $order->save();
            }

            // Thêm sản phẩm vào đơn hàng và cập nhật số lượng tồn kho
            foreach ($cartItems as $cart) {
                $product = Product::find($cart->product_id);
                if (!$product) {
                    continue;
                }
              // Xác định hệ số cân nặng dựa vào size (chỉ áp dụng cho sản phẩm bán theo kg)
$weightFactor = 1;
if (!empty($cart->size) && $product->unit === 'kg') {
    $sizeMapping = [
        '500g' => 0.5,
        '250g' => 0.25,
        '1kg' => 1
    ];
    $weightFactor = $sizeMapping[$cart->size] ?? 1; // Mặc định là 1 nếu không khớp
}


                // Kiểm tra lại số lượng tồn kho
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
                    'gia'        => $computedPrice,
                    'so_luong'   => $cart->quantity,
                    'size'       => $cart->size,
                    'thanh_tien' => $computedPrice * $cart->quantity,
                ]);
            }
            // Nếu sau khi áp voucher, tổng tiền đơn hàng bằng 0, đánh dấu đơn hàng là "đã thanh toán"
            if ($finalTotal <= 0) {
                $order->payment_status = 'paid';
                $order->save();
            }

            // Xóa giỏ hàng sau khi đơn hàng được tạo thành công
            Cart::where('user_id', $userId)->delete();
        });

        return [
            'order_id'     => $order->id,
            'total_amount' => $order->tong_tien,
        ];
    } catch (\Exception $e) {
        return redirect()->route('cart.index')->with('error', $e->getMessage());
    }
}
    /**
     * Tạo đơn hàng PayPal và chuyển hướng người dùng đến trang thanh toán
     */
    public function createPaypalOrder(Request $request)
    {
        try {
            // Bước 1: Tạo đơn hàng nội bộ
            $internalOrderData = $this->createOrder($request); // Hàm này trả về mảng ['order_id' => ..., 'total_amount' => ...]
            $internalOrderId = $internalOrderData['order_id'];
            $order = Order::find($internalOrderId);
            if (!$order) {
                return redirect()->route('cart.index')->with('error', 'Không tìm thấy đơn hàng nội bộ.');
            }
               // Nếu tổng tiền đơn hàng <= 0, chuyển hướng ngay đến paypal.success để xử lý thanh toán thành công
        if ($order->tong_tien <= 0) {
            return redirect()->route('paypal.success', ['order_id' => $internalOrderId]);
        }
    
            // Bước 2: Gọi API PayPal
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $accessToken = $provider->getAccessToken();
    
            // Quy đổi từ VND sang USD nếu cần (ví dụ: 1 USD = 23,000 VND)
            $final_total_vnd = $order->tong_tien;
            $exchange_rate = 23000;
            $final_total_usd = number_format((float)($final_total_vnd / $exchange_rate), 2, '.', '');
    
            // Xây dựng dữ liệu đơn hàng PayPal, truyền internal order id trong return_url và cancel_url
            $orderData = [
                "intent" => "CAPTURE",
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "USD",
                            "value"         => $final_total_usd
                        ]
                    ]
                ],
                "application_context" => [
                    "return_url" => route('paypal.success', ['order_id' => $internalOrderId]),
                    "cancel_url" => route('paypal.cancel', ['order_id' => $internalOrderId]),
                ]
            ];
    
            $orderResponse = $provider->createOrder($orderData);
            Log::info("PayPal Create Order Response", $orderResponse);
    
            if (isset($orderResponse['id']) && $orderResponse['status'] === 'CREATED') {
                // Bước 3: Cập nhật paypal_order_id vào đơn hàng nội bộ
                $order->update(['paypal_order_id' => $orderResponse['id']]);
    
                // Tìm URL phê duyệt thanh toán PayPal và chuyển hướng khách hàng
                foreach ($orderResponse['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return redirect()->away($link['href']);
                    }
                }
                return redirect()->route('cart.index')->with('error', 'Không tìm thấy URL thanh toán PayPal.');
            }
            
            return redirect()->route('cart.index')->with('error', 'Lỗi khi tạo đơn hàng PayPal.');
        } catch (\Exception $e) {
            Log::error('PayPal Order Creation Error: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Lỗi khi tạo đơn hàng PayPal.');
        }
    }
    

    /**
     * Xử lý khi thanh toán thành công
     */
    public function paypalSuccess(Request $request)
    {
        try {
            $paypalOrderID = $request->query('token');
            $internalOrderId = $request->query('order_id');
            
            // Nếu không có token (có thể do đơn hàng bằng 0) thì tự coi đó là thanh toán thành công
            if (!$paypalOrderID) {
                Log::info("Không có token từ PayPal, đơn hàng có thể bằng 0");
                $order = Order::find($internalOrderId);
                if (!$order) {
                    return redirect()->route('cart.index')->with('error', 'Không tìm thấy đơn hàng tương ứng.');
                }
                $order->payment_status = 'paid';
                $order->save();
                return view('cart.checkout.thankyoupaypal', compact('order'))
                    ->with(['success' => 'Thanh toán PayPal thành công (đơn hàng bằng 0)!', 'order_id' => $order->id]);
            }
            
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $accessToken = $provider->getAccessToken();
        
            // Capture thanh toán từ PayPal
            $result = $provider->capturePaymentOrder($paypalOrderID);
            Log::info("PayPal Capture Order Response", $result);
        
            if (isset($result['status']) && $result['status'] === 'COMPLETED') {
                $order = Order::where('paypal_order_id', $paypalOrderID)->first();
                if (!$order && $internalOrderId) {
                    $order = Order::find($internalOrderId);
                    if ($order) {
                        $order->paypal_order_id = $paypalOrderID;
                    }
                } elseif ($order) {
                    $order->paypal_order_id = $paypalOrderID;
                }
        
                if (!$order) {
                    Log::error("Order not found after PayPal capture", [
                        'paypalOrderID' => $paypalOrderID,
                        'internalOrderId' => $internalOrderId
                    ]);
                    return redirect()->route('cart.index')->with('error', 'Không tìm thấy đơn hàng tương ứng.');
                }
        
                $order->payment_status = 'paid';
                $order->save();
                $order = Order::find($internalOrderId);
        
                return view('cart.checkout.thankyoupaypal', compact('result', 'order'))
                    ->with(['success' => 'Thanh toán PayPal thành công!']);
            }
        
            return redirect()->route('cart.index')->with('error', 'Thanh toán PayPal không thành công.');
        } catch (\Exception $e) {
            Log::error('PayPal Capture Error: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Lỗi khi xử lý thanh toán PayPal.');
        }
    }
    

    /**
     * Xử lý khi người dùng hủy thanh toán PayPal
     */
    public function paypalCancel()
    {
        return redirect()->route('cart.index')->with('error', 'Thanh toán PayPal đã bị hủy.');
    }
    /**
     * Retry thanh toán PayPal cho đơn hàng nội bộ đã tạo (với trạng thái pending).
     * Yêu cầu internal order id được truyền qua request.
     */
    public function retryPaypalOrder(Request $request)
    {
        try {
            $internalOrderId = $request->input('order_id');
            if (!$internalOrderId) {
                return redirect()->route('cart.index')->with('error', 'Không tìm thấy đơn hàng nội bộ cần retry.');
            }
            $order = Order::find($internalOrderId);
            if (!$order) {
                return redirect()->route('cart.index')->with('error', 'Đơn hàng không tồn tại.');
            }
            if ($order->payment_status !== 'pending') {
                return redirect()->route('cart.index')->with('error', 'Đơn hàng không ở trạng thái chờ thanh toán.');
            }

            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $accessToken = $provider->getAccessToken();

            $final_total_vnd = $order->tong_tien;
            $exchange_rate = 23000;
            $final_total_usd = number_format((float)($final_total_vnd / $exchange_rate), 2, '.', '');

            $orderData = [
                "intent" => "CAPTURE",
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "USD",
                            "value"         => $final_total_usd
                        ]
                    ]
                ],
                "application_context" => [
                    "return_url" => route('paypal.success', ['order_id' => $internalOrderId]),
                    "cancel_url" => route('paypal.cancel', ['order_id' => $internalOrderId]),
                ]
            ];

            $orderResponse = $provider->createOrder($orderData);
            Log::info("PayPal Retry Order Response", $orderResponse);

            if (isset($orderResponse['id']) && $orderResponse['status'] === 'CREATED') {
                $order->update(['paypal_order_id' => $orderResponse['id']]);
                foreach ($orderResponse['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return redirect()->away($link['href']);
                    }
                }
                return redirect()->route('cart.index')->with('error', 'Không tìm thấy URL thanh toán PayPal.');
            }
            return redirect()->route('cart.index')->with('error', 'Lỗi khi tạo đơn hàng PayPal.');
        } catch (\Exception $e) {
            Log::error('PayPal Retry Order Error: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Lỗi khi retry thanh toán PayPal.');
        }
    }
}

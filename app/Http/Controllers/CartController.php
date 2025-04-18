<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{

    public function updateQuantity(Request $request)
{
    $productId   = $request->input('id');
    $newQuantity = max(1, intval($request->input('quantity'))); // Không cho xuống dưới 1
    $size        = $request->input('size') ?? ''; // Xử lý trường hợp không có size

    Log::info('Nhận request cập nhật số lượng', [
        'product_id'   => $productId,
        'new_quantity' => $newQuantity,
        'size'         => $size,
        'user'         => Auth::check() ? Auth::id() : 'Guest'
    ]);

    if (Auth::check()) {
        // Người dùng đã đăng nhập -> Cập nhật giỏ hàng trong database
        $user  = Auth::user();
        $query = Cart::where('user_id', $user->id)
                     ->where('product_id', $productId);

        if ($size !== '') {
            $query->where('size', $size);
        } else {
            $query->where(function($q) {
                $q->whereNull('size')
                  ->orWhere('size', '');
            });
        }

        $cartItem = $query->first();
        Log::info('Kết quả truy vấn giỏ hàng', ['cart_item' => $cartItem]);

        if ($cartItem) {
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
            Log::info('Cập nhật số lượng thành công', [
                'product_id'   => $productId,
                'new_quantity' => $cartItem->quantity,
                'size'         => $cartItem->size
            ]);
        } else {
            Log::warning('Sản phẩm không tồn tại trong giỏ hàng', ['product_id' => $productId, 'size' => $size]);
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng']);
        }

        // Lấy toàn bộ giỏ hàng từ database để đồng bộ session
        $cartItems = Cart::where('user_id', $user->id)->get();
        $cart = [];
        foreach ($cartItems as $item) {
            $key = !empty($item->size) ? $item->product_id . '_' . $item->size : $item->product_id;
            $cart[$key] = [
                'so_luong'       => $item->quantity,
                'price'          => $item->product->price,
                'discount_price' => $item->product->discount_price ?? null,
                'size'           => $item->size ?? null,
                'unit'           => optional($item->product->unit)->unit_name,
            ];
        }
        session()->put('cart', $cart);
        Log::info('Đã cập nhật session giỏ hàng', ['cart' => $cart]);
    } else {
        $cart = session()->get('cart', []);
    
        // 🔍 Log dữ liệu giỏ hàng trước khi cập nhật
        Log::info('Dữ liệu giỏ hàng trong session trước khi cập nhật', ['cart' => $cart]);
    
        $key  = !empty($size) ? $productId . '-' . $size : $productId; // Dùng dấu '-' thay vì '_'
        
        // 🔍 Log key đang kiểm tra
        Log::info('Key kiểm tra trong session', ['key' => $key]);
    
        if (isset($cart[$key])) {
            $cart[$key]['so_luong'] = $newQuantity;
            session()->put('cart', $cart);
            Log::info('Cập nhật số lượng trong session', [
                'product_id' => $productId,
                'size'       => $size,
                'quantity'   => $newQuantity
            ]);
        } else {
            Log::warning('Sản phẩm không tồn tại trong session', [
                'product_id' => $productId,
                'size'       => $size,
                'key'        => $key, // Log thêm key để debug
                'available_keys' => array_keys($cart) // Log tất cả key trong session
            ]);
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng']);
        }
    }

    // Tính toán lại tổng giá trị giỏ hàng
    $grandTotal  = 0;
    $sizeMapping = ['500g' => 0.5, '250g' => 0.25, '1kg' => 1];

    foreach ($cart as $item) {
        $basePrice = (isset($item['discount_price']) && $item['discount_price'] > 0)
                     ? $item['discount_price']
                     : $item['price'];
        $weightFactor = 1;
        if (!empty($item['size']) && isset($item['unit']) && $item['unit'] === 'kg') {
            $weightFactor = $sizeMapping[$item['size']] ?? 1;
        }
        $grandTotal += ($basePrice * $weightFactor) * $item['so_luong'];
    }

    // Tính tổng cho dòng sản phẩm cập nhật
    $lineTotal = isset($cart[$key])
        ? ((isset($cart[$key]['discount_price']) && $cart[$key]['discount_price'] > 0)
            ? $cart[$key]['discount_price']
            : $cart[$key]['price']) * $newQuantity
        : 0;

    Log::info('Tính toán lại tổng giá trị giỏ hàng', [
        'line_total' => $lineTotal,
        'grand_total' => $grandTotal
    ]);

    return response()->json([
        'success'             => true,
        'newQuantity'         => $newQuantity,
        'lineTotalFormatted'  => number_format($lineTotal, 0, ',', '.') . ' VND',
        'grandTotalFormatted' => number_format($grandTotal, 0, ',', '.') . ' VND'
    ]);
}

    
    public function updateCart(Request $request, $id)
    {
        try {
            // Lấy thông tin từ request, nếu không có thì mặc định là chuỗi rỗng
            $oldSize = $request->input('old_size', '');
            $newSize = $request->input('size', '');
            $quantity = $request->input('quantity', 1);
    
            if (Auth::check()) {
                $user = Auth::user();
    
                if ($oldSize === '') {
                    // Trường hợp sản phẩm không có size: tìm cart item với trường size là null hoặc rỗng
                    $cartItem = Cart::where('user_id', $user->id)
                                    ->where('product_id', $id)
                                    ->where(function($query) {
                                        $query->whereNull('size')
                                              ->orWhere('size', '');
                                    })->first();
                } else {
                    $cartItem = Cart::where('user_id', $user->id)
                                    ->where('product_id', $id)
                                    ->where('size', $oldSize)
                                    ->first();
                }
    
                if ($cartItem) {
                    // Nếu newSize không được truyền vào (hoặc rỗng), giữ nguyên giá trị hiện có
                    if ($newSize === '') {
                        $newSize = $cartItem->size;
                    }
                    $cartItem->size = $newSize;
                    $cartItem->quantity = $quantity;
                    $cartItem->save();
    
                    return response()->json(['success' => true]);
                }
    
                return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng.']);
            } else {
                // Với giỏ hàng trong session
                $cart = session()->get('cart', []);
    
                // Xác định key cũ dựa trên có size hay không
                if ($oldSize === '') {
                    $oldCartKey = (string)$id;
                } else {
                    $oldCartKey = "$id-$oldSize";
                }
    
                if (isset($cart[$oldCartKey])) {
                    // Lấy dữ liệu của sản phẩm, xoá key cũ
                    $cartItem = $cart[$oldCartKey];
                    unset($cart[$oldCartKey]);
    
                    // Xác định key mới dựa trên newSize: nếu không có newSize, giữ nguyên
                    if ($newSize === '') {
                        $newCartKey = (string)$id;
                    } else {
                        $newCartKey = "$id-$newSize";
                    }
    
                    // Cập nhật size và số lượng
                    $cartItem['size'] = $newSize;
                    $cartItem['so_luong'] = $quantity;
                    $cart[$newCartKey] = $cartItem;
    
                    session()->put('cart', $cart);
    
                    return response()->json(['success' => true]);
                }
    
                return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng.']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Đã xảy ra lỗi khi cập nhật sản phẩm trong giỏ hàng.'
            ], 500);
        }
    }
    
    
    
    
    public function index()
    {
        if (Auth::check()) {
            // Lấy giỏ hàng từ database nếu đã đăng nhập
            $cartItems = Cart::where('user_id', Auth::id())
                ->with('product')
                ->get()
                ->map(function ($cart) {
                    // Nếu discount_price > 0 thì dùng discount_price, ngược lại dùng price
                    $price = ($cart->product->discount_price > 0) 
                        ? $cart->product->discount_price 
                        : $cart->product->price;
                    return [
                        'product_id' => $cart->product->id,
                        'name'       => $cart->product->product_name,
                        'price'      => $price, 
                        'so_luong'   => $cart->quantity,
                        'image'      => $cart->product->image,
                        'unit'       => optional($cart->product->unit)->unit_name,
                        'size'       => $cart->size,
                    ];
                })->toArray();
        } else {
            // Nếu chưa đăng nhập, lấy giỏ hàng từ session
            $cartItems = session('cart', []);
        }
        
        return view('cart.index', compact('cartItems'));
    }
    
 
    public function ajaxCart() {
        if (Auth::check()) {
            // Lấy giỏ hàng từ database theo thứ tự thêm (created_at asc)
            $carts = Cart::where('user_id', Auth::id())
                ->orderBy('created_at', 'asc')
                ->with('product')
                ->get();
    
            // Nhóm theo product_id và size, sau đó sắp xếp theo created_at của phần tử đầu tiên
            $cartItems = $carts->groupBy(function ($cart) {
                    return $cart->product->id . '-' . $cart->size;
                })
                ->sortBy(function ($group) {
                    return $group->first()->created_at;
                })
                ->map(function ($group) {
                    $first = $group->first();
                    // Nếu discount_price > 0 thì dùng nó, ngược lại dùng price
                    $basePrice = ($first->product->discount_price > 0)
                                 ? $first->product->discount_price
                                 : $first->product->price;
                    // Tính giá theo size nếu cần
                    if ($first->size == '500g') {
                        $price = $basePrice / 2;
                    } elseif ($first->size == '250g') {
                        $price = $basePrice / 4;
                    } else {
                        $price = $basePrice;
                    }
                    return [
                        'product_id' => $first->product->id,
                        'name'       => $first->product->product_name,
                        'price'      => $price,
                        'so_luong'   => $group->sum('quantity'),
                        'image'      => $first->product->image,
                        'unit'       => optional($first->product->unit)->unit_name,
                        'size'       => $first->size,
                        'created_at' => $first->created_at, // Dùng created_at để giữ thứ tự
                    ];
                })
                ->values()
                ->toArray();
        } else {
            // Với session, giả sử session('cart') lưu đầy đủ thông tin, bao gồm 'created_at'
            $sessionCart = session('cart', []);
            $cartItems = collect($sessionCart)
                ->groupBy(function ($item) {
                    return $item['product_id'] . '-' . $item['size'];
                })
                // Sắp xếp các nhóm theo 'created_at' của phần tử đầu tiên (nếu có)
                ->sortBy(function ($group) {
                    return $group->first()['created_at'] ?? 0;
                })
                ->map(function ($group) {
                    $first = $group->first();
                    // Nếu discount_price > 0 thì dùng nó, ngược lại dùng price
                    $basePrice = (isset($first['discount_price']) && $first['discount_price'] > 0)
                                 ? $first['discount_price']
                                 : $first['price'];
                    if (isset($first['size'])) {
                        if ($first['size'] == '500g') {
                            $price = $basePrice / 2;
                        } elseif ($first['size'] == '250g') {
                            $price = $basePrice / 4;
                        } else {
                            $price = $basePrice;
                        }
                    } else {
                        $price = $basePrice;
                    }
                    return [
                        'product_id' => $first['product_id'],
                        'name'       => $first['name'],
                        'price'      => $price,
                        'so_luong'   => $group->sum('so_luong'),
                        'image'      => $first['image'],
                        'unit'       => $first['unit'],
                        'size'       => $first['size'],
                        'created_at' => $first['created_at'] ?? null,
                    ];
                })
                ->values()
                ->toArray();
        }
        return view('cart._cart_modal_content', compact('cartItems'));
    }
    
    
    
    
    
    public function checkout()
    {
        // Kiểm tra nếu người dùng chưa đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thanh toán.');
        }
        
        // Lấy id của người dùng đang đăng nhập
        $userId = Auth::id();
        $savedAddresses = ShippingAddress::where('user_id', $userId)->get();
        
        // Lấy giỏ hàng của người dùng, kèm theo thông tin sản phẩm (nếu đã định nghĩa quan hệ 'product')
        $cartItems = Cart::where('user_id', $userId)->with('product')->get();
    
        // Nếu giỏ hàng trống, chuyển hướng về trang giỏ hàng và thông báo lỗi
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống!');
        }
    
        // Tính tổng tiền giỏ hàng, điều chỉnh theo hệ số cân nặng nếu sản phẩm bán theo kg
        $cartTotal = $cartItems->sum(function($cart) {
            // Lấy giá ưu tiên: nếu discount_price > 0 thì dùng discount_price, ngược lại dùng price
            $price = ($cart->product->discount_price > 0) ? $cart->product->discount_price : $cart->product->price;
            $weightFactor = 1;
            
            // Nếu sản phẩm bán theo kg và có thông tin size, tính hệ số cân nặng
            if (optional($cart->product->unit)->unit_name === 'kg' && $cart->size) {
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
            
            return $price * $weightFactor * $cart->quantity;
        });
    
        return view('cart.checkout.payment', compact('cartItems', 'cartTotal', 'savedAddresses'));
    }
    public function addToCart(Request $request, $id)
    {
        try {
            // Lấy số lượng từ input, nếu không có thì mặc định 1
            $quantity = $request->input('quantity', 1);
            
            // Validate số lượng (chỉ validate nếu có input)
            $request->validate([
                'quantity' => 'sometimes|integer|min:1',
            ]);
        
            // Lấy sản phẩm theo ID
            $product = Product::findOrFail($id);
        
            // Nếu sản phẩm có đơn vị "túi", thì không sử dụng size (giá trị rỗng)
            if (optional($product->unit)->unit_name === 'túi') {
                $size = '';
            } else {
                // Xác định các size được phép (1kg, 500g, 250g)
                $allowedSizes = ['1kg', '500g', '250g'];
                $size = $request->input('size', '1kg');
                if (!in_array($size, $allowedSizes)) {
                    $size = '1kg'; // Mặc định là 1kg nếu size không hợp lệ
                }
            }
        
            if (Auth::check()) {
                $user = Auth::user();
        
                // Tìm mục giỏ hàng của user theo product_id và size
                $cartItem = Cart::where('user_id', $user->id)
                                ->where('product_id', $product->id)
                                ->where('size', $size)
                                ->first();
        
                if ($cartItem) {
                    // Nếu sản phẩm cùng size đã có, tăng số lượng theo giá trị từ input
                    $cartItem->quantity += $quantity;
                    $cartItem->save();
                } else {
                    // Nếu chưa có, tạo mới mục trong giỏ hàng
                    Cart::create([
                        'user_id'    => $user->id,
                        'product_id' => $product->id,
                        'quantity'   => $quantity,
                        'size'       => $size,
                    ]);
                }
            } else {
                // Với người dùng chưa đăng nhập, lưu giỏ hàng vào session
                $cart = session()->get('cart', []);
        
                // Sử dụng key kết hợp product_id và size (nếu có) để phân biệt các mục
                $cartKey = $size === '' ? (string)$id : $id . '-' . $size;
        
                if (isset($cart[$cartKey])) {
                    // Nếu mục đã tồn tại, tăng số lượng theo giá trị từ input
                    $cart[$cartKey]['so_luong'] += $quantity;
                } else {
                    // Nếu chưa tồn tại, thêm mới mục với số lượng từ input và lưu size
                    $cart[$cartKey] = [
                        'product_id'  => $product->id,
                        'name'        => $product->product_name,
                        'price'       => ($product->discount_price > 0) ? $product->discount_price : $product->price,
                        'so_luong'    => $quantity,
                        // Lấy tên đơn vị từ quan hệ
                        'unit'        => optional($product->unit)->unit_name,
                        'image'       => $product->image,
                        'description' => $product->description,
                        'category'    => $product->category->category_name,
                        'size'        => $size,
                        'created_at'  => now(), // Lưu thời gian thêm
                    ];
                }
        
                // Cập nhật session giỏ hàng
                session()->put('cart', $cart);
            }
        
            // Render lại giao diện giỏ hàng
            $cartItems = Auth::check()
                ? Cart::where('user_id', Auth::id())->with('product')->get()
                : session('cart', []);
            $cartHtml = view('cart._cart_modal_content', ['cartItems' => $cartItems])->render();
        
            return response()->json([
                'success'  => true,
                'message'  => 'Thêm sản phẩm vào giỏ hàng thành công!',
                'cartHtml' => $cartHtml
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi thêm sản phẩm vào giỏ hàng: ' . $e->getMessage());
        
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng.'
            ], 500);
        }
    }
        
    
    
    
    public function removeFromCart(Request $request, $productId)
{
    // Lấy size từ request (nếu có)
    $size = $request->input('size', null);

    if (Auth::check()) {
        // Xóa sản phẩm khỏi giỏ hàng trong cơ sở dữ liệu
        $query = Cart::where('user_id', Auth::id())
                     ->where('product_id', $productId);
        if ($size) {
            // Nếu có size, chỉ xóa mục có đúng size đó
            $query->where('size', $size);
        }
        $query->delete();
    } else {
        // Xóa sản phẩm khỏi giỏ hàng lưu trong session (chưa đăng nhập)
        $cart = session()->get('cart', []);
        // Nếu có size, key được tạo theo định dạng "productId-size", ngược lại chỉ dùng productId
        $cartKey = $size ? $productId . '-' . $size : $productId;
        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
        }
    }

    return redirect()->route('cart.index')->with('success', 'Xóa sản phẩm khỏi giỏ hàng thành công!');
}

    
    public function removeFromCartAjax(Request $request, $productId)
    {
        // Lấy size từ request (nếu có)
        $size = $request->input('size', null);
    
        if (Auth::check()) {
            // Ở đây, nếu có trường size thì chỉ xóa sản phẩm có product_id và size tương ứng
            $query = Cart::where('user_id', Auth::id())
                         ->where('product_id', $productId);
            if ($size) {
                $query->where('size', $size);
            }
            $query->delete();
    
            // Lấy lại các mục trong giỏ hàng của user
            $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        } else {
            // Lấy giỏ hàng từ session
            $cart = session()->get('cart', []);
    
            // Nếu sản phẩm có trường size, key được tạo theo định dạng: "productId-size"
            $cartKey = $size ? $productId . '-' . $size : $productId;
    
            if (isset($cart[$cartKey])) {
                unset($cart[$cartKey]);
                session()->put('cart', $cart);
            }
    
            // Tạo mảng cartItems
            $cartItems = [];
            foreach ($cart as $key => $details) {
                
                $product = Product::find($details['product_id']);
                if ($product) {
                    $details['product'] = $product;
                    $cartItems[] = $details;
                }
            }
        }
    
        $cartHtml = view('cart._cart_modal_content', compact('cartItems'))->render();
    
        return response()->json(['success' => true, 'cartHtml' => $cartHtml]);
    }
    
    

    public function getCartItemCount()
{
    if (Auth::check()) {
        $totalItems = Cart::where('user_id', Auth::id())->count();
    } else {
        $cart = session()->get('cart', []);
        $totalItems = count($cart);
    }
    
    return response()->json(['totalItems' => $totalItems]);
}


}

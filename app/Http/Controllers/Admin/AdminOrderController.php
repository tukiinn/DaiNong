<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminOrderController extends Controller
{
    // Hiển thị danh sách đơn hàng
    public function index(Request $request)
    {
        $query = Order::query();
    
        // Tìm kiếm theo tên khách hàng, số điện thoại, địa chỉ hoặc mã đơn hàng
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ten_khach_hang', 'like', "%{$search}%")
                  ->orWhere('so_dien_thoai', 'like', "%{$search}%")
                  ->orWhere('dia_chi', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }
    
        // Lọc theo ngày
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }
    
        $orders = $query->orderBy('created_at', 'desc')->paginate(12);
    
        return view('admin.orders.index', [
            'orders'    => $orders,
            'search'    => $request->input('search'),
            'from_date' => $request->input('from_date'),
            'to_date'   => $request->input('to_date'),
        ]);
    }
    
    
    // Hiển thị chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::findOrFail($id);
        $orderItems = OrderItem::where('order_id', $order->id)->get();
        return view('admin.orders.show', compact('order', 'orderItems'));
    }
    
    // Xác nhận đơn hàng (đơn hàng COD hoặc các đơn hàng đang ở trạng thái pending)
    public function confirm(Order $order)
    {
        if ($order->trang_thai !== 'pending') {
            return redirect()->back()->with('error', 'Đơn hàng này không thể xác nhận');
        }
    
        $order->trang_thai = 'confirmed';
        $order->save();
    
        return redirect()->back()->with('success', 'Đơn hàng xác nhận thành công!');
    }
    
    // Giao hàng đơn hàng
    public function ship(Order $order)
    {
        // Kiểm tra trạng thái đơn hàng
        if ($order->trang_thai !== 'confirmed') {
            return redirect()->back()->with('error', 'Đơn hàng này không ở trạng thái "Đã xác nhận", không thể chuyển sang giao hàng!');
        }
    
        // Nếu đơn hàng không phải COD, payment_status phải là "paid"
        if ($order->phuong_thuc_thanh_toan !== 'cod' && $order->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Đơn hàng này chưa được thanh toán, không thể chuyển sang trạng thái giao hàng!');
        }
    
        $order->trang_thai = 'shipping';
        $order->save();
    
        return redirect()->back()->with('success', 'Đơn hàng đã được chuyển sang trạng thái giao hàng.');
    }
    
    
    // Hoàn thành đơn hàng
    public function complete(Order $order)
    {
        if ($order->trang_thai !== 'shipping') {
            return redirect()->back()->with('error', 'Đơn hàng này không thể completed!');
        }
    
        $order->trang_thai = 'completed';
        $order->save();
    
        if ($order->user) {
            $user = $order->user;
            $user->remaining_spins += 1;
            $user->save();
        }
    
        return redirect()->back()->with('success', 'Đơn hàng đã hoàn thành! Người dùng được +1 lượt quay.');
    }
    
    // Hủy đơn hàng
    public function cancel(Order $order)
    {
        if (in_array($order->trang_thai, ['completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Đơn hàng không thể hủy');
        }
    
        $order->trang_thai = 'cancelled';
        $order->save();
    
        return redirect()->back()->with('success', 'Đơn hàng đã được hủy!');
    }
    
    // Bulk Actions
    
    // Bulk Confirm: Xác nhận nhiều đơn hàng cùng lúc (chỉ đơn hàng đang ở trạng thái pending)
    public function bulkConfirm(Request $request)
    {
        $orderIds = $request->input('order_ids');
    
        if (empty($orderIds)) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào được chọn.');
        }
    
        $orders = Order::whereIn('id', $orderIds)
                    ->where('trang_thai', 'pending')
                    ->get();
    
        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào ở trạng thái chờ xác nhận.');
        }
    
        foreach ($orders as $order) {
            $order->trang_thai = 'confirmed';
            $order->save();
        }
    
        return redirect()->back()->with('success', count($orders) . ' đơn hàng đã được xác nhận thành công!');
    }
    
    public function bulkShip(Request $request)
    {
        $orderIds = $request->input('order_ids');
    
        if (empty($orderIds)) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào được chọn.');
        }
    
        $orders = Order::whereIn('id', $orderIds)
                    ->where('trang_thai', 'confirmed')
                    ->where(function($query) {
                        $query->where('phuong_thuc_thanh_toan', 'cod')
                              ->orWhere(function($query) {
                                  $query->where('phuong_thuc_thanh_toan', '!=', 'cod')
                                        ->where('payment_status', 'paid');
                              });
                    })->get();
    
        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào đủ điều kiện chuyển sang trạng thái giao hàng.');
        }
    
        foreach ($orders as $order) {
            $order->trang_thai = 'shipping';
            $order->save();
        }
    
        return redirect()->back()->with('success', count($orders) . ' đơn hàng đã được chuyển sang trạng thái giao hàng.');
    }
    
    
  // Bulk Complete: Hoàn thành nhiều đơn hàng cùng lúc (chỉ đơn hàng ở trạng thái shipping)
public function bulkComplete(Request $request)
{
    $orderIds = $request->input('order_ids');

    if (empty($orderIds)) {
        return redirect()->back()->with('error', 'Không có đơn hàng nào được chọn.');
    }

    $orders = Order::whereIn('id', $orderIds)
                ->where('trang_thai', 'shipping')
                ->get();

    if ($orders->isEmpty()) {
        return redirect()->back()->with('error', 'Không có đơn hàng nào ở trạng thái giao hàng.');
    }

    $count = 0;

    foreach ($orders as $order) {
        $order->trang_thai = 'completed';
        $order->save();
        $count++;

        // Nếu trị giá đơn hàng > 200k thì mới cộng lượt quay
        if ($order->tong_tien > 200000 && $order->user) {
            $order->user->remaining_spins += 1;
            $order->user->save();
        }
    }

    return redirect()->back()->with('success', $count . ' đơn hàng đã được hoàn thành.');
}

    
    // Bulk Cancel: Hủy nhiều đơn hàng cùng lúc (chỉ đơn hàng chưa hoàn thành và chưa bị hủy)
    public function bulkCancel(Request $request)
    {
        $orderIds = $request->input('order_ids');
    
        if (empty($orderIds)) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào được chọn.');
        }
    
        $orders = Order::whereIn('id', $orderIds)
                    ->whereNotIn('trang_thai', ['completed', 'cancelled'])
                    ->get();
    
        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào đủ điều kiện hủy.');
        }
    
        foreach ($orders as $order) {
            $order->trang_thai = 'cancelled';
            $order->save();
        }
    
        return redirect()->back()->with('success', count($orders) . ' đơn hàng đã được hủy.');
    }
    
}

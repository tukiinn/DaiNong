<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel; // Import Excel facade
use App\Exports\DashboardExport;
use App\Exports\RevenueExport;


class StatisticsController extends Controller
{
    // Hàm tính toán thống kê hiển thị trên dashboard
    public function dashboard()
    {
        // Tổng số đơn hàng
        $totalOrders = Order::count();
    
        // Tổng số khách hàng riêng biệt
        $totalCustomers = User::whereHas('orders')->count();
    
        // Tổng số đơn hàng chưa hoàn thành (dựa trên cột trang_thai)
        $totalPendingOrders = Order::whereIn('trang_thai', ['pending', 'paid', 'confirmed'])->count();
    
        // Tổng số đơn hàng đã hoàn thành
        $totalCompletedOrders = Order::where('trang_thai', 'completed')->count();
    
        // Tổng số đơn hàng đã hủy
        $totalCancelledOrders = Order::where('trang_thai', 'cancelled')->count();
    
        // Doanh thu theo danh mục cho đơn hàng đã hoàn thành
        $revenueByCategoryCompleted = $this->getCategoryRevenueForChart();
    
        // Tổng doanh thu cho đơn hàng chưa hoàn thành (tính từ order_items.thanh_tien)
        $totalRevenuePending = OrderItem::whereIn('order_id', Order::whereIn('trang_thai', ['pending', 'paid', 'confirmed'])->pluck('id'))
            ->sum('thanh_tien');
    
        // Doanh thu theo ngày cho đơn hàng đã hoàn thành
        $revenueByDayCompleted = OrderItem::select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('SUM(thanh_tien) as revenue')
            )
            ->whereIn('order_id', Order::where('trang_thai', 'completed')->pluck('id'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
    
        // Doanh thu theo tháng cho đơn hàng đã hoàn thành
        $revenueByMonthCompleted = OrderItem::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(thanh_tien) as revenue')
            )
            ->whereIn('order_id', Order::where('trang_thai', 'completed')->pluck('id'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();
    
        // Doanh thu theo năm cho đơn hàng đã hoàn thành
        $revenueByYearCompleted = OrderItem::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(thanh_tien) as revenue')
            )
            ->whereIn('order_id', Order::where('trang_thai', 'completed')->pluck('id'))
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->get();
    
        /* ---------------------- Tính Doanh Thu & Voucher ---------------------- */
        // Lấy tất cả các đơn hàng đã hoàn thành kèm theo orderItems
        $orders = Order::with('orderItems')->where('trang_thai', 'completed')->get();
    
        // Khởi tạo 3 biến tính toán dựa trên cùng một nguồn dữ liệu đơn hàng
        $totalRevenueCompleted = 0;      // Tổng doanh thu gốc (từ order_items.thanh_tien)
        $totalNetRevenueReceived = 0;    // Doanh thu thực nhận (orders.tong_tien sau voucher và các điều chỉnh)
        $totalVoucherDiscount = 0;       // Tổng số tiền voucher đã giảm
    
        foreach ($orders as $order) {
            // Tổng giá trị các mặt hàng trong đơn hàng
            $orderItemsTotal = $order->orderItems->sum('thanh_tien');
    
            // Cộng dồn doanh thu gốc và doanh thu thực nhận
            $totalRevenueCompleted += $orderItemsTotal;
            $totalNetRevenueReceived += $order->tong_tien;
            
            // Nếu đơn hàng có voucher, tính hiệu số (số tiền voucher đã giảm)
            if (!is_null($order->voucher_id)) {
                $totalVoucherDiscount += ($orderItemsTotal - $order->tong_tien);
            }
        }
        // Lưu ý: Nếu không có phí khác (như vận chuyển, thuế...) thì:
        // totalRevenueCompleted = totalNetRevenueReceived + totalVoucherDiscount
    
        // Lấy danh sách danh mục
        $categories = Category::all();
    
        /* ---------------------- Tính Lợi Nhuận ---------------------- */
        $totalImportCost = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('order_items.order_id', Order::where('trang_thai', 'completed')->pluck('id'))
            ->selectRaw('SUM(products.import_price * order_items.so_luong * 
                CASE 
                    WHEN order_items.size = "1kg" THEN 1
                    WHEN order_items.size = "500g" THEN 0.5
                    WHEN order_items.size = "250g" THEN 0.25
                    ELSE 0
                END) as total_import_cost')
            ->value('total_import_cost');
    
        $totalProfit = $totalNetRevenueReceived - $totalImportCost;
    
        /* ---------------------- Thống Kê Sản Phẩm Đã Bán ---------------------- */
        $soldProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.product_name as name',
                'products.image as image',
                DB::raw('SUM(order_items.so_luong * CASE 
                            WHEN order_items.size = "1kg" THEN 1
                            WHEN order_items.size = "500g" THEN 0.5
                            WHEN order_items.size = "250g" THEN 0.25
                            ELSE 0 
                        END) as sold_quantity'),
                DB::raw('SUM(order_items.thanh_tien) as revenue')
            )
            ->groupBy('products.id', 'products.product_name', 'products.image')
            ->get();
    
        // Tổng số lượng sản phẩm đã bán và tổng doanh thu từ các sản phẩm đã bán
        $totalSoldQuantity = $soldProducts->sum('sold_quantity');
        $totalSoldRevenue = $soldProducts->sum('revenue');
    
        /* ---------------------- Thống Kê Khách Hàng Thân Thiết ---------------------- */
        $loyalCustomers = User::select(
                'users.id', 
                'users.name', 
                'users.email',
                'users.avatar',
                DB::raw('COUNT(orders.id) as order_count'), 
                DB::raw('SUM(orders.tong_tien) as total_spent')
            )
            ->join('orders', 'orders.user_id', '=', 'users.id')
            ->where('orders.trang_thai', 'completed')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.avatar')
            ->orderByDesc('total_spent')
            ->get();
    
        return view('admin.dashboard', compact(
            'totalOrders',
            'totalCustomers',
            'totalPendingOrders',
            'totalCompletedOrders',
            'totalCancelledOrders',
            'revenueByCategoryCompleted',
            'revenueByDayCompleted',
            'revenueByMonthCompleted',
            'revenueByYearCompleted',
            'totalRevenueCompleted',       // Tổng doanh thu gốc (order_items)
            'totalRevenuePending',
            'totalVoucherDiscount',        // Số tiền voucher giảm
            'categories',
            'totalNetRevenueReceived',     // Doanh thu thực nhận (orders.tong_tien)
            'soldProducts',
            'totalSoldQuantity',
            'totalSoldRevenue',
            'loyalCustomers',
            'totalImportCost',             // Tổng giá nhập hàng
            'totalProfit'                  // Tổng lợi nhuận
        ));
    }
    

    // Hàm lấy dữ liệu doanh thu theo thời gian (cho biểu đồ AJAX)
    public function getRevenueData(Request $request)
    {
        $timeframe = $request->query('timeframe', 'day');

        $data = $this->getRevenueByTimeframe($timeframe);

        return response()->json([
            'completed' => $data['completed'],
            'pending'   => $data['pending'],
        ]);
    }

    private function getRevenueByTimeframe($timeframe)
    {
        switch ($timeframe) {
            case 'day':
                return [
                    'completed' => OrderItem::selectRaw('DATE(created_at) as label, SUM(thanh_tien) as revenue')
                        ->whereIn('order_id', Order::where('trang_thai', 'completed')->pluck('id'))
                        ->groupBy('label')
                        ->get(),
                    'pending' => OrderItem::selectRaw('DATE(created_at) as label, SUM(thanh_tien) as revenue')
                        ->whereIn('order_id', Order::whereIn('trang_thai', ['pending', 'paid', 'confirmed'])->pluck('id'))
                        ->groupBy('label')
                        ->get(),
                ];
            case 'month':
                return [
                    'completed' => OrderItem::selectRaw('MONTH(created_at) as label, SUM(thanh_tien) as revenue')
                        ->whereIn('order_id', Order::where('trang_thai', 'completed')->pluck('id'))
                        ->groupBy('label')
                        ->get(),
                    'pending' => OrderItem::selectRaw('MONTH(created_at) as label, SUM(thanh_tien) as revenue')
                        ->whereIn('order_id', Order::whereIn('trang_thai', ['pending', 'paid', 'confirmed'])->pluck('id'))
                        ->groupBy('label')
                        ->get(),
                ];
            case 'year':
                return [
                    'completed' => OrderItem::selectRaw('YEAR(created_at) as label, SUM(thanh_tien) as revenue')
                        ->whereIn('order_id', Order::where('trang_thai', 'completed')->pluck('id'))
                        ->groupBy('label')
                        ->get(),
                    'pending' => OrderItem::selectRaw('YEAR(created_at) as label, SUM(thanh_tien) as revenue')
                        ->whereIn('order_id', Order::whereIn('trang_thai', ['pending', 'paid', 'confirmed'])->pluck('id'))
                        ->groupBy('label')
                        ->get(),
                ];
            default:
                return [];
        }
    }

    // Lấy doanh thu theo danh mục (sử dụng bảng order_items với các trường: id, order_id, product_id, name, gia, so_luong, size, thanh_tien, created_at, updated_at)
    public function getCategoryRevenueForChart()
    {
        return Product::join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.trang_thai', 'completed') // Chỉ lấy đơn hàng đã hoàn thành
            ->select(
                'categories.category_name as category_name',
                DB::raw('SUM(order_items.thanh_tien) as revenue')
            )
            ->groupBy('categories.category_name')
            ->get();
    }
    
    public function getPaymentMethodRevenueData()
    {
        $paymentData = Order::select(
                DB::raw("COALESCE(phuong_thuc_thanh_toan, 'Unknown') as phuong_thuc_thanh_toan"),
                DB::raw('SUM(tong_tien) as revenue')
            )
            ->where('trang_thai', 'completed')
            ->groupBy(DB::raw("COALESCE(phuong_thuc_thanh_toan, 'Unknown')"))
            ->get();
    
        return response()->json($paymentData);
    }
    public function exportExcel()
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $fileName = "thong-ke-don-hang_{$timestamp}.xlsx";
    
        return Excel::download(new DashboardExport, $fileName);
    }
    
    public function exportRevenue()
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $fileName = "thong-ke-doanh-thu_{$timestamp}.xlsx";
    
        return Excel::download(new RevenueExport, $fileName);
    }
    
       
}

<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RevenueExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                DB::raw('DATE(order_items.created_at) as date'),
                DB::raw('SUM(order_items.thanh_tien) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(order_items.so_luong) as total_quantity'),
                DB::raw('SUM(CASE WHEN orders.trang_thai = "completed" THEN order_items.thanh_tien ELSE 0 END) as completed_revenue'),
                DB::raw('SUM(CASE WHEN orders.trang_thai = "cancelled" THEN order_items.thanh_tien ELSE 0 END) as cancelled_revenue'),
                // Tính tổng giá nhập hàng với quy đổi size: 250g=0.25, 500g=0.5, còn lại nhân nguyên
                DB::raw('SUM(
                    CASE 
                        WHEN order_items.size = "250g" THEN products.import_price * order_items.so_luong * 0.25
                        WHEN order_items.size = "500g" THEN products.import_price * order_items.so_luong * 0.5
                        ELSE products.import_price * order_items.so_luong
                    END
                ) as total_import_cost'),
                // Lợi nhuận = doanh thu hoàn thành - tổng giá nhập hàng
                DB::raw('(SUM(CASE WHEN orders.trang_thai = "completed" THEN order_items.thanh_tien ELSE 0 END)
                    - SUM(
                        CASE 
                            WHEN order_items.size = "250g" THEN products.import_price * order_items.so_luong * 0.25
                            WHEN order_items.size = "500g" THEN products.import_price * order_items.so_luong * 0.5
                            ELSE products.import_price * order_items.so_luong
                        END
                    )) as total_profit')
            )
            ->groupBy(DB::raw('DATE(order_items.created_at)'))
            ->orderBy('date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Ngày',
            'Tổng doanh thu',
            'Tổng số đơn hàng',
            'Tổng số lượng sản phẩm bán',
            'Doanh thu hoàn thành',
            'Doanh thu bị hủy',
            'Tổng giá nhập hàng',
            'Tổng lợi nhuận'
        ];
    }

    public function map($row): array
    {
        return [
            $row->date,
            $row->total_revenue,
            $row->total_orders,
            $row->total_quantity,
            $row->completed_revenue,
            $row->cancelled_revenue,
            $row->total_import_cost,
            $row->total_profit
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DashboardExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Order::with('user')->get(); // Lấy tất cả đơn hàng
    }
    

    public function headings(): array
    {
        return [
            'ID',
            'User ID',
            'Tên khách hàng',
            'Số điện thoại',
            'Địa chỉ',
            'Tổng tiền',
            'Phương thức thanh toán',
            'Trạng thái đơn',
            'Trạng thái thanh toán',
            'Voucher ID',
            'Created At',
            'Updated At'
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->user_id,
            $order->ten_khach_hang, // Tránh lỗi nếu user bị xóa
            $order->so_dien_thoai,
            $order->dia_chi,
            $order->tong_tien,
            $order->phuong_thuc_thanh_toan,
            $order->trang_thai,
            $order->payment_status,
            $order->voucher_id,
            $order->created_at,
            $order->updated_at
        ];
    }
}

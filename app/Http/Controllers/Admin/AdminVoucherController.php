<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminVoucherController extends Controller
{
    
    public function store(Request $request)
{
    // Kiểm tra xem ngày bắt đầu và ngày kết thúc có hợp lệ hay không
    $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d H:i:s') : null;
    $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d H:i:s') : null;

    // Lưu vào cơ sở dữ liệu
    Voucher::create([
        'code' => $request->code,
        'discount' => $request->discount,
        'type' => $request->type,
        'max_usage' => $request->max_usage,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);

    return redirect()->route('admin.vouchers.index')->with('success', 'Voucher đã được tạo thành công!');
}

    // Hiển thị danh sách voucher
    public function index(Request $request)
    {
        $query = Voucher::query();
    
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }
        
    
        $vouchers = $query->orderBy('created_at', 'desc')->paginate(10);
    
        return view('admin.vouchers.index', compact('vouchers'));
    }
    
    

    // Hiển thị form tạo voucher
    public function create()
    {
        return view('admin.vouchers.create');
    }



    // Hiển thị form chỉnh sửa voucher
    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    // Cập nhật thông tin voucher
    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'discount' => 'required|numeric',
            'type' => 'required|in:percentage,fixed',
            'max_usage' => 'required|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $voucher->update($request->all());

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher đã được cập nhật!');
    }

    // Xóa voucher
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher đã được xóa!');
    }
    public function deleteExpiredOrUsedVouchers()
{
    $now = Carbon::now();

    $deleted = Voucher::where(function ($query) use ($now) {
        $query->whereNotNull('end_date')
              ->where('end_date', '<', $now);
    })->orWhereColumn('used', '>=', 'max_usage')->delete();

    return redirect()->route('admin.vouchers.index')->with('success', "{$deleted} voucher đã bị xóa do hết hạn hoặc đã dùng hết lượt.");
}
public function bulkAction(Request $request)
{
    $ids = $request->input('selected_vouchers', []);
    
    if (!empty($ids)) {
        $deletedCount = Voucher::whereIn('id', $ids)->delete();
        
        $message = $deletedCount === 1 
            ? 'Đã xóa voucher này.' 
            : "Đã xóa {$deletedCount} voucher.";

        return redirect()->route('admin.vouchers.index')->with('success', $message);
    }

    return redirect()->route('admin.vouchers.index')->with('error', 'Không có voucher nào được chọn.');
}



}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class AdminSupplierController extends Controller
{
    // Hiển thị danh sách nhà cung cấp
    public function index(Request $request)
    {
        $query = Supplier::query();
    
        // Nếu có tìm kiếm
        if ($search = $request->input('search')) {
            $query->where('supplier_name', 'like', "%$search%");
        }
    
        // Phân trang 10 nhà cung cấp mỗi trang
        $suppliers = $query->paginate(10);
    
        return view('admin.suppliers.index', compact('suppliers'));
    }
    

    // Trang tạo mới nhà cung cấp
    public function create()
    {
        return view('admin.suppliers.create');
    }

    // Lưu nhà cung cấp mới vào cơ sở dữ liệu
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_info'  => 'nullable|string|max:255',
            'address'       => 'nullable|string|max:255',
        ]);

        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')->with('success', 'Nhà cung cấp được thêm thành công.');
    }

    // Hiển thị chi tiết một nhà cung cấp (nếu cần)
    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.suppliers.show', compact('supplier'));
    }

    // Trang chỉnh sửa nhà cung cấp
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.suppliers.edit', compact('supplier'));
    }

    // Cập nhật thông tin nhà cung cấp
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_info'  => 'nullable|string|max:255',
            'address'       => 'nullable|string|max:255',
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')->with('success', 'Cập nhật nhà cung cấp thành công.');
    }

    // Xóa nhà cung cấp
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return redirect()->route('admin.suppliers.index')->with('success', 'Nhà cung cấp đã được xóa.');
    }
}

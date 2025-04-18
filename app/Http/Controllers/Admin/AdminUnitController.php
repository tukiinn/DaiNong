<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;

class AdminUnitController extends Controller
{
    // Hiển thị danh sách đơn vị
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Unit::query();
    
        if ($search) {
            $query->where('unit_name', 'LIKE', "%{$search}%");
        }
    
        $units = $query->paginate(10);
    
        return view('admin.units.index', compact('units'));
    }
    

    // Trang tạo mới đơn vị
    public function create()
    {
        return view('admin.units.create');
    }

    // Lưu đơn vị mới vào cơ sở dữ liệu
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_name' => 'required|string|max:255',
        ]);

        Unit::create($validated);

        return redirect()->route('admin.units.index')->with('success', 'Đơn vị được thêm thành công.');
    }

    // Hiển thị chi tiết một đơn vị (nếu cần)
    public function show($id)
    {
        $unit = Unit::findOrFail($id);
        return view('admin.units.show', compact('unit'));
    }

    // Trang chỉnh sửa đơn vị
    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        return view('admin.units.edit', compact('unit'));
    }

    // Cập nhật thông tin đơn vị
    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $validated = $request->validate([
            'unit_name' => 'required|string|max:255',
        ]);

        $unit->update($validated);

        return redirect()->route('admin.units.index')->with('success', 'Đơn vị được cập nhật thành công.');
    }

    // Xóa đơn vị
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();
        return redirect()->route('admin.units.index')->with('success', 'Đơn vị đã được xóa.');
    }
}

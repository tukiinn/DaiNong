<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Hiển thị danh sách nhân viên, hỗ trợ tìm kiếm và phân trang
    public function index(Request $request)
    {
        // Chỉ lấy các user có role là 'admin'
        $query = User::where('role', 'admin');
    
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
    
        $users = $query->paginate(10);
        return view('admin.users.index', compact('users'));
    }
    

    // Hiển thị form tạo nhân viên mới
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    // Lưu nhân viên mới
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required' // Role để phân quyền (dùng assignRole)
        ]);
    
        try {
            $data = $request->only(['name', 'email']);
            $data['password'] = bcrypt($request->password);
            
            // Gán role mặc định "admin" vào cột role trong bảng users
            $data['role'] = 'admin';
    
            $user = User::create($data);
    
            // Gán quyền (role) theo lựa chọn của user (dùng assignRole)
            $user->assignRole($request->role);
    
            return redirect()->route('admin.users.index')
                             ->with('success', 'Nhân viên đã được tạo thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                             ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    


    // Hiển thị form chỉnh sửa nhân viên
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    // Cập nhật thông tin nhân viên
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|min:6|confirmed',
            'role'     => 'required'
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        // Đồng bộ role mới
        $user->syncRoles($request->role);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Nhân viên đã được cập nhật thành công.');
    }

    // Xóa nhân viên
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')
                         ->with('success', 'Nhân viên đã được xóa thành công.');
    }
}

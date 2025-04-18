<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Xóa cache permission để đảm bảo thay đổi mới được áp dụng
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Định nghĩa danh sách các permission cần thiết
        $permissions = [
            'manage products',
            'manage categories',
            'manage units',
            'manage suppliers',
            'manage reviews',
            'manage chat',
            'manage news',
            'manage vouchers',
            'view dashboard',
        ];

        // Tạo các permission nếu chưa tồn tại
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Tạo role admin và manager với toàn quyền (toàn bộ permission)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo(Permission::all());

        // Role web staff: chỉ có quyền liên quan đến giao diện và tương tác với khách hàng
        $webStaff = Role::firstOrCreate(['name' => 'web staff']);
        $webStaff->givePermissionTo([
            'manage products',      // Nếu cho phép quản lý sản phẩm trên giao diện
            'manage categories',
            'manage reviews',
            'manage chat',
            'manage news'
        ]);

        // Role accountant: chỉ có quyền liên quan đến tài chính
        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $accountant->givePermissionTo([
            'manage vouchers',
            'view dashboard'
        ]);
    }
}

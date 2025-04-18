@extends('layouts.admin')

@section('content')


<div class="container">
 <!-- Breadcrumb -->
 <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Quản lý Nhân Viên</li>
    </ol>
</nav>
@php
    // Mảng ánh xạ role tiếng Anh sang tiếng Việt
    $roleTranslations = [
        'admin'       => 'Quản trị viên',
        'manager'     => 'Quản lý',
        'web staff'   => 'Nhân viên trực page',
        'accountant'  => 'Nhân viên kế toán',
    ];
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.users.create') }}" class="btn btn-add mb-3">
        <i class="fa-solid fa-plus"></i> Thêm nhân viên
    </a>

    <!-- Form tìm kiếm -->
    <form action="{{ route('admin.users.index') }}" method="GET" class="search-form mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm nhân viên..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-search">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm
            </button>
        </div>
    </form>
</div>

<table class="table modern-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Vai trò</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>#{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @php
                        $translatedRoles = [];
                        foreach($user->getRoleNames() as $role){
                            $roleLower = strtolower($role);
                            $translatedRoles[] = $roleTranslations[$roleLower] ?? ucfirst($role);
                        }
                        echo implode(', ', $translatedRoles);
                    @endphp
                </td>
                <td>
                    @php
                        $currentUser = auth()->user();
                        $isSelf = $currentUser->id === $user->id;
                        $currentUserIsManager = $currentUser->hasRole('manager');
                        $targetIsAdmin = $user->hasRole('admin');
                        $targetIsManager = $user->hasRole('manager');
                
                        // Manager không được sửa admin, manager khác, và không được xóa chính mình
                        $canEdit = true;
                        $canDelete = true;
                
                        if ($currentUserIsManager) {
                            if ($isSelf) {
                                $canDelete = false; // Không tự xóa mình
                                $canEdit = true;    // Có thể sửa bản thân (trừ vai trò, đã xử lý ở trang edit)
                            } elseif ($targetIsAdmin || $targetIsManager) {
                                $canEdit = false;
                                $canDelete = false;
                            }
                        }
                    @endphp
                
                    @if($canEdit)
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-icon btn-edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    @endif
                
                    @if($canDelete)
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-icon btn-delete" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    @endif
                </td>
                
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Phân trang -->
<div class="pagination-wrapper">
    {{ $users->links() }}
</div>

<!-- Inline CSS Styles -->
<style>
    /* Container */
    .container {
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    /* Breadcrumb */
    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
    .breadcrumb-item a {
        text-decoration: none;
        color: #81c784;
    }
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #6c757d;
    }

    /* Tiêu đề */
    h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #343a40;
    }

    /* Nút Thêm Voucher */
    .btn-add {
        background-color: #81c784;
        border: none;
        color: #fff;
        padding: 10px 20px;
        font-size: 1rem;
        border-radius: 50px;
        transition: background-color 0.3s ease, transform 0.3s ease;
        text-decoration: none;
    }
    .btn-add:hover {
        background-color: #689f65;
        transform: translateY(-2px);
    }

    /* Form Tìm Kiếm Voucher */
    .search-form .input-group {
        display: flex;
        align-items: center;
    }
    .search-form .input-group .form-control {
        min-width: 250px;
        border: 1px solid #81c784;
        border-right: none;
        border-radius: 4px 0 0 4px;
        padding: 10px;
    }
    .search-form .input-group .btn {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        background-color: #81c784;
        border: 1px solid #81c784;
        color: #fff;
        padding: 10px 20px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .search-form .input-group .btn:hover {
        background-color: #689f65;
     
    }

    /* Bảng Voucher */
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .modern-table thead {
        background-color: #81c784;
        color: #fff;
    }
    .modern-table th, 
    .modern-table td {
        padding: 15px;
        text-align: center;
        vertical-align: middle;
    }
    .modern-table tbody tr:nth-of-type(odd) {
        background-color: rgba(129,199,132,0.1);
    }
    .modern-table tbody tr:hover {
        background-color: rgba(104,159,101,0.15);
        cursor: pointer;
    }

    /* Nút hành động dạng icon */
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 50%;
        color: #fff;
        margin: 0 2px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .btn-icon:hover {
        transform: scale(1.1);
    }
    /* Nút chỉnh sửa: màu vàng nhạt */
    .btn-edit {
        background-color: #ffc107;
    }
    .btn-edit:hover {
        background-color: #e0a800;
    }
    /* Nút xóa: màu đỏ */
    .btn-delete {
        background-color: #dc3545;
    }
    .btn-delete:hover {
        background-color: #c82333;
    }

    /* Phân trang */
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding-left: 0;
        margin-top: 20px;
    }
    .pagination li {
        margin: 0 5px;
    }
    .pagination li a, 
    .pagination li span {
        color: #81c784;
        border: 1px solid #81c784;
        padding: 8px 12px;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .pagination li a:hover, 
    .pagination li span:hover {
        background-color: #81c784;
        color: #fff;
    }
    .pagination li.active span {
        background-color: #81c784;
        color: #fff;
        border-color: #81c784;
    }
</style>
@endsection

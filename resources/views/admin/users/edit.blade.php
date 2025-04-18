@extends('layouts.admin')

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Nhân viên</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa Nhân Viên</li>
        </ol>
    </nav>

    <h1 class="text-center">Chỉnh sửa Nhân Viên</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @php
        $roleTranslations = [
            'admin'       => 'Quản trị viên',
            'manager'     => 'Quản lý',
            'web staff'   => 'Nhân viên trực page',
            'accountant'  => 'Nhân viên kế toán',
        ];
    @endphp

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Tên -->
        <div class="form-group mb-3">
            <label for="name">Tên</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Mật khẩu -->
        <div class="form-group mb-3">
            <label for="password">Mật khẩu (để trống nếu không muốn thay đổi)</label>
            <input type="password" name="password" class="form-control">
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Xác nhận mật khẩu -->
        <div class="form-group mb-3">
            <label for="password_confirmation">Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        @php
        $isEditingSelf = auth()->id() === $user->id;
        $currentUserIsManager = auth()->user()->hasRole('manager');
    @endphp
    
    @if(!($isEditingSelf && ($user->hasRole('admin') || $user->hasRole('manager'))))
        <div class="form-group mb-3">
            <label for="role">Vai trò</label>
            <select name="role" class="form-control" required>
                <option value="">Chọn vai trò</option>
                @foreach($roles as $role)
                    @php
                        $roleKey = strtolower($role->name);
                        // Nếu người dùng là manager, thì không cho hiện admin và manager trong dropdown
                        $isRestricted = $currentUserIsManager && in_array($role->name, ['admin', 'manager']);
                    @endphp
    
                    @if(!$isRestricted)
                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                            {{ $roleTranslations[$roleKey] ?? ucfirst($role->name) }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
    @endif
    

        <button type="submit" class="btn  btn-add">Cập nhật nhân viên</button>
    </form>
</div>
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

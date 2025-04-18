@extends('layouts.admin')

@section('content')
<div class="container">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.users.index') }}">Quản lý Nhân Viên</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Thêm Nhân Viên</li>
        </ol>
    </nav>

    <h1 class="mb-4">Thêm Nhân Viên</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label for="name">Tên</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="password">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="password_confirmation">Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        @php
        $currentUser = Auth::user();
        $roleTranslations = [
            'admin'       => 'Quản trị viên',
            'manager'     => 'Quản lý',
            'web staff'   => 'Nhân viên trực page',
            'accountant'  => 'Nhân viên kế toán',
        ];
    
        // Kiểm tra xem người dùng hiện tại có phải là manager không
        $isManager = $currentUser->hasRole('manager');
    @endphp
    
    <!-- Vai trò -->
    <div class="form-group mb-4">
        <label for="role">Vai trò</label>
        <select name="role" class="form-control" required>
            <option value="">Chọn vai trò</option>
            @foreach($roles as $role)
                @php 
                    $roleKey = strtolower($role->name); 
                    // Nếu là manager, không cho chọn admin hoặc manager
                    $isRestricted = $isManager && in_array($roleKey, ['admin', 'manager']);
                @endphp
    
                @if(!$isRestricted)
                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                        {{ $roleTranslations[$roleKey] ?? ucfirst($role->name) }}
                    </option>
                @endif
            @endforeach
        </select>
        @error('role') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    
    
        <button type="submit" class="btn btn-add">Tạo nhân viên</button>
    </form>

    <style>
        .container {
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

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

        h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #343a40;
        }

        .form-group label {
            font-weight: 500;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #81c784;
            box-shadow: 0 0 0 0.2rem rgba(129,199,132,.25);
        }

        .btn-add {
            background-color: #81c784;
            border: none;
            color: #fff;
            padding: 10px 24px;
            font-size: 1rem;
            border-radius: 50px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-add:hover {
            background-color: #689f65;
            transform: translateY(-2px);
        }
    </style>
</div>
@endsection

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
                    <a href="{{ route('admin.products.index') }}">Sản phẩm</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.units.index') }}">Đơn vị</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa đơn vị</li>
            </ol>
        </nav>
    <h1 class="text-center">Chỉnh sửa Đơn Vị</h1>
    <form action="{{ route('admin.units.update', $unit->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="unit_name" class="form-label">Tên Đơn Vị</label>
            <input type="text" class="form-control @error('unit_name') is-invalid @enderror" id="unit_name" name="unit_name" value="{{ old('unit_name', $unit->unit_name) }}" required>
            @error('unit_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật Đơn Vị</button>
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

    /* Tiêu đề */
    h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #343a40;
    }

    /* Nút thêm */
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

    /* Form tìm kiếm */
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
    .search-form .input-group .btn-search {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        background-color: #81c784;
        border: 1px solid #81c784;
        color: #fff;
        padding: 10px 20px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .search-form .input-group .btn-search:hover {
        background-color: #689f65;
    }

    /* Bảng */
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
    .btn-edit {
        background-color: #ffc107;
    }
    .btn-edit:hover {
        background-color: #e0a800;
    }
    .btn-delete {
        background-color: #dc3545;
    }
    .btn-delete:hover {
        background-color: #c82333;
    }

    /* Phân trang */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
</style>
@endsection

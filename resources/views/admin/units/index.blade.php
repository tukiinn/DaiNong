@extends('layouts.admin')

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page">Danh sách Đơn Vị</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Nút thêm đơn vị -->
        <a href="{{ route('admin.units.create') }}" class="btn btn-add">
            <i class="fa-solid fa-plus"></i> Thêm Đơn Vị
        </a>

        <!-- Form tìm kiếm -->
        <form action="{{ route('admin.units.index') }}" method="GET" class="search-form">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm đơn vị..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-search">
                    <i class="fa-solid fa-magnifying-glass"></i> Tìm
                </button>
            </div>
        </form>
    </div>

    @if(session('success'))
       <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table modern-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Đơn Vị</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($units as $unit)
                <tr>
                    <td>{{ $unit->id }}</td>
                    <td>{{ $unit->unit_name }}</td>
                    <td>
                        <a href="{{ route('admin.units.edit', $unit->id) }}" class="btn btn-icon btn-edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('admin.units.destroy', $unit->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-icon btn-delete" onclick="return confirm('Bạn có chắc muốn xóa đơn vị này?')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Phân trang -->
    <div class="pagination-wrapper">
        {{ $units->links() }}
    </div>
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

    /* Nút hành động */
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

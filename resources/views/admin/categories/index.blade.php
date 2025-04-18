@extends('layouts.admin')

@section('content')
<div class="container p-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Danh sách danh mục</li>
        </ol>
    </nav>
    <!-- End Breadcrumb -->

    <!-- Tiêu đề trang và form tìm kiếm -->
    <div class="d-flex justify-content-between align-items-center mb-4">
          <!-- Nút Thêm danh mục -->
    <div class="mb-3 text-center">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-add">
            <i class="fas fa-plus"></i> Thêm danh mục
        </a>
    </div>

        <!-- Form tìm kiếm danh mục -->
        <form action="{{ route('admin.categories.index') }}" method="GET" class="form-inline">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm danh mục" value="{{ request('search') }}">
                <button type="submit" class="btn btn-search">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

 
    <!-- Bảng danh sách danh mục -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Hình ảnh</th>
                    <th>Tên danh mục</th>
                    <th>Mô tả</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <!-- Thẻ bao cho ảnh với kích thước cố định -->
                        <div class="table-img-wrapper">
                            <img src="{{ $category->image ? asset($category->image) : 'https://via.placeholder.com/100' }}" 
                                 alt="{{ $category->category_name }}">
                        </div>
                    </td>
                    <td>{{ $category->category_name }}</td>
                    <td>{{ $category->description }}</td>
                    <td>{{ $category->status ? 'Hiển thị' : 'Ẩn' }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-edit" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-delete" title="Xóa"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                        <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-show" title="Chi tiết">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- CSS tùy chỉnh cho giao diện admin với nền trắng và điểm nhấn #81c784 -->
<style>
    /* Container chung */
    .container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    /* Breadcrumb */
    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
    .breadcrumb-item a {
        color: #81c784;
        text-decoration: none;
    }
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #6c757d;
    }
    
    /* Tiêu đề trang */
    h1 {
        color: #333;
    }
    
    /* Form tìm kiếm */
    .form-inline .input-group {
        display: flex;
        align-items: center;
    }
    .form-inline .form-control {
        width: 250px;
        border-radius: 4px 0 0 4px;
        border: 1px solid #81c784;
    }
    /* Thêm style focus cho input */
    .form-inline .form-control:focus {
        border-color: #689f65;
        box-shadow: 0 0 0 0.2rem rgba(104, 159, 101, 0.25);
        outline: none;
    }
    .btn-search {
        border-radius: 0 4px 4px 0;
        background-color: #81c784;
        border: 1px solid #81c784;
        color: #fff;
        transition: background-color 0.3s ease;
    }
    /* Bỏ hover cho nút tìm kiếm (kính lúp) */
    .btn-search:hover {
        background-color: #81c784;
    }
    /* Thêm style focus cho nút tìm kiếm */
    .btn-search:focus {
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(104, 159, 101, 0.25);
    }
    
    /* Nút thêm danh mục */
    .btn-add {
        background-color: #81c784;
        border: 1px solid #81c784;
        color: #fff;
        padding: 8px 16px;
        font-size: 0.9rem;
        border-radius: 4px;
        transition: background-color 0.3s ease, transform 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    .btn-add:hover {
        background-color: #689f65;
        transform: translateY(-2px);
    }
    
    /* Thẻ bao ảnh với kích thước cố định */
    .table-img-wrapper {
        width: 100px;
        height: 100px;
        overflow: hidden;
        border-radius: 4px;
        margin: auto;
        border: 2px solid #81c784;
    }
    .table-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .table-img-wrapper img:hover {
        transform: scale(1.1);
    }
    
    /* Cải thiện giao diện bảng */
    table.table-bordered {
        border: 1px solid #dee2e6;
    }
    table.table-bordered th,
    table.table-bordered td {
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }
    table.table-bordered tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Các nút thao tác */
    .btn {
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
    }
    .btn-edit {
        background-color: #ffc107;
        border: 1px solid #ffc107;
        color: #fff;
        padding: 6px 10px;
        border-radius: 4px;
    }
    .btn-edit:hover {
        background-color: #e0a800;
    }
    .btn-delete {
        background-color: #dc3545;
        border: 1px solid #dc3545;
        color: #fff;
        padding: 6px 10px;
        border-radius: 4px;
    }
    .btn-delete:hover {
        background-color: #c82333;
    }
    .btn-show {
        background-color: #17a2b8;
        border: 1px solid #17a2b8;
        color: #fff;
        padding: 6px 10px;
        border-radius: 4px;
    }
    .btn-show:hover {
        background-color: #138496;
    }
</style>
@endsection

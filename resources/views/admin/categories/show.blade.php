@extends('layouts.admin')

@section('content')
<div class="container p-4 modern-category-detail">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.categories.index') }}">Danh Mục</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Chi Tiết Danh Mục</li>
        </ol>
    </nav>
    <!-- End Breadcrumb -->



    <!-- Category Detail Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body d-flex flex-wrap align-items-center">
            <!-- Hình ảnh danh mục -->
            <div class="category-image-wrapper">
                @if($category->image)
                    <img src="{{ asset($category->image) }}" alt="{{ $category->category_name }}" class="img-fluid">
                @else
                    <img src="https://via.placeholder.com/300" alt="No Image" class="img-fluid">
                @endif
            </div>
            <!-- Thông tin danh mục -->
            <div class="category-info flex-grow-1">
                <h3>{{ $category->category_name }}</h3>
                <p><strong>Mô Tả:</strong> {{ $category->description ?: 'Không có mô tả' }}</p>
                <p><strong>Slug:</strong> {{ $category->slug }}</p>
                <p>
                    <strong>Trạng Thái:</strong>
                    @if($category->status == 1)
                        <span class="badge bg-success">Hoạt động</span>
                    @else
                        <span class="badge bg-danger">Ngừng hoạt động</span>
                    @endif
                </p>
                <p><strong>Sắp Xếp:</strong> {{ $category->sort_order ?? 'Không có thông tin' }}</p>
                <p><strong>Danh Mục Cha:</strong> {{ $category->parent ? $category->parent->category_name : 'Không có danh mục cha' }}</p>
                <p><strong>Mùa Thu Hoạch:</strong> {{ $category->harvest_season ?? 'Không có thông tin' }}</p>
                <p><strong>Vùng Miền:</strong> {{ $category->region ?? 'Không có thông tin' }}</p>
                <p><strong>Chứng Nhận:</strong> {{ $category->certifications ?? 'Không có thông tin' }}</p>
            </div>
        </div>
    </div>

    <!-- Nút hành động -->
    <div class="mt-4 text-center">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay Lại</a>
        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary">Chỉnh Sửa</a>
        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Xóa</button>
        </form>
    </div>
</div>

<!-- Inline CSS Styles for Modern Ecommerce Layout -->
<style>
    /* Container & Card */
    .modern-category-detail {
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .card {
        border: none;
    }
    .card-body {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
    }
    /* Category Image */
    .category-image-wrapper {
        flex: 0 0 300px;
        max-width: 300px;
        margin-right: 30px;
    }
    .category-image-wrapper img {
        width: 100%;
        border-radius: 8px;
        transition: transform 0.3s ease;
    }
    .category-image-wrapper img:hover {
        transform: scale(1.05);
    }
    /* Category Information */
    .category-info {
        flex: 1;
        min-width: 300px;
    }
    .category-info h3 {
        font-size: 2rem;
        margin-bottom: 15px;
        color: #343a40;
    }
    .category-info p {
        margin-bottom: 10px;
        font-size: 1rem;
        color: #555;
    }
    .category-info strong {
        color: #343a40;
    }
    /* Breadcrumb */
    .breadcrumb {
        background-color: transparent;
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
    /* Action Buttons */
    .btn {
        transition: background-color 0.3s ease, transform 0.3s ease;
        margin: 0 5px;
    }
    .btn:hover {
        transform: translateY(-2px);
    }


    .btn-primary {
        background-color: #81c784;
        border-color: #81c784;
        color: #fff;
    }
    .btn-primary:hover {
        background-color: #689f65;
        border-color: #689f65;
    }
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        color: #fff;
    }
    .btn-danger:hover {
        background-color: #c82333;
        border-color: #c82333;
    }
</style>
@endsection

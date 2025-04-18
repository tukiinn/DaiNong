@extends('layouts.admin')

@section('content')
<div class="container p-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Danh sách sản phẩm</li>
        </ol>
    </nav>

    <!-- Tiêu đề trang -->
    <h1 class="text-center mb-4">Quản lý sản phẩm</h1>

    <!-- Thanh công cụ -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <!-- Nhóm các nút thao tác -->
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.products.create') }}" class="btn btn-add mb-2">
                <i class="fas fa-plus"></i> Thêm sản phẩm mới
            </a>
            <a href="{{ route('admin.units.index') }}" class="btn btn-unit mb-2">
                <i class="fas fa-cubes"></i> Quản lý đơn vị
            </a>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-supplier mb-2">
                <i class="fas fa-truck"></i> Quản lý nhà cung cấp
            </a>
            <a href="{{ route('admin.product_histories.index') }}" class="btn btn-warehouse mb-2">
                <i class="fa-solid fa-warehouse"></i> Lịch sử kho sản phẩm
            </a>
        </div>
        <!-- Form tìm kiếm -->
        <form method="GET" action="{{ route('admin.products.index') }}" class="mb-2">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Danh sách sản phẩm -->
    <div class="row justify-content-center">
        @forelse ($products as $product)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card product-card h-100">
                <!-- Phần ảnh sản phẩm -->
                <div class="position-relative product-image-container">
                    <img 
                        src="{{ $product->image ? asset($product->image) : 'https://via.placeholder.com/250x250' }}" 
                        class="card-img-top product-image" 
                        alt="{{ $product->product_name }}">
                    <!-- Hover Icons -->
                    <div class="hover-icons position-absolute w-100 d-flex justify-content-center">
                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn icon-view rounded-circle mx-1" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn icon-edit rounded-circle mx-1" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn icon-delete rounded-circle mx-1" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <!-- Nội dung sản phẩm -->
                <div class="card-body text-center d-flex flex-column justify-content-between">
                    <a href="{{ route('admin.products.show', $product->id) }}" class="card-title-link">
                        <h6 class="card-title text-truncate mb-2">{{ $product->product_name }}</h6>
                    </a>
                    <p class="card-text text-danger font-weight-bold mb-0">
                        {{ number_format($product->import_price , 0, ',', '.') }} VND
                    </p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center">
            <p>Không có sản phẩm nào.</p>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $products->links('pagination::bootstrap-4') }}
    </div>
</div>

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
    text-decoration: none;
    color: #81c784;
}
.breadcrumb-item.active {
    color: #6c757d;
}

/* Thanh tiêu đề */
h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #343a40;
}

/* Thanh công cụ & Form tìm kiếm */
.input-group .form-control {
    min-width: 250px;
    border: 1px solid #81c784;
    border-right: none;
    border-radius: 4px 0 0 4px;
}
.input-group .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    background-color: #81c784;
    border: 1px solid #81c784;
    color: #fff;
    transition: background-color 0.3s ease;
}
.input-group .btn:hover {
    background-color: #689f65;
}

/* Nút thêm sản phẩm (btn-add) */
.btn-add {
    background-color: #81c784;
    border: none;
    color: #fff;
    padding: 10px 20px;
    font-size: 1rem;
    border-radius: 4px;
    transition: background-color 0.3s ease, transform 0.3s ease;
    text-decoration: none;
}
.btn-add:hover {
    background-color: #689f65;
    transform: translateY(-2px);
}

/* Nút quản lý đơn vị (btn-unit) */
.btn-unit {
    background-color: #17a2b8;
    border: none;
    color: #fff;
    padding: 10px 20px;
    font-size: 1rem;
    border-radius: 4px;
    transition: background-color 0.3s ease, transform 0.3s ease;
    text-decoration: none;
}
.btn-unit:hover {
    background-color: #138496;
    transform: translateY(-2px);
}

/* Nút quản lý nhà cung cấp (btn-supplier) */
.btn-supplier {
    background-color: #6f42c1;
    border: none;
    color: #fff;
    padding: 10px 20px;
    font-size: 1rem;
    border-radius: 4px;
    transition: background-color 0.3s ease, transform 0.3s ease;
    text-decoration: none;
}
.btn-supplier:hover {
    background-color: #5a32a3;
    transform: translateY(-2px);
}
.btn-warehouse {
    background-color: #1976d2;
    border: none;
    color: #fff;
    padding: 10px 20px;
    font-size: 1rem;
    border-radius: 4px;
    transition: background-color 0.3s ease, transform 0.3s ease;
    text-decoration: none;
}
.btn-warehouse:hover {
    background-color: #1565c0;
    transform: translateY(-2px);
}


/* Card sản phẩm */
.product-card {
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    background: #fdfdfd;
}
.product-card:hover {
    border-color: #81c784;
    transform: translateY(-8px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

/* Phần ảnh sản phẩm */
.product-image-container {
    position: relative;
    overflow: hidden;
    height: 220px;
}
.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.product-image-container:hover .product-image {
    transform: scale(1.1);
}

/* Hover Icons */
.hover-icons {
    visibility: hidden;
    opacity: 0;
    position: absolute;
    bottom: -50px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    gap: 12px;
    transition: visibility 0.3s, opacity 0.3s ease, bottom 0.3s ease;
}
.product-image-container:hover .hover-icons {
    visibility: visible;
    opacity: 1;
    bottom: 15px;
}
.hover-icons .btn {
    width: 45px;
    height: 45px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #6c757d;
    border: none;
    color: #fff;
    transition: background-color 0.3s ease, transform 0.3s ease;
    font-size: 1.1rem;
}

/* Hover màu theo chức năng */
/* View icon */
.hover-icons .icon-view:hover {
    background-color: #007bff;
    transform: scale(1.15);
}
/* Edit icon */
.hover-icons .icon-edit:hover {
    background-color: #ffc107;
    transform: scale(1.15);
}
/* Delete icon */
.hover-icons .icon-delete:hover {
    background-color: #dc3545;
    transform: scale(1.15);
}

/* Nội dung thẻ sản phẩm */
.card-body {
    padding: 15px;
    background: #fff;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 90px;
}
.card-title-link {
    text-decoration: none;
    color: #343a40;
    font-weight: bold;
    transition: color 0.3s ease;
}
.card-title-link:hover {
    color: #81c784;
}
.card-title {
    font-size: 1rem;
    font-weight: bold;
    margin-bottom: 10px;
}
.card-text {
    font-size: 0.95rem;
    font-weight: bold;
    color: #e74c3c;
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
.pagination li a, .pagination li span {
    color: #81c784;
    border: 1px solid #81c784;
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color 0.3s ease, color 0.3s ease;
}
.pagination li a:hover, .pagination li span:hover {
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

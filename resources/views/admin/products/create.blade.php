@extends('layouts.admin')

@section('content')
<div class="container p-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.products.index') }}">Sản Phẩm</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Thêm Sản Phẩm Mới</li>
        </ol>
    </nav>

    <!-- Tiêu đề trang -->
    <h2 class="mb-4 text-center">Thêm Sản Phẩm Mới</h2>

    <!-- Form Thêm Sản Phẩm -->
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Tên Sản Phẩm -->
        <div class="mb-3">
            <label for="product_name" class="form-label">Tên Sản Phẩm</label>
            <input type="text" class="form-control @error('product_name') is-invalid @enderror" id="product_name" name="product_name" value="{{ old('product_name') }}" required>
            @error('product_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Mô Tả -->
        <div class="mb-3">
            <label for="description" class="form-label">Mô Tả</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Ảnh -->
        <div class="mb-3">
            <label for="image" class="form-label">Ảnh</label>
            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

         <!-- Giá nhập-->
        <div class="mb-3">
            <label for="import_price" class="form-label">Giá nhập</label>
                <input type="number" step="0.01" class="form-control @error('import_price') is-invalid @enderror" id="import_price" name="import_price" value="{{ old('import_price') }}" required>
                @error('import_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

        <!-- Giá -->
        <div class="mb-3">
            <label for="price" class="form-label">Giá</label>
            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
            @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Giá Giảm Giá -->
        <div class="mb-3">
            <label for="discount_price" class="form-label">Giá Giảm Giá (nếu có)</label>
            <input type="number" step="0.01" class="form-control @error('discount_price') is-invalid @enderror" id="discount_price" name="discount_price" value="{{ old('discount_price') }}">
            @error('discount_price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Số Lượng Tồn Kho -->
        <div class="mb-3">
            <label for="stock_quantity" class="form-label">Số Lượng Tồn Kho</label>
            <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity') }}" required>
            @error('stock_quantity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

       <!-- Nhà Cung Cấp -->
<div class="mb-3">
    <label for="supplier_id" class="form-label">Nhà Cung Cấp</label>
    <select class="form-control @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id">
        <option value="">Chọn nhà cung cấp</option>
        @foreach($suppliers as $supplier)
            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                {{ $supplier->supplier_name }}
            </option>
        @endforeach
    </select>
    @error('supplier_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Đơn Vị -->
<div class="mb-3">
    <label for="unit_id" class="form-label">Đơn Vị</label>
    <select class="form-control @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
        <option value="">Chọn đơn vị</option>
        @foreach($units as $unit)
            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                {{ $unit->unit_name }}
            </option>
        @endforeach
    </select>
    @error('unit_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


        <!-- Danh Mục -->
        <div class="mb-3">
            <label for="category_id" class="form-label">Danh Mục</label>
            <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                <option value="">Chọn danh mục</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <!-- Trạng Thái -->
        <div class="mb-3">
            <label for="status" class="form-label">Trạng Thái</label>
            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Ngừng hoạt động</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Ngày Hết Hạn -->
        <div class="mb-3">
            <label for="expiry_date" class="form-label">Ngày Hết Hạn</label>
            <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}">
            @error('expiry_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Nổi Bật -->
        <div class="mb-3">
            <label for="featured" class="form-label">Nổi Bật</label>
            <select class="form-control @error('featured') is-invalid @enderror" id="featured" name="featured">
                <option value="0" {{ old('featured') == '0' ? 'selected' : '' }}>Không</option>
                <option value="1" {{ old('featured') == '1' ? 'selected' : '' }}>Có</option>
            </select>
            @error('featured')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Nút Submit -->
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Tạo Sản Phẩm</button>
        </div>
    </form>
</div>

<!-- CSS Tùy Chỉnh -->
<style>
   /* Container chung */
   .container {
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
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
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #6c757d;
    }
    /* Tiêu đề */
    h2 {
        color: #343a40;
        text-align: center;
    }
    /* Form label */
    .form-label {
        font-weight: 500;
    }
    /* Input, select và textarea */
    .form-control {
        border-radius: 4px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    /* Focus cho input */
    .form-control:focus {
        border-color: #81c784;
        box-shadow: 0 0 0 0.2rem rgba(129, 199, 132, 0.25);
    }
    /* Nút Submit */
    .btn-primary {
        background-color: #81c784;
        border-color: #81c784;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .btn-primary:hover {
        background-color: #689f65;
        border-color: #689f65;
        transform: translateY(-2px);
    }
</style>
@endsection

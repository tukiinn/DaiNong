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
                <a href="{{ route('admin.categories.index') }}">Danh Mục</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa Danh Mục</li>
        </ol>
    </nav>
    <!-- End Breadcrumb -->

    <h2 class="my-4 text-center">Chỉnh Sửa Danh Mục</h2>
    <!-- Form Chỉnh Sửa Danh Mục -->
    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Tên Danh Mục -->
        <div class="mb-3">
            <label for="category_name" class="form-label">Tên Danh Mục</label>
            <input type="text" class="form-control @error('category_name') is-invalid @enderror" id="category_name" name="category_name" value="{{ old('category_name', $category->category_name) }}" required>
            @error('category_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Mô Tả -->
        <div class="mb-3">
            <label for="description" class="form-label">Mô Tả</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $category->description) }}</textarea>
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
            @if($category->image)
                <img src="{{ asset($category->image) }}" alt="{{ $category->category_name }}" class="img-thumbnail mt-2" style="width: 150px;">
            @endif
        </div>

        <!-- Slug -->
        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $category->slug) }}" required>
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Trạng Thái -->
        <div class="mb-3">
            <label for="status" class="form-label">Trạng Thái</label>
            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                <option value="1" {{ old('status', $category->status) == '1' ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ old('status', $category->status) == '0' ? 'selected' : '' }}>Ngừng hoạt động</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Sắp Xếp -->
        <div class="mb-3">
            <label for="sort_order" class="form-label">Sắp Xếp</label>
            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}">
            @error('sort_order')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Danh Mục Cha -->
        <div class="mb-3">
            <label for="parent_id" class="form-label">Danh Mục Cha</label>
            <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                <option value="">Chọn danh mục cha (Nếu có)</option>
                @foreach($categories as $parentCategory)
                    <option value="{{ $parentCategory->id }}" {{ old('parent_id', $category->parent_id) == $parentCategory->id ? 'selected' : '' }}>{{ $parentCategory->category_name }}</option>
                @endforeach
            </select>
            @error('parent_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Mùa Thu Hoạch -->
        <div class="mb-3">
            <label for="harvest_season" class="form-label">Mùa Thu Hoạch</label>
            <input type="text" class="form-control @error('harvest_season') is-invalid @enderror" id="harvest_season" name="harvest_season" value="{{ old('harvest_season', $category->harvest_season) }}">
            @error('harvest_season')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Vùng Miền -->
        <div class="mb-3">
            <label for="region" class="form-label">Vùng Miền</label>
            <input type="text" class="form-control @error('region') is-invalid @enderror" id="region" name="region" value="{{ old('region', $category->region) }}">
            @error('region')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Chứng Nhận -->
        <div class="mb-3">
            <label for="certifications" class="form-label">Chứng Nhận</label>
            <input type="text" class="form-control @error('certifications') is-invalid @enderror" id="certifications" name="certifications" value="{{ old('certifications', $category->certifications) }}">
            @error('certifications')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Nút Submit -->
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Cập Nhật Danh Mục</button>
        </div>
    </form>
</div>


<!-- Inline CSS Styles -->
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
    }
    /* Ảnh */
    .img-thumbnail {
        border-radius: 4px;
        margin-top: 10px;
    }

</style>
@endsection
@extends('layouts.app')

@section('content')
    <!-- Banner -->
    <div class="banner-container position-relative mb-4">
        <img src="{{ asset('images/banner/organic-breadcrumb-1.jpg') }}" alt="Banner quảng cáo" class="banner-image w-100" style="height: 130px; object-fit: cover;">
        <div class="banner-overlay position-absolute top-50 start-50 translate-middle text-center">
            <h2 class="text-dark">Danh sách danh mục</h2>
            <nav aria-label="breadcrumb" class="d-flex justify-content-center">
                <ol class="breadcrumb bg-transparent mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-dark">Trang chủ</a>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container">
        <div class="row">
            @foreach ($categories as $category)
                <div class="col-md-4 mb-4">
                    <div class="card category-card border-light shadow-lg">
                        <!-- Bọc ảnh trong thẻ div có kích thước cố định -->
                        <div class="card-img-wrapper">
                            <img src="{{ $category->image ? asset($category->image) : 'https://via.placeholder.com/300' }}" 
                                 alt="{{ $category->category_name }}" class="card-img-top">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title text-center">{{ $category->category_name }}</h5>
                            <p class="card-text">{{ Str::limit($category->description, 150) }}</p>
                            <div class="d-flex justify-content-between">
                                <span class="badge badge-info">Trạng thái: {{ $category->status ? 'Hiển thị' : 'Ẩn' }}</span>
                                <a href="{{ route('categories.show', $category->id) }}" class="btn btn-success btn-sm">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        /* Cải thiện kiểu dáng thẻ card */
        .category-card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease;
            border-radius: 8px; /* Bo tròn góc */
            overflow: hidden;
        }

        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        /* Hiệu ứng cho nút */
        .btn-success {
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-success:hover {
            background-color: #28a745;
            transform: translateY(-3px);
        }

        /* Tăng cường sự nổi bật cho tiêu đề */
        .card-title {
            font-weight: bold;
            font-size: 1.25rem;
            color: #333;
            margin-bottom: 10px;
        }

        /* Định dạng thẻ bọc ảnh với kích thước cố định */
        .card-img-wrapper {
            width: 100%;
            height: 250px;
            overflow: hidden;
        }

        /* Định dạng ảnh bên trong thẻ bọc */
        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 0.3s ease-in-out;
        }

        .card-img-wrapper img:hover {
            opacity: 0.9;
        }

        /* Điều chỉnh khoảng cách giữa các thẻ card */
        .row {
            margin-top: 20px;
        }

        /* Định dạng badge trạng thái */
        .badge-info {
            font-size: 0.9rem;
        }
    </style>
@endsection

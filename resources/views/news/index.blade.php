@extends('layouts.app')

@section('content')
<!-- CSS tùy chỉnh cho giao diện hiện đại -->
<style>
    /* Kiểu container hiện đại */
    .news-container {
        background: #fff;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Hiệu ứng bay nhẹ cho card */
    .card {
        transition: transform 0.3s, box-shadow 0.3s;
        overflow: hidden; /* Giữ cho phần tử không bị tràn ra khi phóng to */
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.2);
    }

    /* Hiệu ứng chuyển màu tiêu đề khi hover */
    .card-title a {
        color: #333;
        transition: color 0.3s ease;
        font-weight: 700; /* Chữ đậm */
        font-size: 1.25rem; /* Chữ to hơn */
    }
    .card-title a:hover {
        color: #28a745;
    }

    /* Nút "Xem thêm" với màu gradient */
    .btn-custom {
        background: linear-gradient(45deg, #28a745, #66bb6a);
        border: none;
        color: #fff;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        font-size: 1rem;
        font-weight: 500;
        transition: background 0.3s, box-shadow 0.3s, transform 0.3s;
    }
    .btn-custom:hover, .btn-custom:focus {
        background: linear-gradient(45deg, #218838, #57a05a);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    /* Hiệu ứng phóng to ảnh khi hover */
    .card-img-top {
        transition: transform 0.3s ease;
    }
    .card:hover .card-img-top {
        transform: scale(1.1);
    }
</style>

<!-- Banner & Breadcrumb -->
<div class="banner-container position-relative mb-4">
    <img src="{{ asset('images/banner/organic-breadcrumb-1.jpg') }}" alt="Banner quảng cáo" class="banner-image w-100" style="height: 130px; object-fit: cover;">
    <div class="banner-overlay position-absolute top-50 start-50 translate-middle text-center">
        <h2 class="text-dark">Tin Tức</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent justify-content-center mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-dark">Trang chủ</a>
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Container tin tức hiện đại -->
<div class="container my-5 news-container">
    <div class="row">
        @foreach($news as $item)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    @if($item->image)
                        <img src="{{ asset($item->image) }}" class="card-img-top" alt="{{ $item->title }}" style="height: 200px; object-fit: cover;">
                    @else
                        <!-- Ảnh placeholder nếu không có ảnh -->
                        <img src="https://via.placeholder.com/350x200?text=No+Image" class="card-img-top" alt="No Image" style="height: 200px; object-fit: cover;">
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <a href="{{ route('news.show', $item->slug) }}" class="text-decoration-none">
                                {{ $item->title }}
                            </a>
                        </h5>
                        <p class="card-text flex-grow-1">{{ Str::limit($item->summary, 150) }}</p>
                        <a href="{{ route('news.show', $item->slug) }}" class="btn btn-custom mt-3 align-self-start">Xem thêm</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center">
        {{ $news->links() }}
    </div>
</div>
@endsection

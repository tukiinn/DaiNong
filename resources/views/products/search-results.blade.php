@extends('layouts.app')

@section('content')
<div class="container mt-4">
       <!-- Banner & Breadcrumb -->
<div class="banner-container position-relative mb-4">
    <img src="{{ asset('images/banner/organic-breadcrumb-1.jpg') }}" alt="Banner quảng cáo" class="banner-image w-100" style="height: 130px; object-fit: cover;">
    <div class="banner-overlay position-absolute top-50 start-50 translate-middle text-center">
        <h2>Kết quả tìm kiếm cho: "{{ $query }}"</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-dark">Sản phẩm</a></li>              
            </ol>
        </nav>
    </div>
</div>
    @if($products->isEmpty())
    <p class="alert alert-warning mt-3">Không tìm thấy kết quả!</p>
    @else
        <!-- Hiển thị sản phẩm -->
        <div class="row justify-content-center">
            @foreach ($products as $product)
                @php
                    $basePrice = $product->discount_price ?? $product->price;
                @endphp
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card product-card position-relative">
                        @if ($product->discount_price && $product->price)
                            <div class="discount-badge position-absolute bg-danger text-white rounded-circle p-2" style="top: 10px; left: 10px;">
                                -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                            </div>
                        @endif

                        <div class="position-relative product-image-container">
                            <img 
                                src="{{ $product->image ? asset($product->image) : 'https://via.placeholder.com/250x250' }}" 
                                class="card-img-top product-image" 
                                alt="{{ $product->product_name }}">
                            
                            <!-- Loại bỏ inline style ở đây -->
                            <div class="hover-icons position-absolute w-100 d-flex justify-content-center align-items-center">
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-light rounded-circle mx-1" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form class="d-inline add-to-cart-form" data-product-id="{{ $product->id }}">
                                    @csrf
                                    <input type="hidden" name="size" class="selected-size-{{ $product->id }}" value="1kg">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="button" class="btn btn-success rounded-circle mx-1 add-to-cart" title="Thêm vào giỏ hàng">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card-body text-center">
                            <div class="product-info">
                                <a href="{{ route('products.show', $product->id) }}" class="card-title-link">
                                    <h6 class="card-title text-truncate mb-2">{{ $product->product_name }}</h6>
                                </a>
                                <p class="card-text mb-2">
                                    @if ($product->discount_price)
                                        <span class="text-muted text-decoration-line-through">
                                            {{ number_format($product->price, 0, ',', '.') }}₫
                                        </span>
                                    @endif
                                    <span class="text-danger font-weight-bold product-price" id="product-price-{{ $product->id }}">
                                        {{ number_format($basePrice, 0, ',', '.') }}₫
                                    </span>
                                </p>
                            </div>
                            
                            @if (optional($product->unit)->unit_name === 'kg')
                                <div class="size-selection d-flex justify-content-center gap-2 mt-2">
                                    <img src="{{ asset('images/icon/1kg.jpg') }}" alt="1kg" class="size-option img-fluid rounded selected" data-product-id="{{ $product->id }}" data-size="1kg" data-price="{{ $basePrice }}">
                                    <img src="{{ asset('images/icon/500g.jpg') }}" alt="500g" class="size-option img-fluid rounded" data-product-id="{{ $product->id }}" data-size="500g" data-price="{{ $basePrice * 0.5 }}">
                                    <img src="{{ asset('images/icon/250g.jpg') }}" alt="250g" class="size-option img-fluid rounded" data-product-id="{{ $product->id }}" data-size="250g" data-price="{{ $basePrice * 0.25 }}">
                                </div>
                            @elseif (optional($product->unit)->unit_name === 'túi')
                                <div class="bag-option text-center mt-2">
                                    /1 <i class="fa-solid fa-bag-shopping" style="color: #45d66c;"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Phân trang -->
        @if ($products->hasPages())
            <nav>
                <ul class="pagination justify-content-center">
                    {{-- Nút "Trang trước" --}}
                    @if ($products->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">«</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $products->previousPageUrl() }}" rel="prev">«</a>
                        </li>
                    @endif

                    {{-- Hiển thị danh sách số trang --}}
                    @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                        @if ($page == $products->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    {{-- Nút "Trang tiếp" --}}
                    @if ($products->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $products->nextPageUrl() }}" rel="next">»</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">»</span>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif

    @endif
</div>

<style>
    /* Card style */
    .product-card {
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        height: 380px;
        background: #ffffff;
        position: relative;
    }
    .product-card:hover {
        border-color: #28a745;
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }
    /* Discount badge */
    .discount-badge {
        top: 10px;
        right: 10px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: bold;
        z-index: 10;
    }
    /* Phần chứa ảnh */
    .product-image-container {
        position: relative;
        overflow: hidden;
        height: 240px;
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
    /* Hover icons */
    .hover-icons {
        visibility: hidden;
        opacity: 0;
        position: absolute;
        bottom: -50px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        gap: 10px;
        transition: visibility 0.3s, opacity 0.3s ease, bottom 0.3s ease;
    }
    .product-image-container:hover .hover-icons {
        visibility: visible;
        opacity: 1;
        bottom: 10px;
    }
    .hover-icons .btn {
        width: 45px;
        height: 45px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #6c757d;
        border-color: #6c757d;
        color: #fff;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .hover-icons .btn:hover {
        background-color: #28a745;
        border-color: #28a745;
        transform: scale(1.1);
    }
    /* Card content */
    .card-body {
        padding: 15px;
        height: 130px;
        background: #f8f9fa;
    }
    .card-title-link {
        text-decoration: none;
        color: #343a40;
        font-weight: bold;
        transition: color 0.3s ease;
    }
    .card-title-link:hover {
        color: #28a745;
    }
    .card-title {
        font-size: 1.1rem;
        font-weight: bold;
    }
    .card-text {
        font-size: 1rem;
    }
    .my-4 {
        margin-top: 30px;
        margin-bottom: 20px;
    }
    /* Style cho phần lựa chọn size */
    .size-selection .size-option {
        cursor: pointer;
        max-width: 30px;
        transition: transform 0.3s ease, border 0.3s ease;
        border: 1px solid #ccc;
        border-radius: 50%;
        padding: 2px;
    }
    .size-selection .size-option.selected {
        border: 1px solid #28a745;
        transform: scale(1.1);
    }
</style>
<!-- JavaScript xử lý chọn size -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sizeOptions = document.querySelectorAll('.size-option');
        sizeOptions.forEach(function(option) {
            option.addEventListener('click', function(){
                const productId = this.getAttribute('data-product-id');
                const selectedSize = this.getAttribute('data-size');
                const updatedPrice = this.getAttribute('data-price');
    
                // Cập nhật giá hiển thị
                const priceDisplay = document.getElementById(`product-price-${productId}`);
                if (priceDisplay) {
                    priceDisplay.textContent = new Intl.NumberFormat('vi-VN').format(updatedPrice) + '₫';
                }
    
                // Cập nhật input ẩn của form
                const hiddenInput = document.querySelector(`.selected-size-${productId}`);
                if (hiddenInput) {
                    hiddenInput.value = selectedSize;
                }
    
                // Cập nhật hiệu ứng viền cho icon được chọn
                const parent = this.parentElement;
                parent.querySelectorAll('.size-option').forEach(function(img) {
                    img.classList.remove('selected');
                });
                this.classList.add('selected');
            });
        });
    });
    </script>
@endsection

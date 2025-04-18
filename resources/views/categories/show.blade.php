@extends('layouts.app')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Banner -->
<div class="banner-container position-relative mb-4">
    <img src="{{ asset('images/banner/organic-breadcrumb-1.jpg') }}" alt="Banner quảng cáo" class="banner-image w-100" style="height: 130px; object-fit: cover;">
    <div class="banner-overlay position-absolute top-50 start-50 translate-middle text-center">
        <h2 class="text-dark">{{ $category->category_name }}</h2>
        <nav aria-label="breadcrumb" class="d-flex justify-content-center">
            <ol class="breadcrumb bg-transparent mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-dark">Trang chủ</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('categories.index') }}" class="text-dark">Danh mục</a>
                </li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="card category-card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    @if($category->image)
                        <img src="{{ asset($category->image) }}" alt="{{ $category->category_name }}" class="img-fluid rounded shadow" style="max-width: 250px;">
                    @else
                        <img src="{{ asset('images/no-image.png') }}" alt="No image" class="img-fluid rounded shadow" style="max-width: 250px;">
                    @endif
                </div>
                <div class="col-md-8">
                    <h3 class="mb-3 category-title">{{ $category->category_name }}</h3>
                    <p class="mb-4 category-description">{{ $category->description ?: 'Không có mô tả' }}</p>
                    <ul class="list-unstyled row category-details">
                        <li class="col-sm-6">
                            <strong>Trạng Thái:</strong>
                            @if($category->status == 1)
                                <span class="badge bg-success">Hoạt động</span>
                            @else
                                <span class="badge bg-danger">Ngừng hoạt động</span>
                            @endif
                        </li>
                        <li class="col-sm-6">
                            <strong>Mùa Thu Hoạch:</strong> <span>{{ $category->harvest_season ?? 'Không có thông tin' }}</span>
                        </li>
                        <li class="col-sm-6">
                            <strong>Vùng Miền:</strong> <span>{{ $category->region ?? 'Không có thông tin' }}</span>
                        </li>
                        <li class="col-sm-6">
                            <strong>Chứng Nhận:</strong> <span>{{ $category->certifications ?? 'Không có thông tin' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

   <!-- Tab Nội Dung với class tùy chỉnh -->
<ul class="nav custom-nav-tabs mt-4" id="categoryTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="custom-nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
            Mô Tả
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="custom-nav-link" id="related-tab" data-bs-toggle="tab" data-bs-target="#related" type="button" role="tab">
            Sản Phẩm Liên Quan
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="custom-nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
            Đánh Giá
        </button>
    </li>
</ul>

<div class="tab-content border border-top-0 p-4" id="categoryTabContent">
    <!-- Tab Mô Tả -->
    <div class="tab-pane fade show active" id="description" role="tabpanel">
        <p class="tab-description">
            {{ $category->description ?: 'Không có mô tả chi tiết' }}
        </p>
    </div>
    <!-- Tab Sản Phẩm Liên Quan -->
    <div class="tab-pane fade" id="related" role="tabpanel">
        @if($relatedProducts->isEmpty())
            <p class="text-center p-2">Không tìm thấy sản phẩm nào liên quan!</p>
        @else
            <!-- Slider sản phẩm liên quan -->
            <div class="related-products-slider">
                @foreach($relatedProducts as $product)
                    <div class="related-product-item text-center p-2">
                        <a href="{{ route('products.show', $product->id) }}">
                            <img src="{{ asset($product->image ?? 'images/no-image.png') }}" 
                                 alt="{{ $product->product_name }}" 
                                 class="img-fluid rounded" 
                                 style="max-height: 150px;">
                            <p class="mt-2 mb-0 text-dark fw-bold">
                                {{ $product->product_name }}
                            </p>
                            <p class="text-danger fw-bold">
                                {{ number_format($product->price, 0, ',', '.') }}₫
                            </p>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <!-- Tab Đánh Giá & Bình Luận -->
    <div class="tab-pane fade" id="reviews" role="tabpanel">
        <div class="reviews-section">
            <h4 class="mb-3">Đánh Giá & Bình Luận</h4>
    
            <!-- Hiển thị danh sách đánh giá -->
            @if($reviews->isEmpty())
                <p class="text-center p-4">Chưa có đánh giá. Hãy là người đầu tiên để lại bình luận!</p>
            @else
                <div class="reviews-list mb-4">
                    @foreach($reviews as $review)
                        <div id="comment-{{ $review->id }}" class="review-item border rounded p-3 mb-3">
                            <div class="review-header d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="review-user fw-bold">
                                        {{ $review->user ? $review->user->name : 'Ẩn danh' }}
                                    </span>
                                    <span class="review-rating ms-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </span>
                                </div>
                                <span class="review-date text-muted">
                                    {{ $review->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                            <div class="review-body mt-2">
                                <p>{{ $review->comment }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
    
            <!-- Form gửi đánh giá -->
            <div class="review-form">
                <h5>Để lại đánh giá của bạn</h5>
                <form action="{{ route('reviews.store', $category->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Đánh giá:</label>
                        <!-- Star Rating chọn theo kiểu radio -->
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" required>
                            <label for="star5" title="5 sao"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4" title="4 sao"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3" title="3 sao"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2" title="2 sao"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1" title="1 sao"><i class="fas fa-star"></i></label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Bình luận:</label>
                        <textarea name="comment" id="comment" rows="4" class="form-control" 
                                  placeholder="Viết bình luận của bạn ở đây..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Gửi đánh giá</button>
                </form>
            </div>
            
        </div>
    </div>
</div>

<!-- JavaScript để tự động chuyển sang tab "Đánh Giá" nếu URL chứa anchor "comment-" -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Nếu URL có chứa anchor chứa "comment-"
    if(window.location.hash && window.location.hash.includes("comment-")){
        // Lấy nút tab "Đánh Giá"
        var reviewsTabTrigger = document.querySelector('#reviews-tab');
        if(reviewsTabTrigger) {
            // Kích hoạt tab "Đánh Giá" sử dụng Bootstrap Tab API
            var tab = new bootstrap.Tab(reviewsTabTrigger);
            tab.show();
            
            // Sau khi tab hiện ra, cuộn đến bình luận có id trùng với anchor
            setTimeout(function(){
                var commentElement = document.querySelector(window.location.hash);
                if(commentElement) {
                    commentElement.scrollIntoView({ behavior: "smooth", block: "start" });
                }
            }, 300);
        }
    }
});
</script>

<style>
   /* Star Rating */
.star-rating {
    /* Nếu bạn muốn giá trị 5 ở bên trái, giữ thuộc tính sau */
    direction: rtl;
    font-size: 1.5rem;
    unicode-bidi: bidi-override;
    display: inline-block;
}
.star-rating input[type="radio"] {
    display: none;
}
.star-rating label {
    color: #ccc; /* Màu sao khi chưa được chọn */
    cursor: pointer;
    transition: color 0.2s;
    margin: 0 2px;
}
.star-rating input[type="radio"]:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #ffc107;
}

/* Review Section */
.review-item {
    background-color: #fff;
    margin-bottom: 1rem;
    padding: 1rem;
    border: 1px solid #eaeaea;
    border-radius: 8px;
}
.review-header {
    margin-bottom: 0.5rem;
}
.review-user {
    font-size: 1.1rem;
}
.review-rating i {
    margin-left: 2px;
    font-size: 1rem;
}
.review-date {
    font-size: 0.9rem;
}
.review-form h5 {
    margin-top: 2rem;
    margin-bottom: 1rem;
}

/* Category Card */
.category-card {
    border-radius: 10px;
    overflow: hidden;
}
.category-title {
    font-weight: 700;
    font-size: 1.75rem;
    color: #333;
}
.category-description {
    font-size: 1rem;
    color: #555;
}
.category-details li {
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}
/* Custom Nav Tabs */
.custom-nav-tabs {
    border-bottom: 1px solid #dee2e6;
}
.custom-nav-tabs .custom-nav-link {
    font-size: 1rem;
    font-weight: 500;
    color: #555;
    padding: 0.75rem 1rem;
    border: none;
    background: none;
    cursor: pointer;
}
.custom-nav-tabs .custom-nav-link.active {
    font-weight: 600;
    color: #2bbe46;
    border-bottom: 3px solid #2bbe46;
}
/* Tab Content */
.tab-content {
    border-radius: 0 0 10px 10px;
    background-color: #fff;
}
.tab-description, .tab-reviews {
    font-size: 1rem;
    color: #666;
}
/* Related Products Slider */
.related-products-slider {
    display: flex;
    overflow-x: auto;
    gap: 1rem;
    padding-bottom: 1rem;
}
.related-products-slider::-webkit-scrollbar {
    height: 8px;
}
.related-products-slider::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 4px;
}
.related-product-item {
    flex: 0 0 auto;
    width: 150px;
    background: #f9f9f9;
    border-radius: 8px;
    transition: background-color 0.3s;
}
.related-product-item:hover {
    background-color: #e9ecef;
}
.related-products-slider a {
    text-decoration: none !important;
}
.related-product-item .product-name {
    font-size: 0.95rem;
    margin-top: 0.5rem;
    color: #333;
}
.related-product-item .product-price {
    font-size: 0.9rem;
}
/* Review Section */
.review-item {
    background-color: #fff;
    margin-bottom: 1rem;
    padding: 1rem;
    border: 1px solid #eaeaea;
    border-radius: 8px;
}
.review-header {
    margin-bottom: 0.5rem;
}
.review-user {
    font-size: 1.1rem;
}
.review-rating i {
    margin-left: 2px;
    font-size: 1rem;
}
.review-date {
    font-size: 0.9rem;
}
.review-form h5 {
    margin-top: 2rem;
    margin-bottom: 1rem;
}

/* Responsive */
@media (max-width: 576px) {
    .category-title {
        font-size: 1.5rem;
    }
    .related-product-item {
        width: 120px;
    }
}
</style>
@endsection



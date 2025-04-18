@extends('layouts.app')

@section('content')
<!-- Banner & Breadcrumb -->
<div class="banner-container position-relative mb-4">
    <img src="{{ asset('images/banner/organic-breadcrumb-1.jpg') }}" alt="Banner quảng cáo" class="banner-image w-100" style="height: 130px; object-fit: cover;">
    <div class="banner-overlay position-absolute top-50 start-50 translate-middle text-center">
        <h2 class="breadcrumb-item active">{{ $product->product_name }}</h2>
        <nav aria-label="breadcrumb" class="d-flex justify-content-center">
            <ol class="breadcrumb bg-transparent mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-dark">Trang chủ</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('products.index') }}" class="text-dark">Sản phẩm</a>
                </li>
            </ol>
        </nav>
    </div>
</div>
<style>
 
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
    display: -webkit-box;
    -webkit-line-clamp: 2;         /* Giới hạn tối đa 2 dòng */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;       /* Hiển thị dấu "..." khi vượt quá */
    position: relative;           /* Để tạo pseudo-element */
}


.related-product-item .product-price {
   font-size: 0.9rem;
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
<style>
/* Style cho phần lựa chọn size */
.size-selection .size-option {
        cursor: pointer;
        max-width: 35px; /* Icon nhỏ lại một chút */
        transition: transform 0.3s ease, border 0.3s ease;
        border: 1px solid #ccc; /* Viền xám mờ mặc định */
        border-radius: 50%;
        padding: 2px;
    }
    .size-selection .size-option.selected {
        border: 1px solid #28a745; /* Viền khi được chọn */
        transform: scale(1.1);
    }

    input:focus,
    .form-control:focus {
      outline: none !important;
      box-shadow: none !important;
      border-color: #ced4da !important; /* đặt lại màu viền mặc định */
    }
    
    
        /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
       /* Giảm kích thước ô input */
       .small-input {
        width: 50px;      /* Chiều rộng nhỏ */
        height: 60px;     /* Chiều cao nhỏ */
        padding: 0;
        font-size: 0.875rem;
        line-height: 30px;
      }
      /* Giảm kích thước của các nút */
      .quantity-selector .btn {
        height: 30.45px; 
        width: 30px;      /* Chiều cao nhỏ */
        padding: 0 5px;
        font-size: 0.875rem;
        line-height: 30px;
      }
      /* Giới hạn nhóm input tổng thể */
      .quantity-selector {
        max-width: 70px;
      }
       /* Loại bỏ bo tròn ở góc trên bên trái của nút "+" */
       [data-bs-step="up"] {
        border-top-left-radius: 0 !important;
      }
      /* Loại bỏ bo tròn ở góc dưới bên trái của nút "-" */
      [data-bs-step="down"] {
        border-bottom-left-radius: 0 !important;
      }
      .delivery-date {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    display: flex;
    align-items: center;
    gap: 5px;
}

.delivery-date i {
    color: #ff9800; /* Màu cam cho icon xe tải */
    font-size: 18px;
}
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
.reply-toggle {
    display: inline-block;
    padding: 0.3rem 0.6rem;
    background-color: #f1f1f1;
    color: #333;
    border-radius: 4px;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    transition: background-color 0.3s, color 0.3s;
    cursor: pointer;
}

.reply-toggle:hover {
    background-color: #e2e2e2;
    color: #000;
}

 </style>
      
    
  
  <div class="container">
    <div class="card border-0 mb-5">
      <div class="row g-0">
        <!-- Ảnh sản phẩm với kích thước cố định -->
        <div class="col-md-5 d-flex align-items-center justify-content-center">
          <img src="{{ asset($product->image) }}" alt="{{ $product->product_name }}" class="img-fluid p-3" style="max-height: 100%; object-fit: cover;">
        </div>
        <div class="col-md-7">
          <div class="card-body">
            <h4 class="card-title mb-3">{{ $product->product_name }}</h4>              
            <strong class="card-text">
              <i class="fa-solid fa-fire" style="color: #e60000;"></i> Hurry! 5 người đã thêm vào giỏ hàng
            </strong>
            <div class="mb-4">
              @php
    // Nếu có giá giảm và giá giảm > 0 thì lấy, nếu không thì dùng giá gốc
    $basePrice = ($product->discount_price && $product->discount_price > 0) ? $product->discount_price : $product->price;
@endphp

@if($product->discount_price && $product->discount_price > 0)
    <!-- Giá gốc bị gạch ngang -->
    <p class="mb-2">
        <del class="text-muted fs-5">{{ number_format($product->price, 0, ',', '.') }} ₫</del>
    </p>
    <!-- Giá khuyến mãi lớn hơn -->
    <p class="fw-bold text-danger fs-3" id="product-price-{{ $product->id }}">
        {{ number_format($basePrice, 0, ',', '.') }} ₫
    </p>
@else
    <!-- Nếu không có giá khuyến mãi hoặc giá khuyến mãi = 0 thì hiển thị giá bình thường -->
    <p class="fw-bold text-primary fs-3" id="product-price-{{ $product->id }}">
        {{ number_format($basePrice, 0, ',', '.') }} ₫
    </p>
@endif



              <p class="card-text mb-4 my-3 text-secondary">
                {{ $product->description ?? 'Không có mô tả.' }}
              </p>
              <p class="delivery-date">
                <i class="fa-solid fa-truck"></i> Dự kiến giao hàng: 
                <span id="expected-date"></span>
            </p>
            
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    let today = new Date();
                    let startDate = new Date(today);
                    let endDate = new Date(today);
            
                    startDate.setDate(today.getDate() + 3); // Ngày bắt đầu (hiện tại + 3 ngày)
                    endDate.setDate(today.getDate() + 5); // Ngày kết thúc (hiện tại + 5 ngày)
            
                    function formatDate(date) {
                        return date.toLocaleDateString("vi-VN", { 
                            weekday: "long", 
                            month: "short", 
                            day: "2-digit" 
                        }).replace(',', ''); // Loại bỏ dấu phẩy
                    }
            
                    document.getElementById("expected-date").textContent = `${formatDate(startDate)} – ${formatDate(endDate)}`;
                });
            </script>
            
                                      <!-- Phần chọn size (chỉ hiển thị nếu sản phẩm có đơn vị là "kg") -->
                                      @if (optional($product->unit)->unit_name === 'kg')
                                      <div class="size-selection d-flex gap-2 mt-2">
                                          <img src="{{ asset('images/icon/1kg.jpg') }}" 
                                               alt="1kg" 
                                               class="size-option img-fluid selected" 
                                               data-product-id="{{ $product->id }}" 
                                               data-size="1kg" 
                                               data-price="{{ $basePrice }}">
                                          <img src="{{ asset('images/icon/500g.jpg') }}" 
                                               alt="500g" 
                                               class="size-option img-fluid " 
                                               data-product-id="{{ $product->id }}" 
                                               data-size="500g" 
                                               data-price="{{ $basePrice * 0.5 }}">
                                          <img src="{{ asset('images/icon/250g.jpg') }}" 
                                               alt="250g" 
                                               class="size-option img-fluid " 
                                               data-product-id="{{ $product->id }}" 
                                               data-size="250g" 
                                               data-price="{{ $basePrice * 0.25 }}">
                                      </div>
                                 
                                  @endif
            </div>
            <form class="d-inline add-to-cart-form" data-product-id="{{ $product->id }}">
                @csrf
            <!-- Ô nhập số lượng với nút + và - gắn liền -->
            <div class="mb-4 rounded-3">
              <label for="inputQuantitySelector" class="form-label">Số lượng:</label>
              <div class="input-group quantity-selector">
                <input type="number" id="inputQuantitySelector" class="form-control text-center small-input" name="quantity" value="1" min="1" max="10" step="1">
                <div class="input-group-append">
                  <div class="btn-group-vertical" role="group" aria-label="Stepper">
                    <button type="button" class="btn btn-secondary" data-bs-step="up">
                      <span class="visually-hidden">Tăng số lượng</span>
                      +
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-step="down">
                      <span class="visually-hidden">Giảm số lượng</span>
                      -
                    </button>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Nút Quay lại, Thêm vào giỏ hàng và Mua ngay -->
            <div class="d-flex gap-3">
                <!-- Giá trị mặc định của size là 1kg -->
                <input type="hidden" name="size" class="selected-size-{{ $product->id }}" value="1kg">
               
                <button type="button" class="btn btn-success mx-1 add-to-cart" title="Thêm vào giỏ hàng">
                  <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ hàng
                </button>
            </form>    

            <div class="add-to-cart-form" data-product-id="{{ $product->id }}">
              <button type="button" class="btn btn-primary buy-now">
                <i class="fas fa-bolt me-1"></i> Mua Ngay
              </button>
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Nội Dung với class tùy chỉnh -->
<ul class="nav custom-nav-tabs mt-4" id="categoryTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="custom-nav-link active" id="related-tab" data-bs-toggle="tab" data-bs-target="#related" type="button" role="tab">
      Sản Phẩm Liên Quan
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="custom-nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
      Đánh Giá & Bình Luận
    </button>
  </li>
</ul>

<div class="tab-content border border-0 p-4" id="categoryTabContent">
  <!-- Tab Sản Phẩm Liên Quan -->
  <div class="tab-pane fade show active" id="related" role="tabpanel">
    @if($relatedProducts->isEmpty())
      <p class="text-center p-2">Không tìm thấy sản phẩm nào liên quan!</p>
    @else
      <!-- Slider sản phẩm liên quan -->
      <div class="related-products-slider">
        @foreach($relatedProducts as $relatedProduct)
          <div class="related-product-item text-center p-2">
            <a href="{{ route('products.show', $relatedProduct->id) }}">
              <img src="{{ asset($relatedProduct->image ?? 'images/no-image.png') }}" 
                   alt="{{ $relatedProduct->product_name }}" 
                   class="img-fluid rounded" 
                   style="height: 150px; width: 150px;">
              <p class="mt-2 mb-0 text-dark fw-bold product-name">
                {{ $relatedProduct->product_name }}
              </p>
              <p class="text-danger fw-bold">
                {{ number_format($relatedProduct->price, 0, ',', '.') }}₫
              </p>
            </a>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  <!-- Tab Đánh Giá & Bình Luận Sản Phẩm -->
  <div class="tab-pane fade" id="reviews" role="tabpanel">
    <div class="reviews-section">
      <h4 class="mb-3">Đánh Giá & Bình Luận Sản Phẩm</h4>

      <!-- Hiển thị danh sách đánh giá -->
      @if($reviews->isEmpty())
        <p class="text-center p-4">Chưa có đánh giá. Hãy là người đầu tiên để lại bình luận!</p>
      @else
      <div class="reviews-list mb-4">
        @foreach($reviews as $review)
        <div id="comment-{{ $review->id }}" class="review-item border rounded p-3 mb-3">
            <div class="review-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <!-- Avatar người dùng -->
                    <img src="{{ $review->user && $review->user->avatar ? asset($review->user->avatar) : asset('images/avatars/avtdf.jpg') }}" 
                         alt="Avatar" class="rounded-circle me-2" width="40" height="40">
    
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
                </div>
                <div class="d-flex align-items-center">
                    <span class="review-date text-muted me-2">
                        {{ $review->created_at->format('d/m/Y') }}
                    </span>
                    <!-- Chỉ hiển thị nút xóa nếu người dùng đăng nhập chính là tác giả của bình luận -->
                    @if(Auth::check() && $review->user && Auth::id() === $review->user->id)
                    <form action="{{ route('product.comments.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="review-body mt-2">
                <p>{{ $review->comment }}</p>
            </div>
    
            <!-- Nút "Trả lời" -->
            <div class="review-actions mt-2">
                <a href="javascript:void(0);" class="reply-toggle" data-comment-id="{{ $review->id }}">
                    Trả lời
                </a>
            </div>
    
            <!-- Form trả lời (ẩn mặc định) -->
            <div class="reply-form mt-2" id="reply-form-{{ $review->id }}" style="display: none;">
                <form action="{{ route('product.comments.reply', $review->id) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <textarea name="comment" class="form-control" placeholder="Nhập phản hồi..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Gửi phản hồi</button>
                </form>
            </div>
    
            <!-- Hiển thị các phản hồi -->
            @if($review->replies->isNotEmpty())
                <div class="replies ml-4 mt-3">
                    @foreach($review->replies as $reply)
                    <div id="comment-{{ $reply->id }}" class="review-item border rounded p-3 mb-3">
                        <div class="review-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <!-- Avatar của người trả lời -->
                                <img src="{{ $reply->user && $reply->user->avatar ? asset($reply->user->avatar) : asset('images/avatars/avtdf.jpg') }}" 
                                     alt="Avatar" class="rounded-circle me-2" width="40" height="40">
    
                                <span class="review-user fw-bold">
                                    {{ $reply->user ? $reply->user->name : 'Ẩn danh' }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="review-date text-muted me-2">
                                    {{ $reply->created_at->format('d/m/Y') }}
                                </span>
                                <!-- Hiển thị nút xóa nếu người dùng đăng nhập là tác giả của phản hồi -->
                                @if(Auth::check() && $reply->user && Auth::id() === $reply->user->id)
                                <form action="{{ route('product.comments.destroy', $reply->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phản hồi này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        <div class="review-body mt-2">
                            <p>{{ $reply->comment }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
        @endforeach
    </div>
    
      
      
      @endif

      <div class="review-form">
        <h5>Để lại đánh giá của bạn</h5>
        <form action="{{ route('product.comments.store', $product->id) }}" method="POST">
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

<!-- JavaScript để kích hoạt tab "Đánh Giá & Bình Luận" nếu URL có anchor "comment-" -->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    if(window.location.hash && window.location.hash.includes("comment-")){
      var reviewsTabTrigger = document.querySelector('#reviews-tab');
      if(reviewsTabTrigger) {
        var tab = new bootstrap.Tab(reviewsTabTrigger);
        tab.show();
        // Sau khi tab được hiển thị, cuộn đến bình luận có id trùng với hash
        setTimeout(function(){
          var commentElement = document.querySelector(window.location.hash);
          if(commentElement) {
            commentElement.scrollIntoView({ behavior: "smooth", block: "start" });
          } else {
            // Nếu bình luận đã bị xóa (không tìm thấy), cuộn về container danh sách bình luận
            var reviewsList = document.querySelector('.reviews-list');
            if(reviewsList) {
              reviewsList.scrollIntoView({ behavior: "smooth", block: "start" });
            }
          }
        }, 300);
      }
    }
  });
  </script>
  

<script>
  document.addEventListener("DOMContentLoaded", function() {
      const replyToggles = document.querySelectorAll('.reply-toggle');
      replyToggles.forEach(function(toggle) {
          toggle.addEventListener('click', function() {
              const commentId = this.getAttribute('data-comment-id');
              const replyForm = document.getElementById('reply-form-' + commentId);
              if (replyForm.style.display === 'none' || replyForm.style.display === '') {
                  replyForm.style.display = 'block';
              } else {
                  replyForm.style.display = 'none';
              }
          });
      });
  });
  </script>
  



  <!-- Script xử lý sự kiện cho nút tăng/giảm và Thêm vào giỏ hàng -->

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
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Xử lý nút tăng/giảm số lượng
    const quantityInput = document.getElementById('inputQuantitySelector');
    const btnUp = document.querySelector('[data-bs-step="up"]');
    const btnDown = document.querySelector('[data-bs-step="down"]');
  
    btnUp.addEventListener('click', function() {
      let currentValue = parseInt(quantityInput.value) || 1;
      let max = parseInt(quantityInput.max) || 10;
      let step = parseInt(quantityInput.step) || 1;
      if (currentValue < max) {
        quantityInput.value = currentValue + step;
      }
    });
  
    btnDown.addEventListener('click', function() {
      let currentValue = parseInt(quantityInput.value) || 1;
      let min = parseInt(quantityInput.min) || 1;
      let step = parseInt(quantityInput.step) || 1;
      if (currentValue > min) {
        quantityInput.value = currentValue - step;
      }
    });
  
  });
  </script>

  <script>
// Xử lý nút "Mua Ngay"
  const buyNowButtons = document.querySelectorAll('.buy-now');
  buyNowButtons.forEach(function(button) {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      // Lấy productId từ container của nút "Mua Ngay" (không phải form)
      const container = this.closest('.add-to-cart-form');
      const productId = container.getAttribute('data-product-id');
      
      // Tìm form chứa input quantity và size có cùng productId
      const form = document.querySelector('form.add-to-cart-form[data-product-id="' + productId + '"]');
      let size = '';
      let quantity = 1;
      if (form) {
        size = form.querySelector('input[name="size"]').value;
        quantity = form.querySelector('input[name="quantity"]').value;
      }
      
      fetch(`/cart/add/${productId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          size: size,
          quantity: quantity
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Chuyển hướng đến trang thanh toán ngay sau khi thêm thành công
          window.location.href = "{{ route('cart.checkout') }}";
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: data.message
          });
        }
      })
      .catch(error => {
        console.error("Error:", error);
        Swal.fire({
          icon: 'error',
          title: 'Lỗi',
          text: 'Có lỗi xảy ra, vui lòng thử lại.'
        });
      });
    });
  });

  </script>
  
@endsection
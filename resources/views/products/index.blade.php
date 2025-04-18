@extends('layouts.app')

@section('content')
   <!-- Banner & Breadcrumb -->
<div class="banner-container position-relative mb-4">
    <img src="{{ asset('images/banner/organic-breadcrumb-1.jpg') }}" alt="Banner quảng cáo" class="banner-image w-100" style="height: 130px; object-fit: cover;">
    <div class="banner-overlay position-absolute top-50 start-50 translate-middle text-center">
        <h2 class="text-dark">Sản phẩm</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-dark">Trang chủ</a></li>
                
            </ol>
        </nav>
    </div>
</div>

@php
    $currentSort = request('sort') ?? '';
    switch ($currentSort) {
        case 'featured':
            $sortText = 'Phổ biến';
            break;
        case 'newest':
            $sortText = 'Mới nhất';
            break;
        case 'price_asc':
            $sortText = 'Giá: Tăng dần';
            break;
        case 'price_desc':
            $sortText = 'Giá: Thấp dần';
            break;
        default:
            $sortText = 'Mặc định';
            break;
    }
@endphp

<div class="container position-relative">
  <div class="row">
    <!-- Sidebar bộ lọc -->
    <div class="col-md-2 mt-5">
      <div class="filter-sidebar p-4 border rounded">
        <h5 class="mb-4">Bộ lọc</h5>
        <form id="filterForm" method="GET" action="{{ route('products.index') }}">
          <!-- Input hidden để giữ lại giá trị sort -->
          <input type="hidden" name="sort" value="{{ request('sort', '') }}">
            <!-- 🆕 Giữ lại category đang chọn -->
      <input type="hidden" name="category" value="{{ request('category', '') }}">
          <!-- Danh mục hiển thị dạng tag -->
<div class="mb-3">
  <label class="form-label">Danh mục</label>
  <div>
    @php $currentCategory = request('category'); @endphp

    <!-- Mặc định: Tất cả -->
    <a href="{{ route('products.index', collect(request()->all())->except('category')->toArray()) }}"
       class="btn btn-outline-secondary mb-1 {{ empty($currentCategory) ? 'active' : '' }}">
      Tất cả
    </a>

    @foreach ($categories as $category)
      <a href="{{ route('products.index', array_merge(request()->all(), ['category' => $category->id])) }}"
         class="btn btn-outline-secondary mb-1 {{ $currentCategory == $category->id ? 'active' : '' }}">
        {{ $category->category_name }}
        <span class="badge bg-secondary">{{ $category->product_count ?? 0 }}</span>
      </a>
    @endforeach
  </div>
</div>

      
          <!-- Lọc theo khoảng giá -->
          <div class="mb-3">
            <label for="price_range" class="form-label">Chọn khoảng giá:</label>
            <div id="price_slider"></div>
            <!-- Các input ẩn để gửi giá trị -->
            <input type="hidden" name="min_price" id="min_price" value="{{ request('min_price', 0) }}">
            <input type="hidden" name="max_price" id="max_price" value="{{ request('max_price', 1000000) }}">
            <p class="mt-2">
              <span id="price_range_display"></span>
            </p>
          </div>
      
          <!-- Lọc theo tình trạng -->
          <div class="mb-3">
            <label for="condition" class="form-label">Tình trạng</label>
            <select name="condition" id="condition" class="form-select">
              <option value="">Tất cả</option>
              <option value="sale" {{ request('condition') == 'sale' ? 'selected' : '' }}>Sale</option>
              <option value="featured" {{ request('condition') == 'featured' ? 'selected' : '' }}>Phổ biến</option>
              <option value="in-stock" {{ request('condition') == 'in-stock' ? 'selected' : '' }}>Còn hàng</option>
            </select>
          </div>
      
          <!-- Nút submit bị loại bỏ vì form tự động submit khi thay đổi -->
        </form>
      </div>
    </div>

    <script>
      $(function() {
        // Auto submit cho các input (ngoại trừ hidden) và select khi thay đổi
        $("#filterForm").find("input:not([type='hidden']), select").on("change", function() {
          $("#filterForm").submit();
        });
        
        var minPrice = parseFloat($("#min_price").val()) || 0;
        var maxPrice = parseFloat($("#max_price").val()) || 1000000;
        
        $("#price_slider").slider({
          range: true,
          min: 0,
          max: 1000000,
          values: [minPrice, maxPrice],
          slide: function(event, ui) {
            $("#price_range_display").text(
              ui.values[0].toLocaleString('vi-VN') + "₫ - " + ui.values[1].toLocaleString('vi-VN') + "₫"
            );
            $("#min_price").val(ui.values[0]);
            $("#max_price").val(ui.values[1]);
          },
          stop: function(event, ui) {
            // Khi slider dừng thay đổi, tự động submit form
            $("#filterForm").submit();
          }
        });
        
        // Cập nhật hiển thị ban đầu cho slider
        $("#price_range_display").text(
          minPrice.toLocaleString('vi-VN') + "₫ - " + maxPrice.toLocaleString('vi-VN') + "₫"
        );
      });
    </script>
        
    <!-- Nội dung chính: Sắp xếp và danh sách sản phẩm -->
    <div class="col-md-10">
      <!-- Dropdown Sắp xếp -->
      <div class="dropdown mb-4">
        <div class="sort-dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button">
            Sắp xếp: {{ $sortText }}
          </button>
          <ul class="dropdown-menu">
            <li>
              <a class="dropdown-item {{ $currentSort == '' ? 'active' : '' }}" 
                 href="{{ route('products.index', array_merge(request()->all(), ['sort' => ''])) }}">
                Mặc định
              </a>
            </li>
            <li>
              <a class="dropdown-item {{ $currentSort == 'featured' ? 'active' : '' }}" 
                 href="{{ route('products.index', array_merge(request()->all(), ['sort' => 'featured'])) }}">
                Phổ biến
              </a>
            </li>
            <li>
              <a class="dropdown-item {{ $currentSort == 'newest' ? 'active' : '' }}" 
                 href="{{ route('products.index', array_merge(request()->all(), ['sort' => 'newest'])) }}">
                Mới nhất
              </a>
            </li>
            <li>
              <a class="dropdown-item {{ $currentSort == 'price_asc' ? 'active' : '' }}" 
                 href="{{ route('products.index', array_merge(request()->all(), ['sort' => 'price_asc'])) }}">
                Giá: Tăng dần
              </a>
            </li>
            <li>
              <a class="dropdown-item {{ $currentSort == 'price_desc' ? 'active' : '' }}" 
                 href="{{ route('products.index', array_merge(request()->all(), ['sort' => 'price_desc'])) }}">
                Giá: Thấp dần
              </a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Danh sách sản phẩm -->
      <div class="row justify-content-center mt-5">
        @foreach ($products as $product)
          @php
              // Sử dụng discount_price nếu có và > 0, nếu không dùng price
              $basePrice = ($product->discount_price > 0) ? $product->discount_price : $product->price;
          @endphp
          <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card product-card">
              <!-- Hiển thị phần trăm giảm giá nếu có -->
              @if ($product->discount_price > 0 && $product->price > 0)
                <div class="discount-badge position-absolute bg-danger text-white rounded-circle">
                  -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                </div>
              @endif

              <!-- Ảnh sản phẩm -->
              <div class="position-relative product-image-container">
                <img 
                  src="{{ $product->image ? asset($product->image) : 'https://via.placeholder.com/250x250' }}" 
                  class="card-img-top product-image" 
                  alt="{{ $product->product_name }}">
                <div class="hover-icons position-absolute w-100 d-flex justify-content-center">
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

              <!-- Nội dung sản phẩm -->
              <div class="card-body text-center">
                <div class="product-info">
                  <a href="{{ route('products.show', $product->id) }}" class="card-title-link">
                    <h6 class="card-title text-truncate mb-2">{{ $product->product_name }}</h6>
                  </a>
                  <p class="card-text mb-2">
                    @if ($product->discount_price > 0)
                      <span class="text-muted text-decoration-line-through">
                        {{ number_format($product->price, 0, ',', '.') }}₫
                      </span>
                    @endif
                    <span class="text-danger font-weight-bold product-price" id="product-price-{{ $product->id }}">
                      {{ number_format($basePrice, 0, ',', '.') }}₫
                    </span>
                  </p>
                </div>
                
                @if(optional($product->unit)->unit_name === 'kg')
                  <div class="size-selection d-flex justify-content-center gap-2 mt-2">
                    <img src="{{ asset('images/icon/1kg.jpg') }}" alt="1kg" class="size-option img-fluid rounded selected" data-product-id="{{ $product->id }}" data-size="1kg" data-price="{{ $basePrice }}">
                    <img src="{{ asset('images/icon/500g.jpg') }}" alt="500g" class="size-option img-fluid rounded" data-product-id="{{ $product->id }}" data-size="500g" data-price="{{ $basePrice * 0.5 }}">
                    <img src="{{ asset('images/icon/250g.jpg') }}" alt="250g" class="size-option img-fluid rounded" data-product-id="{{ $product->id }}" data-size="250g" data-price="{{ $basePrice * 0.25 }}">
                  </div>
                @elseif(optional($product->unit)->unit_name === 'túi')
                  <div class="bag-option text-center mt-2">
                    /1 <i class="fa-solid fa-bag-shopping" style="color: #45d66c;"></i>
                  </div>
                @endif
                
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
  <div class="d-flex justify-content-center mt-4">
    {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
  </div>
</div>

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


<!-- CSS Styles -->
<style>
  /* Custom pagination styles for a modern, agricultural green theme */
.pagination {
    display: flex;
    justify-content: center;
    padding-left: 0;
    list-style: none;
    border-radius: 0.25rem;
    font-family: 'Roboto', sans-serif;
    margin-top: 20px;
}

.pagination .page-item a,
.pagination .page-item span {
    color: #28a745; /* Màu xanh lá chủ đạo */
    border: 1px solid #28a745;
    margin: 0 4px;
    padding: 8px 12px;
    text-decoration: none;
    background-color: #fff;
    transition: background-color 0.3s, color 0.3s, box-shadow 0.3s;
    border-radius: 4px;
}

.pagination .page-item a:hover,
.pagination .page-item span:hover {
    background-color: #28a745;
    color: #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.pagination .page-item.active span {
    background-color: #28a745;
    border-color: #28a745;
    color: #fff;
    font-weight: bold;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.pagination .page-item.disabled span {
    color: #6c757d;
    border-color: #dee2e6;
    pointer-events: none;
    background-color: #fff;
}
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
        height: 240px; /* Tăng chiều cao của ảnh lên 20px so với trước */
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
        height: 130px; /* Điều chỉnh chiều cao để chứa cả thông tin và lựa chọn size */
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
        max-width: 30px; /* Icon nhỏ lại một chút */
        transition: transform 0.3s ease, border 0.3s ease;
        border: 1px solid #ccc; /* Viền xám mờ mặc định */
        border-radius: 50%;
        padding: 2px;
    }
    .size-selection .size-option.selected {
        border: 1px solid #28a745; /* Viền khi được chọn */
        transform: scale(1.1);
    }

   

    .sort-dropdown {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1000;
}

/* Ẩn menu mặc định, dùng opacity, visibility và transform để tạo hiệu ứng chuyển động */
.sort-dropdown .dropdown-menu {
    display: block;  /* Cho phép các hiệu ứng chuyển động hoạt động */
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: opacity 0.3s ease, transform 0.3s ease;
    position: absolute;
    top: 100%;
    right: 0;
    margin: 0;
    padding: 0.5rem 0;
    background-color: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
}

/* Khi hover vào .sort-dropdown thì hiển thị menu với hiệu ứng */
.sort-dropdown:hover .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* Style cho các mục dropdown */
.sort-dropdown .dropdown-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease, color 0.3s ease;
    color: #888; /* Mặc định: chữ xám mờ */
    font-size: 14px;
}

/* Khi hover: nền chuyển sang #f8f9fa, chữ chuyển thành đen */
.sort-dropdown .dropdown-item:hover {
    background-color: #f8f9fa;
    color: #000;
}

/* Mục đang chọn: gộp style giống hover */
.sort-dropdown .dropdown-item.active {
    background-color: #f8f9fa;
    color: #000;
    font-weight: normal;
}


.sort-dropdown .dropdown-toggle {
    background: none !important;  /* Loại bỏ nền */
    border: none !important;       /* Loại bỏ viền */
    box-shadow: none !important;   /* Loại bỏ đổ bóng nếu có */
    color: inherit;                /* Dùng màu chữ của phần tử cha (hoặc bạn có thể đặt màu cụ thể) */
    font-size: 15px;
    font-weight: bold;

}

.sort-dropdown .dropdown-toggle:hover,
.sort-dropdown .dropdown-toggle:focus {
    background: none !important;
    border: none !important;
    box-shadow: none !important;
}


/* Sidebar Bộ Lọc */
.filter-sidebar {
  background-color: #ffffff;
  border: none;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  border-radius: 4px; /* Bo nhẹ */
  padding: 20px;
  margin-bottom: 30px;
}

.filter-sidebar h5 {
  font-size: 1.5rem;
  color: #333;
  margin-bottom: 20px;
  font-weight: 600;
}

/* Danh mục hiển thị dạng tag */
.filter-sidebar a.btn {
  border-radius: 4px; /* Hình chữ nhật với bo nhẹ */
  border: 1px solid #28a745; /* Đường viền xanh lá */
  background-color: #f8f8f8;
  color: #333;
  padding: 6px 16px;
  font-size: 0.9rem;
  margin: 0 5px 5px 0;
  transition: all 0.3s ease;
  text-decoration: none;
}

.filter-sidebar a.btn:hover,
.filter-sidebar a.btn.active {
  background-color: #28a745;
  border-color: #28a745;
  color: #fff;
}

/* Số lượng (badge) */
.filter-sidebar .badge {
  background-color: #218838; /* Sắc thái xanh lá đậm */
  color: #fff;
  font-size: 0.75rem;
  border-radius: 4px; /* Bo nhẹ */
  padding: 2px 8px;
  margin-left: 5px;
}

/* Style cho select input */
.filter-sidebar .form-select {
  border: 1px solid #ced4da;
  border-radius: 4px;
  background-color: #fff;
  height: 40px;
  padding: 0 10px;
}

/* Khoảng cách cho slider giá */
.filter-sidebar #price_slider {
  margin-bottom: 10px;
}

/* Hiển thị khoảng giá */
.filter-sidebar p.mt-2 {
  font-size: 0.9rem;
  color: #555;
}

/* Button lọc sản phẩm */
.filter-sidebar button.btn-success {
  background-color: #28a745;
  border: none;
  font-weight: bold;
  border-radius: 4px;
  padding: 10px;
  width: 100%;
  transition: background-color 0.3s ease;
}

.filter-sidebar button.btn-success:hover {
  background-color: #218838;
}



</style>

@endsection

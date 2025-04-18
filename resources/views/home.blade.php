@extends('layouts.app1')

@section('content')

<div class="container">
    <!-- Banner quảng cáo -->
    <div class="banner-container position-relative mb-5 my-4">
        <!-- Banner ảnh -->
        <img src="{{ asset('images/banner/banner1.jpg') }}" alt="Banner quảng cáo" class="banner-image w-100">
        <!-- Nội dung quảng cáo -->
        <div class="banner-content position-absolute text-white text-left">
            <h2 class="banner-title mb-3">
                Trang trại <span class="highlight">Thực phẩm </span> 
            </h2>
            <h2 class="banner-title mb-3">
                <span class="highlight">tươi sạch &</span>
                <strong>100% Hữu cơ</strong>
            </h2>
            <p class="banner-text mb-4">Always fresh organic products for you</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary banner-button">MUA NGAY</a>
        </div>

        <!-- Nút điều hướng -->
        <button class="banner-arrow banner-arrow-left position-absolute">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="banner-arrow banner-arrow-right position-absolute">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</div>

<style>
    /* Lớp tròn bên ngoài */
    .outer-circle {
      width: 110px;
      height: 110px;
      border: 0px solid #ccc;  /* Viền ngoài, thay đổi màu/độ dày nếu cần */
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      flex-shrink: 0; /* Ngăn không cho phần tử co lại */
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Thêm hiệu ứng đổ bóng */
      transition: border-color 0.3s ease, transform 0.3s ease;
    }
    /* Lớp tròn bên trong */    
    .inner-circle {
      width: 80px;
      height: 80px;
      border: 1.7px dashed rgba(0, 0, 0, 0.1);  /* Viền nét đứt mờ */
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: border-color 0.3s ease, transform 0.3s ease;
    }
    /* Khi hover lên outer-circle, lớp inner-circle hiển thị viền xanh lá, xoay tròn và hơi lớn lên */
    .outer-circle:hover .inner-circle {
        border-color: #28a745; /* Màu viền xanh lá */
        border: 0px solid #ccc; 
        transition: border-color 0.3s ease, transform 0.3s ease;
    }
    /* Định nghĩa keyframes cho hiệu ứng xoay chỉ áp dụng cho viền bên trong */
    .outer-circle:hover .inner-circle::before {
        
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 100%;
      height: 100%;
      border: 1.7px dashed #28a745;
      border-radius: 50%;
      transform: translate(-50%, -50%);
      animation: rotateLoading 4s linear infinite; /* Giảm tốc độ xoay xuống */
      
    }
    /* Định nghĩa keyframes cho hiệu ứng xoay */
    @keyframes rotateLoading {
      from { transform: translate(-50%, -50%) rotate(0deg); }
      to { transform: translate(-50%, -50%) rotate(360deg); }
    }
  </style>
  
  <div class="container">
    <div class="row g-3">
      <!-- Mục 1: Tươi sạch -->
      <div class="col-lg-3 col-md-6 d-flex justify-content-center">
        <div class="d-flex align-items-start">
          <!-- Outer circle chứa inner circle -->
          <div class="outer-circle">
            <div class="inner-circle">
              <img src="images/icon/1.png" alt="Tươi sạch" style="width:50px;">
            </div>
          </div>
          <!-- Phần chữ mô tả bên cạnh -->
          <div class="ms-2" style="margin-top:10px;">
            <h6 class="mb-1">Tươi sạch</h6>
            <p class="mb-0 small text-muted">Sản phẩm luôn tươi mới và đảm bảo vệ sinh an toàn.</p>
          </div>
        </div>
      </div>
      
      <!-- Mục 2: Hữu cơ -->
      <div class="col-lg-3 col-md-6 d-flex justify-content-center">
        <div class="d-flex align-items-start">
          <div class="outer-circle">
            <div class="inner-circle">
              <img src="images/icon/2.png" alt="Hữu cơ" style="width:50px;">
            </div>
          </div>
          <div class="ms-2" style="margin-top:10px;">
            <h6 class="mb-1">Hữu cơ</h6>
            <p class="mb-0 small text-muted">Sản phẩm được trồng theo tiêu chuẩn hữu cơ, không dùng hóa chất.</p>
          </div>
        </div>
      </div>
      
      <!-- Mục 3: Chất lượng -->
      <div class="col-lg-3 col-md-6 d-flex justify-content-center">
        <div class="d-flex align-items-start">
          <div class="outer-circle">
            <div class="inner-circle">
              <img src="images/icon/3.png" alt="Chất lượng" style="width:50px;">
            </div>
          </div>
          <div class="ms-2" style="margin-top:10px;">
            <h6 class="mb-1">Chất lượng</h6>
            <p class="mb-0 small text-muted">Chúng tôi cam kết kiểm định nghiêm ngặt để đảm bảo chất lượng sản phẩm.</p>
          </div>
        </div>
      </div>
      
      <!-- Mục 4: Tự nhiên -->
      <div class="col-lg-3 col-md-6 d-flex justify-content-center">
        <div class="d-flex align-items-start">
          <div class="outer-circle">
            <div class="inner-circle">
              <img src="images/icon/4.png" alt="Tự nhiên" style="width:50px;">
            </div>
          </div>
          <div class="ms-2" style="margin-top:10px;">
            <h6 class="mb-1">Tự nhiên</h6>
            <p class="mb-0 small text-muted">Sản phẩm giữ nguyên hương vị tự nhiên, không qua xử lý hóa học.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  


  
<div class="container px-4">
    <h3 class="my-4 text-left ms-4">Sản phẩm chất lượng</h3>
    <div class="row my-4">
        <!-- Cột chứa banner dọc và các sản phẩm -->
        <div class="col-lg-3 col-md-4 mb-4">
    <!-- Banner dọc -->
    <div class="banner-dock position-relative">
        <img src="{{ asset('images/banner/bannerdoc1.jpg') }}" class="img-fluid w-100" alt="Banner dọc">
        <!-- Nội dung đè lên banner -->
        <div class="banner-overlay position-absolute text-center">
            <h5 class="text-white mb-3">Trái cây tự nhiên mỗi ngày</h5>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Mua Ngay</a>
        </div>
    </div>
</div>


<!-- Cột sản phẩm -->
<div class="col-lg-9 col-md-8">
    <div class="row">
        @foreach ($products->where('featured', 1)->sortByDesc('created_at')->take(8) as $product)

            @php
                              // Lấy giá cơ bản: nếu có discount_price và lớn hơn 0 thì dùng, nếu không thì dùng price
$basePrice = ($product->discount_price && $product->discount_price > 0) ? $product->discount_price : $product->price;
            @endphp
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card product-card">
        <!-- Hiển thị phần trăm giảm giá nếu có -->
@if ($product->price > 0 && $product->discount_price > 0 && $product->discount_price < $product->price)
<div class="discount-badge position-absolute bg-danger text-white rounded-circle">
    -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
</div>
@endif


                    <!-- Phần ảnh sản phẩm -->
                    <div class="position-relative product-image-container">
                        <img 
                            src="{{ $product->image ? asset($product->image) : 'https://via.placeholder.com/250x250' }}" 
                            class="card-img-top product-image" 
                            alt="{{ $product->product_name }}">

                        <!-- Hover icons -->
                        <div class="hover-icons position-absolute w-100 d-flex justify-content-center">
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-light rounded-circle mx-1" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form class="d-inline add-to-cart-form" data-product-id="{{ $product->id }}">
                                @csrf
                                <!-- Giá trị mặc định của size là 1kg -->
                                <input type="hidden" name="size" class="selected-size-{{ $product->id }}" value="1kg">
                                <!-- Giá trị mặc định của số lượng là 1 -->
                                <input type="hidden" name="quantity" value="1">
                                <button type="button" class="btn btn-success rounded-circle mx-1 add-to-cart" title="Thêm vào giỏ hàng">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </form>
                            
                            
                        </div>
                    </div>

                    <!-- Nội dung thẻ sản phẩm -->
                    <div class="card-body text-center">
                        <a href="{{ route('products.show', $product->id) }}" class="card-title-link">
                            <h6 class="card-title text-truncate mb-2">{{ $product->product_name }}</h6>
                        </a>
                        <p class="card-text mb-2">
                            @if ($product->discount_price && $product->discount_price > 0)
                                <span class="text-muted text-decoration-line-through">
                                    {{ number_format($product->price, 0, ',', '.') }}₫
                                </span>
                            @endif
                            <!-- Giá hiển thị mặc định tính theo 1kg -->
                            <span class="text-danger font-weight-bold product-price" id="featured-product-price-{{ $product->id }}">
                                {{ number_format($basePrice, 0, ',', '.') }}₫
                            </span>
                            
                        </p>
                        
                        <!-- Phần chọn size (chỉ hiển thị nếu sản phẩm có đơn vị là "kg") -->
                        @if(optional($product->unit)->unit_name === 'kg')
    <div class="size-selection d-flex justify-content-center gap-2 mt-2">
        <img src="{{ asset('images/icon/1kg.jpg') }}" 
             alt="1kg" 
             class="size-option img-fluid rounded selected" 
             data-product-id="{{ $product->id }}" 
             data-size="1kg" 
             data-price="{{ $basePrice }}">
        <img src="{{ asset('images/icon/500g.jpg') }}" 
             alt="500g" 
             class="size-option img-fluid rounded" 
             data-product-id="{{ $product->id }}" 
             data-size="500g" 
             data-price="{{ $basePrice * 0.5 }}">
        <img src="{{ asset('images/icon/250g.jpg') }}" 
             alt="250g" 
             class="size-option img-fluid rounded" 
             data-product-id="{{ $product->id }}" 
             data-size="250g" 
             data-price="{{ $basePrice * 0.25 }}">
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
</div>
    </div>
</div>

<div class="container">
    <div class="banner-container position-relative mb-4">
        <img src="{{ asset('images/banner/organic2-h-banner.jpg') }}" alt="Banner quảng cáo" class="banner-image w-100" style="height: 150px; object-fit: cover;">
        <div class="position-absolute top-50 start-50 translate-middle text-center">
            <h3 class="text-danger">20% OFF</h3>
            <h4 class="text-dark">Rau củ hữu cơ</h4>
            <a href="{{ route('products.index') }}" class="btn btn-link text-success text-decoration-none">MUA NGAY</a>

        </div>
    </div>
</div>

<div class="container px-4">
    <h3 class="my-4 text-left ms-3">Bán chạy</h3>
    <div class="row my-4">


<!-- Cột sản phẩm -->
<div class="col-lg-9 col-md-8">
    <div class="row">
        @foreach ($bestSellingProducts as $product)
            @php
               // Lấy giá cơ bản: nếu có discount_price và lớn hơn 0 thì dùng, nếu không thì dùng price
$basePrice = ($product->discount_price && $product->discount_price > 0) ? $product->discount_price : $product->price;
            @endphp
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card product-card">
                   <!-- Hiển thị phần trăm giảm giá nếu có -->
@if ($product->price > 0 && $product->discount_price > 0 && $product->discount_price < $product->price)
<div class="discount-badge position-absolute bg-danger text-white rounded-circle">
    -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
</div>
@endif


                    <!-- Phần ảnh sản phẩm -->
                    <div class="position-relative product-image-container">
                        <img 
                            src="{{ $product->image ? asset($product->image) : 'https://via.placeholder.com/250x250' }}" 
                            class="card-img-top product-image" 
                            alt="{{ $product->product_name }}">

                        <!-- Hover icons -->
                        <div class="hover-icons position-absolute w-100 d-flex justify-content-center">
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-light rounded-circle mx-1" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form class="d-inline add-to-cart-form" data-product-id="{{ $product->id }}">
                                @csrf
                                <!-- Giá trị mặc định của size là 1kg -->
                                <input type="hidden" name="size" class="selected-size-{{ $product->id }}" value="1kg">
                                <!-- Giá trị mặc định của số lượng là 1 -->
                                <input type="hidden" name="quantity" value="1">
                                <button type="button" class="btn btn-success rounded-circle mx-1 add-to-cart" title="Thêm vào giỏ hàng">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </form>
                            
                            
                        </div>
                    </div>

                    <!-- Nội dung thẻ sản phẩm -->
                    <div class="card-body text-center">
                        <a href="{{ route('products.show', $product->id) }}" class="card-title-link">
                            <h6 class="card-title text-truncate mb-2">{{ $product->product_name }}</h6>
                        </a>
                        <p class="card-text mb-2">
                            @if ($product->discount_price && $product->discount_price > 0)
                                <span class="text-muted text-decoration-line-through">
                                    {{ number_format($product->price, 0, ',', '.') }}₫
                                </span>
                            @endif
                            <!-- Giá hiển thị mặc định tính theo 1kg -->
                            <span class="text-danger font-weight-bold product-price" id="bestselling-product-price-{{ $product->id }}">
                                {{ number_format($basePrice, 0, ',', '.') }}₫
                            </span>
                        </p>
                        
                        <!-- Phần chọn size (chỉ hiển thị nếu sản phẩm có đơn vị là "kg") -->
                        @if (optional($product->unit)->unit_name === 'kg')
    <div class="size-selection d-flex justify-content-center gap-2 mt-2">
        <img src="{{ asset('images/icon/1kg.jpg') }}" 
             alt="1kg" 
             class="size-option img-fluid rounded selected" 
             data-product-id="{{ $product->id }}" 
             data-size="1kg" 
             data-price="{{ $basePrice }}">
        <img src="{{ asset('images/icon/500g.jpg') }}" 
             alt="500g" 
             class="size-option img-fluid rounded" 
             data-product-id="{{ $product->id }}" 
             data-size="500g" 
             data-price="{{ $basePrice * 0.5 }}">
        <img src="{{ asset('images/icon/250g.jpg') }}" 
             alt="250g" 
             class="size-option img-fluid rounded" 
             data-product-id="{{ $product->id }}" 
             data-size="250g" 
             data-price="{{ $basePrice * 0.25 }}">
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
</div>

        <!-- Cột chứa banner dọc và các sản phẩm -->
        <div class="col-lg-3 col-md-4 mb-4">
    <!-- Banner dọc -->
    <div class="banner-dock position-relative">
        <img src="{{ asset('images/banner/bannerdoc2.jpg') }}" class="img-fluid w-100" alt="Banner dọc">
        <!-- Nội dung đè lên banner -->
        <div class="banner-overlay position-absolute text-center my-5">
            <h5 class="text-dark">Thực phẩm bán chạy</h5>
            <a href="{{ route('products.index') }}" class="btn btn-success my-4">Mua Ngay</a>
        </div>
    </div>
</div>


<div class="container my-3">
    <div class="row">
      <!-- Ảnh 1 -->
<div class="col-md-4 mb-3">
    <div class="card text-white organic">
      <img src="{{ asset('images/banner/organic1.jpg') }}" class="card-img" alt="Organic 1">
      <div class="card-img-overlay d-flex flex-column justify-content-center align-items-start p-4">
        <h4 class="text-dark mb-2">Rau củ tươi sạch</h4>
        <h6 class="text-dark mb-3">
          Sale off <span style="color: #ff0000">25%</span>
        </h6>
        <a href="{{ route('products.index') }}" class="btn btn-primary banner-button">Mua Ngay</a>
      </div>
    </div>
  </div>
  
      <!-- Ảnh 2 -->
      <div class="col-md-4 mb-3">
        <div class="card text-white organic">
            <img src="{{ asset('images/banner/organic2.jpg') }}" class="card-img" alt="Organic 1">
            <div class="card-img-overlay d-flex flex-column justify-content-center align-items-start p-4">
              <h5 class="text-dark mb-2">Dầu táo Mona</h5>
              <p class="text-secondary">Best product to make<br>
                your favor</p></p>
              <h5 class="text-dark mb-3">
                chỉ với <br>
                <span style="color: #ff0000">94 000₫</span>
              </h5>
            </div>
          </div>
      </div>
      <!-- Ảnh 3 -->
      <div class="col-md-4 mb-3">
        <div class="card text-white organic">
            <img src="{{ asset('images/banner/organic3.jpg') }}" class="card-img" alt="Organic 1">
            <div class="card-img-overlay d-flex flex-column justify-content-center align-items-start p-4">
              <h5 class="text-dark mb-2">Trái cây chất lượng</h5>
              <h6 class="text-secondary mb-3">
                <span style="color: #ff0000">30% OFF</span> IN 9/4
              </h6>
              <a href="{{ route('products.index') }}" class="btn btn-primary banner-button">Mua Ngay</a>
            </div>
          </div>
      </div>
    </div>
  </div>
  
  <style>
    /* Loại bỏ viền và ẩn phần tràn */
    .organic {
      border: 0;
      overflow: hidden;
    }
    /* Hiệu ứng chuyển động cho ảnh */
    .organic img {
      transition: transform 0.3s ease;
      width: 100%;
      height: auto;
    }
    /* Phóng ảnh khi hover trên container */
    .organic:hover img {
      transform: scale(1.05);
    }

</style>



<div class="container px-4">
    <h3 class="my-4 text-left ms-4">Khuyến mại</h3>
    <div class="row my-4">
        <!-- Cột chứa banner dọc và các sản phẩm -->
        <div class="col-lg-3 col-md-4 mb-4">
    <!-- Banner dọc -->
    <div class="banner-dock position-relative">
        <img src="{{ asset('images/banner/bannerdoc3.jpg') }}" class="img-fluid w-100" alt="Banner dọc">
    </div>
</div>


<!-- Cột sản phẩm -->
<div class="col-lg-9 col-md-8">
    <div class="row">
        @foreach ($promotionalProducts as $product)
            @php
                             // Lấy giá cơ bản: nếu có discount_price và lớn hơn 0 thì dùng, nếu không thì dùng price
$basePrice = ($product->discount_price && $product->discount_price > 0) ? $product->discount_price : $product->price;
            @endphp
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card product-card">
                  <!-- Hiển thị phần trăm giảm giá nếu có -->
@if ($product->price > 0 && $product->discount_price > 0 && $product->discount_price < $product->price)
<div class="discount-badge position-absolute bg-danger text-white rounded-circle">
    -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
</div>
@endif

                    <!-- Phần ảnh sản phẩm -->
                    <div class="position-relative product-image-container">
                        <img 
                            src="{{ $product->image ? asset($product->image) : 'https://via.placeholder.com/250x250' }}" 
                            class="card-img-top product-image" 
                            alt="{{ $product->product_name }}">

                        <!-- Hover icons -->
                        <div class="hover-icons position-absolute w-100 d-flex justify-content-center">
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-light rounded-circle mx-1" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form class="d-inline add-to-cart-form" data-product-id="{{ $product->id }}">
                                @csrf
                                <!-- Giá trị mặc định của size là 1kg -->
                                <input type="hidden" name="size" class="selected-size-{{ $product->id }}" value="1kg">
                                <!-- Giá trị mặc định của số lượng là 1 -->
                                <input type="hidden" name="quantity" value="1">
                                <button type="button" class="btn btn-success rounded-circle mx-1 add-to-cart" title="Thêm vào giỏ hàng">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </form>
                            
                            
                        </div>
                    </div>

                    <!-- Nội dung thẻ sản phẩm -->
                    <div class="card-body text-center">
                        <a href="{{ route('products.show', $product->id) }}" class="card-title-link">
                            <h6 class="card-title text-truncate mb-2">{{ $product->product_name }}</h6>
                        </a>
                        <p class="card-text mb-2">
                            @if ($product->discount_price && $product->discount_price > 0)
                                <span class="text-muted text-decoration-line-through">
                                    {{ number_format($product->price, 0, ',', '.') }}₫
                                </span>
                            @endif
                            <span class="text-danger font-weight-bold product-price" id="promotional-product-price-{{ $product->id }}">
                                {{ number_format($basePrice, 0, ',', '.') }}₫
                            </span>
                        </p>
                        
                        <!-- Phần chọn size (chỉ hiển thị nếu sản phẩm có đơn vị là "kg") -->
                        @if (optional($product->unit)->unit_name === 'kg')
    <div class="size-selection d-flex justify-content-center gap-2 mt-2">
        <img src="{{ asset('images/icon/1kg.jpg') }}" 
             alt="1kg" 
             class="size-option img-fluid rounded selected" 
             data-product-id="{{ $product->id }}" 
             data-size="1kg" 
             data-price="{{ $basePrice }}">
        <img src="{{ asset('images/icon/500g.jpg') }}" 
             alt="500g" 
             class="size-option img-fluid rounded" 
             data-product-id="{{ $product->id }}" 
             data-size="500g" 
             data-price="{{ $basePrice * 0.5 }}">
        <img src="{{ asset('images/icon/250g.jpg') }}" 
             alt="250g" 
             class="size-option img-fluid rounded" 
             data-product-id="{{ $product->id }}" 
             data-size="250g" 
             data-price="{{ $basePrice * 0.25 }}">
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
</div>
    </div>
</div>

<div class="container my-2">
    <!-- CSS cho hiệu ứng hover -->
    <style>
      .card-hover {
        transition: transform 0.3s, box-shadow 0.3s;
      }
      .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      }
      /* Hiệu ứng đổi màu tiêu đề khi hover */
      .card-title {
         transition: color 0.3s ease;
      }
      .card-hover:hover .card-title {
         color: #28a745; /* Mã màu xanh lá */
      }
    </style>
  
    <h2 class="text-center mb-2">Kiến thức mới</h2>
    <p class="text-center text-secondary fst-italic">The freshest and most exctings news</p>
    <div class="row">
      @foreach($news as $item)
        <div class="col-md-3 mb-4">
          <a href="{{ route('news.show', $item->slug) }}" class="text-decoration-none text-dark">
            <div class="card h-100 border-0 card-hover">
              @if($item->image)
                <img src="{{ asset($item->image) }}" class="card-img-top" alt="{{ $item->title }}" style="height: 200px; object-fit: cover;">
              @else
                <!-- Nếu không có ảnh, hiển thị ảnh placeholder -->
                <img src="https://via.placeholder.com/350x200?text=No+Image" class="card-img-top" alt="No Image" style="height: 200px; object-fit: cover;">
              @endif
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">{{ $item->title }}</h5>
                <p class="card-text flex-grow-1">{{ Str::limit($item->summary, 150) }}</p>
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
</div>

  

<!-- JavaScript xử lý chọn size (cập nhật giá và input ẩn) -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sizeOptions = document.querySelectorAll('.size-option');
        sizeOptions.forEach(function(option) {
            option.addEventListener('click', function(){
                const selectedSize = this.getAttribute('data-size');
                const updatedPrice = this.getAttribute('data-price');
        
                // Tìm phần tử cha của product card chứa nút được click
                const productCard = this.closest('.product-card');
                if (productCard) {
                    // Cập nhật giá hiển thị trong product card
                    const priceDisplay = productCard.querySelector('.product-price');
                    if (priceDisplay) {
                        priceDisplay.textContent = new Intl.NumberFormat('vi-VN').format(updatedPrice) + '₫';
                    }
        
                    // Cập nhật input ẩn của form trong product card
                    const hiddenInput = productCard.querySelector('input[name="size"]');
                    if (hiddenInput) {
                        hiddenInput.value = selectedSize;
                    }
                }
        
                // Cập nhật hiệu ứng viền cho icon được chọn trong group
                const parent = this.parentElement;
                parent.querySelectorAll('.size-option').forEach(function(img) {
                    img.classList.remove('selected');
                });
                this.classList.add('selected');
            });
        });
    });
    </script>
    


<!-- CSS cho Banner quảng cáo và Banner dọc -->
<style>
/* CSS cho Banner dọc trong danh sách sản phẩm */
.banner-dock {
    position: relative;
    height: 620px; /* Chiều cao của banner dọc */
    width: 100%; /* Chiếm hết chiều rộng của cột chứa nó */
    max-width: 280px; /* Giới hạn chiều rộng của banner dọc */
    margin: 0 auto; /* Canh giữa banner */
    border-radius: 8px;
    overflow: hidden;
}

.banner-dock img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Đảm bảo ảnh chiếm toàn bộ không gian mà không bị kéo dài */
    border-radius: 8px;
}

/* Hiệu ứng hover */
.banner-dock:hover img {
    transform: scale(1.05); /* Phóng to ảnh khi hover */
    transition: transform 0.3s ease-in-out; /* Hiệu ứng mượt mà */
}

/* Lớp phủ nội dung đè lên banner */
.banner-overlay {
    top: 20%; /* Đặt nội dung ở giữa chiều dọc */
    left: 40%; /* Đặt nội dung ở giữa chiều ngang */
    transform: translate(-50%, -50%); /* Căn giữa nội dung hoàn toàn */
    color: white;
    text-align: center;
    padding: 10px;
    
}

/* Hiệu ứng hover cho nút "Mua Ngay" */
.banner-overlay .btn {
    background-color: #fff; /* Nền trắng */
    color: #000; /* Chữ đen */
    border: 0px solid #000; /* Đường viền mỏng màu đen */
    font-size: 14px; /* Giảm kích thước chữ */
    padding: 8px 16px; /* Giảm kích thước padding để nút nhỏ hơn */
    border-radius: 4px; /* Bo góc nhẹ */
    transition: background-color 0.3s ease, color 0.3s ease; /* Hiệu ứng mượt khi hover */
}

/* Hiệu ứng khi hover */
.banner-overlay .btn:hover {
    background-color: #000; /* Đổi nền thành đen khi hover */
    color: #fff; /* Đổi chữ thành trắng khi hover */
}
    /* Banner quảng cáo */
.banner-container {
    position: relative;
    overflow: hidden;
    border-radius: 15px;
    transition: transform 0.5s ease-in-out; /* Thêm hiệu ứng khi thay đổi vị trí của banner */
}

.banner-image {
    height: 400px;
    object-fit: cover;
    opacity: 1; /* Khởi tạo trạng thái ảnh hiển thị */
    transition: opacity 0.5s ease-in-out; /* Thêm hiệu ứng mờ dần khi chuyển ảnh */
}

/* Banner Content Styles */
.banner-content {
    top: 40%;
    left: 63%;
    opacity: 0;
    transform: translate(-50%, 20%); /* Trượt xuống ban đầu */
    transition: opacity 0.6s ease, transform 0.6s ease; /* Hiệu ứng mượt */
}

.banner-title {
    font-size: 2.5rem;
    font-weight: italic;
    color: #333;
    margin-bottom: 10px;
    line-height: 1.4;
}

.banner-title .highlight {
    color: #6fbf4b; /* Màu xanh lá cây */
}

.banner-text {
    font-size: 1.2rem;
    color: #555555;
    margin-bottom: 20px;
}

.banner-button {
    font-size: 1rem;
    padding: 10px 20px;
    color: #fff;
    background-color: #6fbf4b;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.banner-button:hover {
    background-color: #5aa43c; /* Màu tối hơn khi hover */
    transform: translateY(-3px); /* Di chuyển nhẹ lên trên */
}

/* Hiệu ứng trượt mượt mà */
.banner-content.show {
    opacity: 1; /* Hiển thị nội dung */
    transform: translate(-50%, -50%); /* Trượt lên nhẹ */
}

.banner-arrow {
    top: 45%;   
    background-color: rgba(0, 0, 0, 0.5);
    color: #fff;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s ease, transform 0.3s ease; /* Thêm hiệu ứng khi hover */
}

.banner-arrow:hover {
    background-color: rgba(0, 0, 0, 0.7);
    transform: scale(1.1); /* Tăng kích thước khi hover */
}

.banner-arrow-left {
    left: 10px;
}

.banner-arrow-right {
    right: 10px;
}

     /* Card style */
     .product-card {
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        height: 300px;
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
        height: 240px; /* Giữ chiều cao của ảnh như trong mẫu ban đầu */
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
        width: 40px;
        height: 40px;
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
        height: 160px; /* Giữ chiều cao ban đầu để chứa thông tin và lựa chọn size */
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
        font-size: 1rem;
        font-weight: bold;
    }
    .card-text {
        font-size: 0.9rem;
    }

    /* Style cho phần lựa chọn size */
    .size-selection .size-option {
        cursor: pointer;
        max-width: 25px; /* Icon nhỏ lại một chút */
        transition: transform 0.3s ease, border 0.3s ease;
        border: 1px solid #ccc; /* Viền xám mờ mặc định */
        border-radius: 50%;
        padding: 2px;
    }
    .size-selection .size-option.selected {
        border: 1px solid #28a745; /* Viền khi được chọn */
        transform: scale(1.1);
    }

</style>

<!-- JavaScript cho banner quảng cáo -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const bannerContent = document.querySelector('.banner-content');
    const banners = [
        "{{ asset('images/banner/banner1.jpg') }}",
        "{{ asset('images/banner/banner2.jpg') }}",
    ];
    
    let currentIndex = 0;
    const bannerImage = document.querySelector('.banner-image');
    const leftArrow = document.querySelector('.banner-arrow-left');
    const rightArrow = document.querySelector('.banner-arrow-right');

    // Hiển thị nội dung banner với hiệu ứng trượt khi tải trang
    setTimeout(() => {
        bannerContent.classList.add('show');
    }, 500);

    // Hàm cập nhật ảnh banner và reset nội dung
    function updateBanner(index) {
        bannerImage.style.opacity = 0;
        bannerContent.classList.remove('show');

        setTimeout(() => {
            bannerImage.src = banners[index];
            bannerImage.style.opacity = 1;
            setTimeout(() => {
                bannerContent.classList.add('show');
            }, 300);
        }, 500);
    }

    leftArrow.addEventListener('click', () => {
        currentIndex = (currentIndex === 0) ? banners.length - 1 : currentIndex - 1;
        updateBanner(currentIndex);
    });

    rightArrow.addEventListener('click', () => {
        currentIndex = (currentIndex === banners.length - 1) ? 0 : currentIndex + 1;
        updateBanner(currentIndex);
    });

    // Tự động chuyển banner sau mỗi 15 giây
    setInterval(() => {
        currentIndex = (currentIndex === banners.length - 1) ? 0 : currentIndex + 1;
        updateBanner(currentIndex);
    }, 15000);
});
</script>

@endsection

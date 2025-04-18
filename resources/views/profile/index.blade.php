@extends('layouts.app')

@section('content')

   <!-- Banner & Breadcrumb -->
   <div class="banner-container position-relative mb-4">
    <img src="{{ asset('images/banner/organic-breadcrumb-1.jpg') }}" alt="Banner quảng cáo" class="banner-image w-100" style="height: 130px; object-fit: cover;">
    <div class="banner-overlay position-absolute top-50 start-50 translate-middle text-center">
        <h2 class="text-dark">Profile</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-dark">Trang chủ</a></li>
                
            </ol>
        </nav>
    </div>
</div>

<div class="container mt-4">
    <!-- Thông tin cơ bản -->
    <div class="card profile-card shadow-sm mb-4 profile-bg">
        <div class="card-body">
            <div class="row align-items-center">
                <!-- Ảnh đại diện -->
                <div class="col-md-3 text-center">
                    <img src="{{ $user->avatar ? asset($user->avatar) : asset('images/avatars/avtdf.jpg') }}" 
                         alt="Avatar" 
                         class="img-fluid rounded-circle" 
                         style="width:150px; height:150px;">
                </div>
                <!-- Thông tin cá nhân -->
                <div class="col-md-9">
                    <h3>{{ $user->name }}</h3>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    
                    <p><strong>Số điện thoại:</strong> {!! $user->phone ? $user->phone : '&lt;trống&gt;' !!}</p>

                    <p>
                        <strong>Giới tính:</strong>
                        @if($user->gender)
                            @switch($user->gender)
                                @case('male')
                                    Nam
                                    @break
                                @case('female')
                                    Nữ
                                    @break
                                @case('other')
                                    Khác
                                    @break
                                @default
                                    {{ ucfirst($user->gender) }}
                            @endswitch
                        @else
                            <span>&lt;trống&gt;</span>
                        @endif
                    </p>
                    
                    <p><strong>Ngày sinh:</strong> {!! $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d/m/Y') : '&lt;trống&gt;' !!}</p>

                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-success mt-3">
                        Chỉnh sửa thông tin
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Lịch sử hoạt động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            Lịch sử hoạt động
        </div>
        <div class="card-body">
            @if(!$productComments->isEmpty())
            <div class="list-group mb-3">
              @foreach($productComments as $comment)
                <a href="{{ route('products.show', $comment->product->id) }}" class="list-group-item list-group-item-action d-flex align-items-center">
                  <img src="{{ asset($comment->product->image ?? 'images/no-image.png') }}" alt="{{ $comment->product->product_name }}" class="img-thumbnail me-3" style="width: 80px; height: 80px;">
                  <div class="flex-grow-1">
                    <h5 class="mb-1">{{ $comment->product->product_name ?? 'Không xác định' }}</h5>
                    @if(!$comment->parent_id)
                      <!-- Nếu không phải tin nhắn trả lời: hiển thị đánh giá sao và bình luận -->
                      <p class="mb-1">
                        <strong>Đánh giá:</strong>
                        @for($i = 1; $i <= 5; $i++)
                          @if($i <= $comment->rating)
                            <i class="fas fa-star text-warning"></i>
                          @else
                            <i class="far fa-star text-warning"></i>
                          @endif
                        @endfor
                      </p>
                      <p class="mb-1"><strong>Bình luận:</strong> {{ $comment->comment }}</p>
                    @else
                      <!-- Nếu là tin nhắn trả lời: chỉ hiển thị nội dung trả lời -->
                      <p class="mb-1"><strong>Trả lời:</strong> {{ $comment->comment }}</p>
                    @endif
                  </div>
                </a>
              @endforeach
            </div>
          @endif
          
          
        

            @if($orders->isEmpty())
                <p>Chưa có đơn hàng nào.</p>
            @else
                <div id="ordersCarousel" class="carousel slide position-relative mt-5" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($orders->chunk(5) as $chunkIndex => $orderChunk)
                            <div class="carousel-item @if($chunkIndex == 0) active @endif">
                                <div class="d-flex justify-content-center">
                                    @foreach($orderChunk as $order)
                                        <div class="order-card-wrapper me-2">
                                            <div class="card order-card">
                                                <!-- Header: Hiển thị ID đơn hàng -->
                                                <div class="card-header text-center">
                                                    Đơn hàng #{{ $order->id }}
                                                </div>
                                                <div class="card-body">
                                                    <!-- Thông tin chung của đơn hàng -->
                                                    <div class="order-info mb-2">
                                                        <p class="mb-1">
                                                            <strong>Trạng thái:</strong>
                                                            @switch($order->trang_thai)
                                                                @case('pending')
                                                                    <span class="badge bg-warning">Chờ xử lý</span>
                                                                    @break
                                                                @case('confirmed')
                                                                    <span class="badge bg-info">Đã xác nhận</span>
                                                                    @break
                                                                @case('shipping')
                                                                    <span class="badge bg-secondary">Đang vận chuyển</span>
                                                                    @break
                                                                @case('cancelled')
                                                                    <span class="badge bg-danger">Đã hủy</span>
                                                                    @break
                                                                @case('completed')
                                                                    <span class="badge bg-success">Hoàn thành</span>
                                                                    @break
                                                                @default
                                                                    <span class="badge bg-secondary">{{ $order->trang_thai }}</span>
                                                            @endswitch
                                                        </p>
                                                        <p class="mb-1"><strong>Ngày đặt:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</p>
                                                    </div>
                                                    <hr>
                                                    <!-- Danh sách sản phẩm -->
                                                    <div class="order-items" style="height: 100px; overflow-y: auto;">
                                                        @foreach($order->orderItems as $orderItem)
                                                            <div class="order-item d-flex align-items-center mb-2">
                                                                <img src="{{ asset($orderItem->product->image ?? 'images/products/default.jpg') }}" 
                                                                     alt="{{ $orderItem->product->product_name }}" 
                                                                     class="img-thumbnail" 
                                                                     style="width:80px; height:80px; object-fit:cover;">
                                                                <div class="ms-2">
                                                                    <p class="mb-1"><strong>{{ $orderItem->product->product_name }}</strong></p>
                                                                    <p class="mb-0">Số lượng: {{ $orderItem->so_luong }}</p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    
                                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-success btn-sm w-100 mt-2">
                                                        Xem chi tiết
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Custom arrow buttons -->
                    <button class="banner-arrow banner-arrow-left position-absolute" type="button" data-bs-target="#ordersCarousel" data-bs-slide="prev">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="banner-arrow banner-arrow-right position-absolute" type="button" data-bs-target="#ordersCarousel" data-bs-slide="next">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>

<!-- Thêm phần hiển thị Voucher đang sở hữu -->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        Voucher đang sở hữu
    </div>
    <div class="card-body">
        @if($user->vouchers->isEmpty())
            <p>Chưa có voucher nào.</p>
        @else
            <div class="d-flex flex-row flex-wrap">
                @foreach($user->vouchers->filter(fn($v) => $v->used < $v->max_usage) as $voucher)
                <div class="mr-3 mb-3">
                    <div class="card voucher-card ms-3">
                        <div class="card-body d-flex align-items-center">
                            <img src="{{ asset('images/icon/voucher.png') }}" alt="Voucher Icon" class="voucher-icon">
                            <div class="voucher-info">
                                <div class="voucher-title">{{ $voucher->code }}</div>
                                <div class="voucher-discount">
                                    @if($voucher->type == 'fixed')
                                        {{ number_format($voucher->discount, 0, ',', '.') }}đ
                                    @elseif($voucher->type == 'percentage')
                                        {{ $voucher->discount }}%
                                    @else
                                        {{ $voucher->discount }}
                                    @endif
                                </div>
                                <div class="voucher-usage">
                                    Lượt sử dụng: {{ $voucher->max_usage }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach            
            </div>
        @endif
    </div>
</div>

</div>

<style>
    .profile-bg {

    background: url('/images/banner/bg2.jpg') no-repeat center center;
    background-size: cover;
    
}

</style>
<style>
    .voucher-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    background-color: #fff;
    overflow: hidden;
    width: 210px;

}
.voucher-card:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
.voucher-icon {
    width: 50px;
    height: 50px;
    object-fit: contain;
}
.voucher-info {
    margin-left: 15px;
}
.voucher-title {
    font-size: 1.2rem;
    margin-bottom: 5px;
    font-weight: bold;
}
.voucher-discount {
    font-size: 1.1rem;
    color: #e74c3c;
    margin-bottom: 5px;
}
.voucher-usage {
    font-size: 0.9rem;
    color: #555;
}

    /* Wrapper cho mỗi card đơn hàng */
    .order-card-wrapper {
        width: 220px;
    }
    
    /* Tùy chỉnh card đơn hàng */
    .order-card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Custom arrow buttons */
    .banner-arrow {
        width: 40px;
        height: 40px;
        border: none;
        background-color: rgba(255, 255, 255, 0.8);
        color: #000;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        z-index: 1000;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .banner-arrow-left {
        left: 10px;
    }
    
    .banner-arrow-right {
        right: 10px;
    }
    
    .banner-arrow i {
        font-size: 18px;
    }
    </style>
    
    
@endsection

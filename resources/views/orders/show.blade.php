@extends('layouts.app')

@section('content')

  <!-- Banner -->
  <div class="banner-container position-relative mb-4">
    <img src="{{ asset('images/banner/organic-breadcrumb-1.jpg') }}" alt="Banner quảng cáo" class="banner-image w-100" style="height: 130px; object-fit: cover;">
    <div class="banner-overlay position-absolute top-50 start-50 translate-middle text-center">
        <h2 class="text-dark">Chi tiết đơn hàng #{{ $order->id }}</h2>
        <nav aria-label="breadcrumb" class="d-flex justify-content-center">
            <ol class="breadcrumb bg-transparent mb-0">
              <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-dark">Trang chủ</a>
              </li>
              <li class="breadcrumb-item">
                <a href="{{ route('orders.index') }}" class="text-dark">Đơn hàng</a>
              </li>
            </ol>
          </nav>
    </div>
</div>

<div class="container my-4">
    @php
    $statusMapping = [
        'pending'           => 'Chờ xác nhận',
        'confirmed'         => 'Đã xác nhận',
        'shipping'          => 'Đang giao hàng',
        'completed'         => 'Hoàn thành',
        'canceled'          => 'Đã hủy',
    ];

$paymentMapping = [
    'pending' => 'Chưa thanh toán',
    'paid'    => 'Đã thanh toán',
];
@endphp
    <!-- Ticket cho chi tiết đơn hàng -->
    <div class="ticket">
        <div class="order-info mb-4">
            <h3>Chi tiết đơn hàng #{{ $order->id }}</h3>
            <div class="row">
                <div class="col-lg-6">
                    <p><i class="fas fa-user" style="color: #ff5733;"></i> <strong>Khách hàng:</strong> {{ $order->ten_khach_hang }}</p>
                    <p><i class="fas fa-phone" style="color: #33aaff;"></i> <strong>Số điện thoại:</strong> {{ $order->so_dien_thoai }}</p>
                    <p><i class="fas fa-map-marker-alt" style="color: #f39c12;"></i> <strong>Địa chỉ:</strong> {{ $order->dia_chi }}</p>
                </div>
                <div class="col-lg-6">
                    <p>
                        <i class="fas fa-money-bill-wave" style="color: #27ae60;"></i> 
                        <strong>Tổng tiền:</strong> {{ number_format($order->tong_tien, 0) }} VND
                    </p>
                    
                    @if($order->voucher)
                        <p>
                            <i class="fas fa-tags" style="color: #e74c3c;"></i>
                            <strong>Giảm giá:</strong> 
                            @if($order->voucher->type === 'percentage')
                                -{{ $order->voucher->discount }}%
                            @else
                                -{{ number_format($order->voucher->discount, 0) }}₫
                            @endif
                        </p>
                    @endif
                    
                    <p><i class="fas fa-credit-card" style="color: #8e44ad;"></i> <strong>Phương thức thanh toán:</strong> {{ $order->phuong_thuc_thanh_toan }}</p>
                    <p><i class="fas fa-info-circle" style="color: #2980b9;"></i> <strong>Trạng thái:</strong> {{ $statusMapping[$order->trang_thai] ?? $order->trang_thai }} ({{ $paymentMapping[$order->payment_status] ?? $order->payment_status }})</p>
                @if ($order->payment_status == 'pending' && $order->phuong_thuc_thanh_toan == 'VNPay')
                    <form action="{{ route('retryvnpay.vn') }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <button type="submit" class="btn btn-primary">Thanh toán</button>
                    </form>
                @elseif ($order->payment_status == 'pending' && $order->phuong_thuc_thanh_toan == 'Momo')
                    <form action="{{ route('retrymomo.vn') }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <button type="submit" class="btn btn-primary">Thanh toán</button>
                    </form>
                @elseif ($order->payment_status == 'pending' && $order->phuong_thuc_thanh_toan == 'Paypal')
                    <form action="{{ route('paypal.retryOrder') }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <button type="submit" class="btn btn-primary">Thanh toán</button>
                    </form>
                @endif
                </div> 
            </div>
        </div>
        

        <!-- Danh sách sản phẩm trong đơn hàng -->
        <h4 class="mb-3">Sản phẩm trong đơn hàng:</h4>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Mã SP</th>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>                 
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orderItems as $item)
                    @php
                        // Lấy đơn giá: ưu tiên giá giảm nếu có, và lớn hơn 0
                        $basePrice = ($item->product->discount_price && $item->product->discount_price > 0) 
                                        ? $item->product->discount_price 
                                        : $item->product->price;
                
                        // Xử lý size: chỉ có 3 loại nên gán hệ số trực tiếp
                        $weightFactor = 1; // mặc định là 1kg
                        if ($item->size === '250g') {
                            $weightFactor = 0.25;
                        } elseif ($item->size === '500g') {
                            $weightFactor = 0.5;
                        }
                
                        $giaTheoSize = $basePrice * $weightFactor;
                    @endphp
                
                    <tr>
                        <td>{{ $item->product->id }}</td>
                
                        <td class="text-start">
                            <div class="row">
                                <div class="col-lg-4">
                                    <a class="text-decoration-none" href="{{ route('products.show', $item->product->id) }}">
                                        <img src="{{ asset($item->product->image ?? 'https://via.placeholder.com/100') }}" 
                                             alt="Ảnh sản phẩm" 
                                             width="100" 
                                             height="100" 
                                             style="object-fit: cover; border-radius: 5px;">
                                    </a>
                                </div>
                                <div class="col-lg-8 d-flex justify-content-center align-items-center text-wrap">
                                    <a href="{{ route('products.show', $item->product->id) }}" 
                                       class="text-decoration-none text-dark fw-bold text-center ms-2">
                                        {{ $item->product->product_name }}
                                        @if(!empty($item->size))
                                            ({{ $item->size }})
                                        @endif
                                    </a> 
                                </div>
                            </div>
                        </td>
                
                        <td class="price-highlight">{{ number_format($giaTheoSize, 0, ',', '.') }} VND</td>
                        <td>{{ $item->so_luong }}</td>
                        <td class="price-highlight">{{ number_format($giaTheoSize * $item->so_luong, 0, ',', '.') }} VND</td>
                    </tr>
                @endforeach
                
                    
                </tbody>
            </table>
            @if ($order->trang_thai == 'pending')
    <div class="d-flex justify-content-end">
        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
            @csrf
            <button type="submit" class="btn btn-danger">Hủy đơn hàng</button>
        </form>
    </div>
@endif
        </div>
    </div>
</div>

<!-- CSS cho giao diện Ticket -->
<style>
.ticket {
    background: #fff;
    border: 2px dashed #ccc;
    border-radius: 15px;
    max-width: 800px;
    margin: 30px auto;
    padding: 20px 30px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    position: relative;
}

.ticket::before,
.ticket::after {
    content: "";
    position: absolute;
    width: 30px;
    height: 30px;
    background: #f8f9fa;
    border: 2px dashed #ccc;
    border-radius: 50%;
}

.ticket::before {
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
}

.ticket::after {
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
}

.ticket h3 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 24px;
}

.order-info p {
    font-size: 16px;
    margin-bottom: 10px;
}

.order-info i {
    margin-right: 8px;
}

/* Lớp nổi bật giá */
.price-highlight {
    color: #e74c3c !important;
    font-weight: bold;
    font-size: 16px;
}

/* Table trong ticket */
.ticket table {
    width: 100%;
    margin-top: 20px;
}

.ticket table th,
.ticket table td {
    text-align: center;
    vertical-align: middle;
    padding: 10px;
}
</style>
@endsection

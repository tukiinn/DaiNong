@extends('layouts.app')

@section('content')

<div class="container">
    <div class="order-steps row">
        <!-- Bước 1: Giỏ hàng -->
        <a href="{{ route('cart.index') }}" class="col-md-4 step active">
            <div class="step-number">01</div>
            <div class="step-content">
                <h4>Giỏ hàng</h4>
                <p>Quản lý danh sách sản phẩm</p>
            </div>
        </a>
    
        <!-- Bước 2: Chi tiết thanh toán -->
        <a href="{{ route('cart.checkout') }}" class="col-md-4 step active">
            <div class="step-number">02</div>
            <div class="step-content">
                <h4>Chi tiết thanh toán</h4>
                <p>Thanh toán danh sách sản phẩm</p>
            </div>
        </a>
    
        <!-- Bước 3: Hoàn thành đơn hàng -->
        <a  class="col-md-4 step active">
            <div class="step-number">03</div>
            <div class="step-content">
                <h4>Hoàn thành đơn hàng</h4>
                <p>Xem lại đơn hàng</p>
            </div>
        </a>
    </div>
    </div>

    <div class="container my-5">
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
      <div class="ticket">
        <div class="ticket-header">
          <h2>Cảm ơn bạn. Đơn hàng của bạn đã được nhận.</h2>
        </div>
        <div class="ticket-body">
          <div class="info">
              <strong><i class="bi bi-receipt"></i> Mã đơn hàng:</strong>
              <span>{{ $order->id }}</span>
          </div>
          <div class="info">
              <strong><i class="bi bi-calendar-event"></i> Ngày:</strong>
              <span>{{ \Carbon\Carbon::parse($order->created_at)->locale('vi')->translatedFormat('d F, Y') }}</span>
          </div>
          <div class="info">
              <strong><i class="bi bi-cash-stack"></i> Tổng:</strong>
              <span>{{ number_format($order->tong_tien, 0, ',', '.') }}₫</span>
          </div>
          <div class="info">
              <strong><i class="bi bi-credit-card"></i> Phương thức thanh toán:</strong>
              <span>{{ $order->phuong_thuc_thanh_toan }}</span>
          </div>
          <div class="info">
              <strong><i class="bi bi-person"></i> Tên khách hàng:</strong>
              <span>{{ $order->ten_khach_hang }}</span>
          </div>
          <div class="info">
              <strong><i class="bi bi-telephone"></i> Số điện thoại:</strong>
              <span>{{ $order->so_dien_thoai }}</span>
          </div>
          <div class="info">
              <strong><i class="bi bi-geo-alt"></i> Địa chỉ giao hàng:</strong>
              <span>{{ $order->dia_chi }}</span>
          </div>
          <div class="info">
              <strong><i class="bi bi-truck"></i> Trạng thái đơn:</strong>
              <span>{{ $statusMapping[$order->trang_thai] ?? $order->trang_thai }} ({{ $paymentMapping[$order->payment_status] ?? $order->payment_status }})</span>
          </div>
      </div>
      
      </div>
      

      <div class="container my-3">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-8">
                <div class="card-header text-center">
                    <h4 class="mb-0">Chi tiết đơn hàng</h4>
                  </div>    
              <div class="card shadow my-3">
               
                <div class="card-body">
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th class="text-end">Giá</th>
                      </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalSubtotal = 0;
                        @endphp
                    
                        @foreach ($order->orderItems as $item)
                            @php
                                // Lấy giá gốc hoặc giá giảm nếu có
                                $basePrice = ($item->product->discount_price && $item->product->discount_price > 0)
                                                ? $item->product->discount_price
                                                : $item->product->price;
                    
                                // Xử lý size
                                $weightFactor = 1; // mặc định 1kg
                                if ($item->size === '250g') {
                                    $weightFactor = 0.25;
                                } elseif ($item->size === '500g') {
                                    $weightFactor = 0.5;
                                }
                    
                                // Giá theo size
                                $unitPrice = $basePrice * $weightFactor;
                                $lineTotal = $unitPrice * $item->so_luong;
                    
                                $totalSubtotal += $lineTotal;
                            @endphp
                    
                            <tr>
                                <td>{{ $item->product->product_name }} 
                                    @if ($item->size) ({{ $item->size }}) @endif
                                </td>
                                <td>{{ $item->so_luong }}</td>
                                <td class="text-end">{{ number_format($unitPrice, 0, ',', '.') }}₫</td>
                            </tr>
                        @endforeach
                    
                        @php
                            $discountAmount = $totalSubtotal - $order->tong_tien;
                        @endphp
                    
                        <tr>
                            <td colspan="2"><strong><i class="bi bi-cart"></i> Tổng số phụ:</strong></td>
                            <td class="text-end">{{ number_format($totalSubtotal, 0, ',', '.') }}₫</td>
                        </tr>
                    
                        @if($discountAmount > 0)
                            <tr>
                                <td colspan="2"><strong><i class="bi bi-tag"></i> Giảm giá:</strong></td>
                                <td class="text-end text-success">- {{ number_format($discountAmount, 0, ',', '.') }}₫</td>
                            </tr>
                        @endif
                    
                        <tr>
                            <td colspan="2"><strong><i class="bi bi-truck"></i> Giao nhận hàng:</strong></td>
                            <td class="text-end">Giao hàng miễn phí</td>
                        </tr>
                    
                        <tr class="table-secondary">
                            <td colspan="2"><strong><i class="bi bi-cash"></i> Tổng cộng:</strong></td>
                            <td class="text-danger text-end fw-bold">
                                {{ number_format($order->tong_tien, 0, ',', '.') }}₫
                            </td>
                        </tr>
                    </tbody>
                    
                  </table>
                </div>
              </div>
            </div>
          </div>
    </div>
<style>
           
    .ticket {
max-width: 800px;
margin: 0 auto;
background-color: #fff;
border: 2px dashed #03c720; /* Viền vé màu xanh với đường nét đứt */
border-radius: 8px;
overflow: hidden;
box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.ticket-header {
background-color: #e0f8e9;
padding: 20px;
text-align: center;
}
.ticket-header h2 {
color: #28a745;
margin: 0;
font-size: 28px;
}
.ticket-body {
padding: 20px;
display: flex;
flex-wrap: wrap;
justify-content: space-between;
}
.ticket-body .info {
flex: 0 0 41%;
margin-bottom: 20px;
}
.ticket-body .info strong {
display: block;
color: #555;
margin-bottom: 5px;
}
.ticket-body .info span {
font-size: 16px;
color: #333;
}
@media (max-width: 480px) {
.ticket-body .info {
 flex: 0 0 100%;
}
}
   </style>
@endsection

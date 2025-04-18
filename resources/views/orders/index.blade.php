@extends('layouts.app')

@section('content')

<!-- Banner & Breadcrumb -->
<div class="banner-container position-relative mb-4">
    <img src="{{ asset('images/banner/organic-breadcrumb-1.jpg') }}" alt="Banner quảng cáo" class="banner-image w-100" style="height: 130px; object-fit: cover;">
    <div class="banner-overlay position-absolute top-50 start-50 translate-middle text-center">
        <h2 class="text-dark">Danh sách đơn hàng</h2>
        <nav aria-label="breadcrumb" class="d-flex justify-content-center">
            <ol class="breadcrumb bg-transparent mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-dark">Trang chủ</a>
                </li>
                <!-- Có thể thêm các breadcrumb-item khác nếu cần -->
            </ol>
        </nav>
    </div>
</div>

<<div class="container my-4">
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
    @if($orders->count() > 0)
        <div class="row">
            @foreach($orders as $order)
                <div class="col-md-6 col-lg-4">
                    <div class="card order-card mb-4 shadow">
                        <div class="card-header bg-secondary text-white text-center">
                            Đơn hàng #{{ $order->id }}
                        </div>
                        <div class="card-body">
                            <p><i class="fas fa-user"></i> <strong>Khách hàng:</strong> {{ $order->ten_khach_hang }}</p>
                            <p><i class="fas fa-phone"></i> <strong>Số điện thoại:</strong> {{ $order->so_dien_thoai }}</p>
                            <p><i class="fas fa-map-marker-alt"></i> <strong>Địa chỉ:</strong> {{ $order->dia_chi }}</p>
                            <p>
                                <i class="fas fa-money-bill-wave"></i> <strong>Tổng tiền:</strong>
                                <span class="price-highlight">{{ number_format($order->tong_tien, 0) }} VND</span>
                            </p>
                            <p>
                                @php
                                $statusMapping = [
                                    'pending'   => 'Chờ xác nhận',
                                    'confirmed' => 'Đã xác nhận',
                                    'shipping'  => 'Đang giao hàng',
                                    'completed' => 'Hoàn thành',
                                    'cancelled'  => 'Đã hủy',
                                ];

                                // Mapping cho CSS class của trạng thái đơn hàng
                                $statusBadgeClassMapping = [
                                    'pending'   => 'badge bg-secondary',
                                    'confirmed' => 'badge bg-primary',
                                    'shipping'  => 'badge bg-info',
                                    'completed' => 'badge bg-success',
                                    'cancelled'  => 'badge bg-danger',
                                ];

                                $paymentMapping = [
                                    'pending' => 'Chưa thanh toán',
                                    'paid'    => 'Đã thanh toán',
                                ];

                                // Mapping cho CSS class của trạng thái thanh toán
                                $paymentBadgeClassMapping = [
                                    'pending' => 'badge bg-secondary',
                                    'paid'    => 'badge bg-success',
                                ];
                            @endphp
                                <p>
                                <i class="fas fa-info-circle"></i> <strong>Trạng thái:</strong>
                                <span class="{{ $statusBadgeClassMapping[$order->trang_thai] ?? 'badge bg-secondary' }}">
                                    {{ $statusMapping[$order->trang_thai] ?? $order->trang_thai }}
                                </span>
                                
                                <span class="{{ $paymentBadgeClassMapping[$order->payment_status] ?? 'badge bg-secondary' }}">
                                    {{ $paymentMapping[$order->payment_status] ?? $order->payment_status }}
                                </span>
                                
                            </p>

                            <p><i class="fas fa-calendar-alt"></i> <strong>Ngày tạo:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-success btn-block mt-3">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <!-- Phân trang -->
        @if ($orders->hasPages())
            <nav>
                <ul class="pagination justify-content-center">
                    {{-- Nút "Trang trước" --}}
                    @if ($orders->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">«</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">«</a>
                        </li>
                    @endif

                    {{-- Hiển thị danh sách số trang --}}
                    @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                        @if ($page == $orders->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    {{-- Nút "Trang tiếp" --}}
                    @if ($orders->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">»</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">»</span>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
    @else
        <p class="alert alert-warning mt-3">Không có đơn hàng nào</p>
    @endif
</div>
<!-- CSS cho giao diện với chủ đề nông sản (xanh lá) -->
<style>

/* Order Card */
.order-card {
    border: none;
    border-radius: 10px;
    overflow: hidden;
    background-color: #fff;
    transition: transform 0.3s;
}

.order-card:hover {
    transform: translateY(-5px);
}

/* Card header */
.order-card .card-header {
    font-size: 18px;
    font-weight: bold;
    text-align: center;
}

/* Card body */
.order-card .card-body p {
    font-size: 15px;
    margin-bottom: 0.75rem;
}

.order-card .card-body i {
    margin-right: 6px;
    color: #2d6a2d; /* sắc xanh lá tối */
}

/* Nổi bật giá với màu xanh lá đậm */
.price-highlight {
    color: red !important;
    font-weight: bold;
    font-size: 16px;
}

/* Nút "Xem chi tiết" */
.btn-block {
    display: block;
    width: 100%;
}


/* Responsive adjustments */
@media (max-width: 576px) {
    .order-card .card-body p {
        font-size: 14px;
    }
}
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
</style>
@endsection

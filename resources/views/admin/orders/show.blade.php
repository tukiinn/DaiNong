@extends('layouts.admin')

@section('content')
<div class="container p-4 print-container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="no-print">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.orders.index') }}">Đơn hàng</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Chi tiết đơn hàng #{{ $order->id }}</li>
        </ol>
    </nav>

    <!-- Tiêu đề trang & Nút in -->
    <div class="mb-4 text-center">
        <h2>Chi tiết đơn hàng #{{ $order->id }}</h2>
        <div class="no-print mt-3">
            <button onclick="window.print()" class="btn btn-print">
                <i class="fas fa-print"></i> In đơn hàng
            </button>
        </div>
    </div>

    <!-- Thông tin đơn hàng -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <!-- Thông tin khách hàng -->
                <div class="col-md-6">
                    <h5 class="mb-3">Thông tin khách hàng</h5>
                    <p><strong>Khách hàng:</strong> {{ $order->ten_khach_hang }}</p>
                    <p>
                        <strong>Số điện thoại:</strong>
                        <span class="phone-original">{{ $order->so_dien_thoai }}</span>
                        <span class="phone-masked">{{ substr($order->so_dien_thoai, 0, -3) . '***' }}</span>
                    </p>
                    <p><strong>Địa chỉ:</strong> {{ $order->dia_chi }}</p>
                </div>
                <!-- Thông tin đơn hàng -->
                <div class="col-md-6">
                    <h5 class="mb-3">Thông tin đơn hàng</h5>
                    <p><strong>Tổng tiền:</strong> {{ number_format($order->tong_tien, 0) }} VND</p>
                    <p><strong>Phương thức thanh toán:</strong> {{ $order->phuong_thuc_thanh_toan }}</p>
                    <p><strong>Trạng thái:</strong> {{ $order->trang_thai }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách sản phẩm trong đơn hàng -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-3">Sản phẩm trong đơn hàng:</h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Mã SP</th>
                            <th style="width: 20%;">Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>                 
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderItems as $item)
                            <tr>
                                <td>{{ $item->product->id }}</td>
                                <td class="text-start" style="max-width:150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <a class="text-decoration-none no-print" href="{{ route('products.show', $item->product->id) }}">
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
                                <td class="price-highlight">{{ number_format($item->gia, 0) }} VND</td>
                                <td>{{ $item->so_luong }}</td>
                                <td class="price-highlight">{{ number_format($item->thanh_tien, 0) }} VND</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Nút xác nhận đơn hàng (hiển thị nếu đơn hàng đang xử lý) -->
    @if ($order->trang_thai === 'Đang xử lý')
        <div class="text-center no-print">
            <form action="{{ route('order.payCOD', $order->id) }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-success btn-lg action-confirm">
                    <i class="fas fa-check-circle"></i> Xác nhận đơn hàng
                </button>
            </form>
        </div>
    @endif
</div>

<!-- CSS Styles -->
<style>
    /* Container */
    .container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    /* Breadcrumb */
    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
    .breadcrumb-item a {
        text-decoration: none;
        color: #81c784;
    }
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #6c757d;
    }

    /* Tiêu đề */
    h2, h4 {
        color: #343a40;
    }

    /* Thông tin đơn hàng */
    .order-details p {
        font-size: 1rem;
        margin-bottom: 10px;
    }
    .order-details strong {
        color: #343a40;
    }

    /* Ẩn số điện thoại gốc trên màn hình in */
    .phone-masked {
        display: none;
    }

    /* In ấn: hiển thị đầy đủ trong 1 trang (nếu có thể) */
    @media print {
        .no-print {
            display: none !important;
        }
        .phone-original {
            display: none;
        }
        .phone-masked {
            display: inline;
        }
        body * {
            visibility: hidden;
        }
        .print-container, .print-container * {
            visibility: visible;
        }
        .print-container {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%) scale(1); /* Thu nhỏ và căn giữa theo chiều ngang */
    width: 100%;
}

    }

    /* Bảng */
    .table {
        margin-top: 20px;
        border-radius: 8px;
        overflow: hidden;
    }
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
        padding: 15px;
    }
    .table-dark {
        background-color: #343a40;
        color: #fff;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
        cursor: pointer;
    }

    /* Nút hành động */
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        font-weight: bold;
        padding: 15px 30px;
        border-radius: 8px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
        transform: scale(1.05);
    }
    .btn-success i {
        margin-right: 10px;
    }

    /* Khoảng cách */
    .mt-4 {
        margin-top: 1.5rem !important;
    }
    .mb-4 {
        margin-bottom: 1.5rem !important;
    }

    /* Nút in đơn hàng */
    .btn-print {
        background-color: #81c784;
        border: none;
        color: #fff;
        padding: 10px 20px;
        font-size: 1rem;
        border-radius: 50px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .btn-print:hover {
        background-color: #689f65;
        transform: translateY(-2px);
    }

    /* Nút xác nhận (cho hành động khác trên trang) */
    .action-confirm {
        background-color: #81c784;
        border-color: #81c784;
        font-weight: bold;
        padding: 15px 30px;
        border-radius: 8px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .action-confirm:hover {
        background-color: #689f65;
        border-color: #689f65;
        transform: scale(1.05);
    }
</style>
@endsection

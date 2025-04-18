@extends('layouts.admin')

@section('title', 'Chi tiết lịch sử kho sản phẩm')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.product_histories.index') }}">Lịch sử kho</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
        </ol>
    </nav>

    <h2 class="mb-4 fw-semibold">Chi tiết lịch sử kho sản phẩm</h2>

    @php
        $data = $history->data ?? [];
        $old = $history->old_data ?? [];
        $action = $history->action;
        $label = match($action) {
            'create' => 'Thêm',
            'update' => 'Cập nhật',
            'delete' => 'Xoá',
            default => ucfirst($action),
        };
        $badgeClass = match($action) {
            'create' => 'bg-success',
            'update' => 'bg-warning text-dark',
            'delete' => 'bg-danger',
            default => 'bg-secondary',
        };
        $productName = $data['product_name'] ?? $old['product_name'] ?? optional($history->product)->product_name ?? '[Đã xoá]';
    @endphp

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <!-- Overview -->
            <div class="mb-4">
                <h4 class="fw-bold text-success">{{ $productName }}</h4>
                <p class="mb-1"><span class="text-muted">Người thao tác:</span> {{ optional($history->user)->name ?? 'Không xác định' }}</p>
                <p class="mb-0"><span class="text-muted">Hành động:</span> <span class="badge {{ $badgeClass }}">{{ $label }}</span></p>
            </div>

            @if ($action === 'update')
                <div class="row mb-4">
                    <div class="col-md-6 border-end">
                        <h5 class="text-muted mb-3">Trước khi cập nhật</h5>
                        <p><strong>Mô tả:</strong> {{ $old['description'] ?? '[Không có]' }}</p>
                        <p><strong>Giá nhập:</strong> {{ number_format($old['import_price'] ?? 0, 0, ',', '.') }} đ</p>
                        <p><strong>Giá bán:</strong> {{ number_format($old['price'] ?? 0, 0, ',', '.') }} đ</p>
                        <p><strong>Giá khuyến mãi:</strong> {{ number_format($old['discount_price'] ?? 0, 0, ',', '.') }} đ</p>
                        <p><strong>Số lượng:</strong> {{ $old['stock_quantity'] ?? 0 }}</p>
                        @if (!empty($old['expiry_date']))
                            <p><strong>Ngày hết hạn:</strong> {{ \Carbon\Carbon::parse($old['expiry_date'])->format('d/m/Y') }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-muted mb-3">Sau khi cập nhật</h5>
                        <p><strong>Mô tả:</strong> {{ $data['description'] ?? '[Không có]' }}</p>
                        <p><strong>Giá nhập:</strong> {{ number_format($data['import_price'] ?? 0, 0, ',', '.') }} đ</p>
                        <p><strong>Giá bán:</strong> {{ number_format($data['price'] ?? 0, 0, ',', '.') }} đ</p>
                        <p><strong>Giá khuyến mãi:</strong> {{ number_format($data['discount_price'] ?? 0, 0, ',', '.') }} đ</p>
                        <p><strong>Số lượng:</strong> {{ $data['stock_quantity'] ?? 0 }}</p>
                        @if (!empty($data['expiry_date']))
                            <p><strong>Ngày hết hạn:</strong> {{ \Carbon\Carbon::parse($data['expiry_date'])->format('d/m/Y') }}</p>
                        @endif
                    </div>
                </div>
            @else
                <!-- Nếu là create hoặc delete -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Mô tả:</strong> {{ $data['description'] ?? '[Không có]' }}</p>
                        <p><strong>Giá nhập:</strong> {{ number_format($data['import_price'] ?? 0, 0, ',', '.') }} đ</p>
                        <p><strong>Giá bán:</strong> {{ number_format($data['price'] ?? 0, 0, ',', '.') }} đ</p>
                        <p><strong>Giá khuyến mãi:</strong> {{ number_format($data['discount_price'] ?? 0, 0, ',', '.') }} đ</p>
                        <p><strong>Số lượng:</strong> {{ $data['stock_quantity'] ?? 0 }}</p>
                        @if (!empty($data['expiry_date']))
                            <p><strong>Ngày hết hạn:</strong> {{ \Carbon\Carbon::parse($data['expiry_date'])->format('d/m/Y') }}</p>
                        @endif
                    </div>
                    <div class="col-md-6 text-center">
                        @if (!empty($data['image']))
                            <p class="text-muted">Ảnh sản phẩm:</p>
                            <img src="{{ asset($data['image']) }}" alt="Ảnh sản phẩm" class="img-fluid rounded shadow-sm" style="max-width: 250px;">
                        @else
                            <p class="fst-italic text-muted mt-4">Không có ảnh sản phẩm</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Timestamp -->
            <div class="mt-4">
                <p class="text-muted">
                    <i class="bi bi-clock-history me-1"></i>
                    Thời gian thực hiện: {{ $history->created_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <a href="{{ route('admin.product_histories.index') }}" class="btn btn-outline-success mt-3">
                ← Quay lại danh sách
            </a>
        </div>
    </div>
</div>

<style>
    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

    .breadcrumb-item a {
        text-decoration: none;
        color: #4caf50;
    }

    .breadcrumb-item a:hover {
        text-decoration: underline;
    }

    .container {
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

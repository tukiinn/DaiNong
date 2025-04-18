@extends('layouts.admin')

@section('title', 'Lịch sử nhập hàng')

@section('content')
<div class="container bg-white rounded shadow-sm p-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lịch sử kho sản phẩm</li>
        </ol>
    </nav>

    <h4 class="mb-4 fw-bold text-success">Lịch sử kho sản phẩm</h4>

    <!-- Form tìm kiếm -->
    <form action="{{ route('admin.product_histories.index') }}" method="GET" class="row gy-2 gx-3 align-items-center search-date-form mb-4">
        <div class="col-md-3">
            <input type="text" name="product_name" class="form-control" placeholder="🔍 Tên sản phẩm" value="{{ request('product_name') }}">
        </div>
        <div class="col-md-3">
            <input type="text" name="user_name" class="form-control" placeholder="👤 Người thao tác" value="{{ request('user_name') }}">
        </div>
        <div class="col-md-3">
            <select name="action" class="form-select">
                <option value="">⚙️ Hành động</option>
                <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>➕ Thêm</option>
                <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>✏️ Cập nhật</option>
                <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>🗑️ Xoá</option>
            </select>
        </div>
        <div class="col-md-3 d-flex">
            <button type="submit" class="btn search-btn me-2">🔎 Tìm kiếm</button>
            <a href="{{ route('admin.product_histories.index') }}" class="btn btn-outline-secondary">🧹 Đặt lại</a>
        </div>
    </form>

    <!-- Bảng dữ liệu -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-success">
                <tr>
                    <th>#</th>
                    <th>Người thao tác</th>
                    <th>Sản phẩm</th>
                    <th>Hành động</th>
                    <th>Thời gian</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($histories as $history)
                    <tr>
                        <td>{{ $history->id }}</td>
                        <td>{{ optional($history->user)->name ?? 'Không xác định' }}</td>
                        <td>{{ optional($history->product)->product_name ?? '[Đã xóa]' }}</td>
                        <td>
                            @php
                                $label = match($history->action) {
                                    'create' => 'Thêm',
                                    'update' => 'Cập nhật',
                                    'delete' => 'Xoá',
                                    default => $history->action,
                                };
                                $class = match($history->action) {
                                    'create' => 'success',
                                    'update' => 'warning',
                                    'delete' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $class }}">{{ $label }}</span>
                        </td>
                        <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.histories.show', $history->id) }}" class="btn btn-sm btn-outline-primary">
                                Xem
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Không có dữ liệu phù hợp.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center mt-4">
        {{ $histories->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</div>

<style>
    .breadcrumb {
        background: none;
        font-size: 0.95rem;
    }

    .breadcrumb-item a {
        color: #66bb6a;
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        text-decoration: underline;
    }

    .search-btn {
        background-color: #66bb6a;
        color: #fff;
        border: 1px solid #66bb6a;
        transition: background-color 0.3s ease;
    }

    .search-btn:hover {
        background-color: #4caf50;
    }

    .table thead th {
        font-weight: 600;
    }

    .table td, .table th {
        vertical-align: middle;
    }
</style>
@endsection

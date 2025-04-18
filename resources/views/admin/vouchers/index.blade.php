@extends('layouts.admin')

@section('content')
<div class="container p-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Voucher</li>
        </ol>
    </nav>
    <!-- End Breadcrumb -->

    <!-- Tiêu đề trang -->
    <h1 class="mb-4 text-center">Danh sách Voucher</h1>
    <div class="d-flex justify-content-between align-items-center mb-4">
    <!-- Nút Thêm Voucher -->
    <div class="mb-3 text-center">
        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-add">
            <i class="fas fa-plus"></i> Tạo Voucher mới
        </a>
    </div>
        <!-- Form Tìm Kiếm Voucher -->
        <form method="GET" action="{{ route('admin.vouchers.index') }}" class="mb-3 search-form">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm voucher..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-search">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </div>
        </form>
</div>


<form method="POST" action="{{ route('admin.vouchers.bulkAction') }}" id="bulk-action-form">
    @csrf

    <!-- Nút thao tác hàng loạt -->
    <div class="mb-3 d-flex justify-content-end">
        <div>
            <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa các voucher đã chọn?')">
                🗑️ Xóa các voucher đã chọn
            </button>

            <a href="{{ route('admin.vouchers.clean') }}" class="btn btn-warning" onclick="return confirm('Bạn có chắc muốn xóa các voucher hết hạn hoặc đã dùng hết?')">
                🧹 Xóa voucher hết hạn / hết lượt
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table modern-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>Mã Voucher</th>
                    <th>Giảm giá</th>
                    <th>Loại</th>
                    <th>Số lần sử dụng tối đa</th>
                    <th>Số lần đã sử dụng</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vouchers as $voucher)
                <tr>
                    <td>
                        <input type="checkbox" name="selected_vouchers[]" value="{{ $voucher->id }}">
                    </td>
                    <td>{{ $voucher->code }}</td>
                    <td>{{ $voucher->discount }} {{ $voucher->type == 'percentage' ? '%' : '₫' }}</td>
                    <td>{{ ucfirst($voucher->type) }}</td>
                    <td>{{ $voucher->max_usage }}</td>
                    <td>{{ $voucher->used }}</td>
                    <td>{{ $voucher->start_date ? \Carbon\Carbon::parse($voucher->start_date)->format('d/m/Y') : 'Không xác định' }}</td>
                    <td>{{ $voucher->end_date ? \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y') : 'Không xác định' }}</td>
                    <td>
                        <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-icon btn-edit" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                
                        <button type="button"
                            class="btn btn-icon btn-delete"
                            title="Xóa"
                            onclick="deleteSingleVoucher({{ $voucher->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
</form>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center mt-3">
        {{ $vouchers->links() }}
    </div>
</div>

<!-- Inline CSS Styles -->
<style>
    /* Container */
    .container {
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
    h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #343a40;
    }

    /* Nút Thêm Voucher */
    .btn-add {
        background-color: #81c784;
        border: none;
        color: #fff;
        padding: 10px 20px;
        font-size: 1rem;
        border-radius: 50px;
        transition: background-color 0.3s ease, transform 0.3s ease;
        text-decoration: none;
    }
    .btn-add:hover {
        background-color: #689f65;
        transform: translateY(-2px);
    }

    /* Form Tìm Kiếm Voucher */
    .search-form .input-group {
        display: flex;
        align-items: center;
    }
    .search-form .input-group .form-control {
        min-width: 250px;
        border: 1px solid #81c784;
        border-right: none;
        border-radius: 4px 0 0 4px;
        padding: 10px;
    }
    .search-form .input-group .btn {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        background-color: #81c784;
        border: 1px solid #81c784;
        color: #fff;
        padding: 10px 20px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .search-form .input-group .btn:hover {
        background-color: #689f65;
     
    }

    /* Bảng Voucher */
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .modern-table thead {
        background-color: #81c784;
        color: #fff;
    }
    .modern-table th, 
    .modern-table td {
        padding: 15px;
        text-align: center;
        vertical-align: middle;
    }
    .modern-table tbody tr:nth-of-type(odd) {
        background-color: rgba(129,199,132,0.1);
    }
    .modern-table tbody tr:hover {
        background-color: rgba(104,159,101,0.15);
        cursor: pointer;
    }

    /* Nút hành động dạng icon */
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 50%;
        color: #fff;
        margin: 0 2px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .btn-icon:hover {
        transform: scale(1.1);
    }
    /* Nút chỉnh sửa: màu vàng nhạt */
    .btn-edit {
        background-color: #ffc107;
    }
    .btn-edit:hover {
        background-color: #e0a800;
    }
    /* Nút xóa: màu đỏ */
    .btn-delete {
        background-color: #dc3545;
    }
    .btn-delete:hover {
        background-color: #c82333;
    }

    /* Phân trang */
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding-left: 0;
        margin-top: 20px;
    }
    .pagination li {
        margin: 0 5px;
    }
    .pagination li a, 
    .pagination li span {
        color: #81c784;
        border: 1px solid #81c784;
        padding: 8px 12px;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .pagination li a:hover, 
    .pagination li span:hover {
        background-color: #81c784;
        color: #fff;
    }
    .pagination li.active span {
        background-color: #81c784;
        color: #fff;
        border-color: #81c784;
    }
    /* Nút cảnh báo: màu cam nhạt (giống btn-edit) */
.btn-warning {
    background-color: #ffc107;
    border: none;
    color: #fff;
    padding: 10px 20px;
    font-size: 0.95rem;
    border-radius: 50px;
    transition: background-color 0.3s ease, transform 0.3s ease;
    text-decoration: none;
}
.btn-warning:hover {
    background-color: #e0a800;
    transform: translateY(-2px);
}

/* Nút xóa hàng loạt: giống btn-delete */
.btn-danger {
    background-color: #dc3545;
    border: none;
    color: #fff;
    padding: 10px 20px;
    font-size: 0.95rem;
    border-radius: 50px;
    transition: background-color 0.3s ease, transform 0.3s ease;
    text-decoration: none;
}
.btn-danger:hover {
    background-color: #c82333;
    transform: translateY(-2px);
}
</style>
<script>
    // Chọn tất cả
    document.getElementById('select-all').addEventListener('change', function () {
        let checkboxes = document.querySelectorAll('input[name="selected_vouchers[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Hàm xóa 1 voucher bằng bulk
    function deleteSingleVoucher(voucherId) {
        if (confirm("Bạn có chắc chắn muốn xóa voucher này không?")) {
            // Bỏ chọn hết
            let checkboxes = document.querySelectorAll('input[name="selected_vouchers[]"]');
            checkboxes.forEach(cb => cb.checked = false);

            // Tích chọn dòng cần xóa
            let checkbox = document.querySelector(`input[name="selected_vouchers[]"][value="${voucherId}"]`);
            if (checkbox) {
                checkbox.checked = true;

                // Submit form bulk
                document.getElementById('bulk-action-form').submit();
            }
        }
    }
</script>

@endsection

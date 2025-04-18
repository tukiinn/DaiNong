@extends('layouts.admin')

@section('content')
<div class="container p-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Đơn Hàng</li>
        </ol>
    </nav>

    <!-- Tiêu đề trang -->
    <h2 class="mb-4 text-center">Danh Sách Đơn Hàng</h2>

<!-- Form Tìm Kiếm & Lọc Theo Ngày (Kết hợp) -->
<form method="GET" action="{{ route('admin.orders.index') }}" class="mb-2 search-date-form">
    <div class="input-group">
        <input type="date" name="from_date" class="form-control" placeholder="Từ ngày" value="{{ request('from_date') }}">
        <input type="date" name="to_date" class="form-control" placeholder="Đến ngày" value="{{ request('to_date') }}">
        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm đơn hàng..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-search">
            <i class="fas fa-search"></i> Tìm kiếm
        </button>
    </div>
</form>


    @if($orders->count() > 0)
    <!-- Form dùng chung cho thao tác hàng loạt và thao tác cho 1 đơn -->
    <form id="bulkActionForm" action="{{ route('admin.orders.bulkConfirm') }}" method="POST">
        @csrf

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <!-- Checkbox chọn tất cả -->
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Mã</th>
                        <th>Tên khách hàng</th>
                        <th>Phương thức thanh toán</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusMapping = [
                            'pending'   => 'Chờ xác nhận',
                            'confirmed' => 'Đã xác nhận',
                            'shipping'  => 'Đang giao hàng',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy',
                        ];
                        $paymentMapping = [
                            'pending' => 'Chưa thanh toán',
                            'paid'    => 'Đã thanh toán',
                        ];
                    @endphp

                    @foreach($orders as $order)
                    <tr data-status="{{ $order->trang_thai }}">
                        <td>
                            @if(!in_array($order->trang_thai, ['completed', 'cancelled']))
                                <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox">
                            @endif
                        </td>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->ten_khach_hang }}</td>
                        <td>{{ $order->phuong_thuc_thanh_toan }}</td>
                        <td>{{ number_format($order->tong_tien, 0) }} VND</td>
                        <td>
                            <span class="badge 
                                @if($order->trang_thai == 'pending') bg-warning 
                                @elseif($order->trang_thai == 'confirmed') bg-primary 
                                @elseif($order->trang_thai == 'shipping') bg-info 
                                @elseif($order->trang_thai == 'completed') bg-success 
                                @elseif($order->trang_thai == 'cancelled') bg-danger 
                                @else bg-secondary @endif">
                                {{ $statusMapping[$order->trang_thai] ?? $order->trang_thai }}
                            </span>
                            @if($order->phuong_thuc_thanh_toan != 'COD')
                                <span class="badge ms-2 
                                    @if($order->payment_status == 'pending') bg-secondary 
                                    @elseif($order->payment_status == 'paid') bg-success 
                                    @else bg-secondary @endif">
                                    {{ $paymentMapping[$order->payment_status] ?? $order->payment_status }}
                                </span>
                            @endif
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <!-- Action icons -->
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn order-action-btn icon-view" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($order->trang_thai == 'pending')
                               <!-- Xác nhận (cho trạng thái pending) -->
<button type="button" class="btn order-action-btn icon-confirm single-action-btn"
data-action="{{ route('admin.orders.bulkConfirm') }}"
data-order-id="{{ $order->id }}" title="Xác nhận">
<i class="fas fa-check"></i>
</button>
                            @endif
                            @if($order->trang_thai == 'confirmed')
                                <!-- Giao hàng (cho trạng thái confirmed) -->
<button type="button" class="btn order-action-btn icon-ship single-action-btn"
data-action="{{ route('admin.orders.bulkShip') }}"
data-order-id="{{ $order->id }}" title="Giao hàng">
<i class="fas fa-truck"></i>
</button>
                            @endif
                            @if($order->trang_thai == 'shipping')
                             <!-- Hoàn thành (cho trạng thái shipping) -->
<button type="button" class="btn order-action-btn icon-complete single-action-btn"
data-action="{{ route('admin.orders.bulkComplete') }}"
data-order-id="{{ $order->id }}" title="Hoàn thành">
<i class="fas fa-check-double"></i>
</button>
                            @endif
                            @if(!in_array($order->trang_thai, ['completed', 'cancelled']))
                            <button type="button" class="btn order-action-btn icon-cancel single-action-btn"
                            data-action="{{ route('admin.orders.bulkCancel') }}"
                            data-order-id="{{ $order->id }}" title="Hủy đơn hàng">
                            <i class="fas fa-times"></i>
                        </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Bulk action buttons -->
        <div class="mt-3" id="bulkButtons" style="display: none;">
            <button type="button" class="btn btn-success bulk-action-btn" data-action="{{ route('admin.orders.bulkConfirm') }}">Xác nhận</button>
            <button type="button" class="btn btn-primary bulk-action-btn" data-action="{{ route('admin.orders.bulkShip') }}">Giao hàng</button>
            <button type="button" class="btn btn-warning bulk-action-btn" data-action="{{ route('admin.orders.bulkComplete') }}">Hoàn thành</button>
            <button type="button" class="btn btn-danger bulk-action-btn" data-action="{{ route('admin.orders.bulkCancel') }}">Hủy đơn hàng</button>
        </div>
    </form>

    <!-- Phân trang -->
    @if ($orders->hasPages())
        <nav>
            <ul class="pagination justify-content-center mt-3">
                @if ($orders->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">«</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">«</a></li>
                @endif

                @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                    @if ($page == $orders->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach

                @if ($orders->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">»</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">»</span></li>
                @endif
            </ul>
        </nav>
    @endif

    @else
    <div class="alert alert-info">
        Không có đơn hàng nào.
    </div>
    @endif

</div>

<!-- JavaScript: xử lý checkbox, bulk action và single action -->
<script>
    function singleOrderAction(button) {
        const orderId = button.getAttribute('data-order-id');
        const row = button.closest('tr');
        const checkbox = row.querySelector('input[name="order_ids[]"]');
        if(checkbox) {
            checkbox.checked = true;
        }
        const actionUrl = button.getAttribute('data-action');
        document.getElementById('bulkActionForm').setAttribute('action', actionUrl);
        document.getElementById('bulkActionForm').submit();
    }

    document.querySelectorAll('.single-action-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            singleOrderAction(this);
        });
    });

    function updateBulkActionButtons() {
        const selectedCheckboxes = document.querySelectorAll('input[name="order_ids[]"]:checked');
        const bulkButtonsContainer = document.getElementById('bulkButtons');
        if(selectedCheckboxes.length === 0){
            bulkButtonsContainer.style.display = 'none';
            return;
        }
        const statuses = new Set();
        selectedCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            if(row){
                statuses.add(row.getAttribute('data-status'));
            }
        });
        if(statuses.size > 1){
            bulkButtonsContainer.style.display = 'none';
            return;
        }
        const commonStatus = statuses.values().next().value;
        document.querySelectorAll('.bulk-action-btn').forEach(btn => {
            btn.style.display = 'none';
        });
        if(commonStatus === 'pending'){
            document.querySelector('.bulk-action-btn[data-action="{{ route('admin.orders.bulkConfirm') }}"]').style.display = 'inline-block';
            document.querySelector('.bulk-action-btn[data-action="{{ route('admin.orders.bulkCancel') }}"]').style.display = 'inline-block';
        } else if(commonStatus === 'confirmed'){
            document.querySelector('.bulk-action-btn[data-action="{{ route('admin.orders.bulkShip') }}"]').style.display = 'inline-block';
            document.querySelector('.bulk-action-btn[data-action="{{ route('admin.orders.bulkCancel') }}"]').style.display = 'inline-block';
        } else if(commonStatus === 'shipping'){
            document.querySelector('.bulk-action-btn[data-action="{{ route('admin.orders.bulkComplete') }}"]').style.display = 'inline-block';
            document.querySelector('.bulk-action-btn[data-action="{{ route('admin.orders.bulkCancel') }}"]').style.display = 'inline-block';
        } else {
            bulkButtonsContainer.style.display = 'none';
            return;
        }
        bulkButtonsContainer.style.display = 'block';
    }

    document.getElementById('selectAll').addEventListener('change', function(){
        const checkboxes = document.querySelectorAll('input[name="order_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionButtons();
    });

    document.querySelectorAll('input[name="order_ids[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionButtons);
    });

    document.querySelectorAll('.bulk-action-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const actionUrl = this.getAttribute('data-action');
            document.getElementById('bulkActionForm').setAttribute('action', actionUrl);
            document.getElementById('bulkActionForm').submit();
        });
    });
</script>

<!-- CSS Tùy Chỉnh -->
<style>
    /* Container chung */
    .container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    /* Breadcrumb */
    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
    .breadcrumb-item a {
        color: #81c784;
        text-decoration: none;
    }
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #6c757d;
    }
    /* Tiêu đề */
    h2 {
        font-size: 1.75rem;
        font-weight: 600;
        color: #343a40;
        margin-bottom: 20px;
    }
    /* Table */
    .table {
        margin-bottom: 0;
    }
    .table thead {
        background-color: #007bff;
        color: #fff;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.05);
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.075);
    }
    /* Phân trang: sử dụng màu #81c784 */
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
    .pagination li a, .pagination li span {
        color: #81c784;
        border: 1px solid #81c784;
        padding: 8px 12px;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .pagination li a:hover, .pagination li span:hover {
        background-color: #81c784;
        color: #fff;
    }
    .pagination li.active span {
        background-color: #81c784;
        color: #fff;
        border-color: #81c784;
    }
/* Combined form: Tìm kiếm & Lọc theo ngày */
.search-date-form .input-group {
    display: flex;
    align-items: center;
    flex-wrap: nowrap;
}

.search-date-form .input-group .form-control {
    padding: 10px;
    border: 1px solid #81c784;
    /* Bo tròn từng ô, sẽ chỉnh theo vị trí */
    border-radius: 0;
    flex: 1;
}

/* Bo tròn cho ô đầu tiên (Từ ngày) */
.search-date-form .input-group .form-control:first-child {
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}

/* Thêm khoảng cách giữa các ô nhập nếu cần */
.search-date-form .input-group .form-control + .form-control {
    margin-left: 5px;
}

/* Nút tìm kiếm cuối cùng */
.search-date-form .input-group .btn {
    padding: 10px 20px;
    background-color: #81c784;
    border: 1px solid #81c784;
    color: #fff;
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
    transition: background-color 0.3s ease, transform 0.3s ease;
    margin-left: 5px;
}
.search-date-form .input-group .btn:hover {
    background-color: #689f65;
    transform: translateY(-2px);
}

   
    /* CSS cho các nút hành động đơn hàng (icon style) */
.order-action-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%; /* Bo tròn hoàn toàn */
    padding: 0;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    font-size: 1.1rem;
    margin: 2px;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

/* Icon xem chi tiết */
.order-action-btn.icon-view {
    background-color: #17a2b8;  /* Màu info */
    color: #fff;
}
.order-action-btn.icon-view:hover {
    background-color: #138496;
    transform: scale(1.1);
}

/* Icon xác nhận (pending) */
.order-action-btn.icon-confirm {
    background-color: #28a745;  /* Màu success */
    color: #fff;
}
.order-action-btn.icon-confirm:hover {
    background-color: #218838;
    transform: scale(1.1);
}

/* Icon giao hàng (confirmed) */
.order-action-btn.icon-ship {
    background-color: #ffc107;  /* Màu warning */
    color: #fff;
}
.order-action-btn.icon-ship:hover {
    background-color: #e0a800;
    transform: scale(1.1);
}

/* Icon hoàn thành (shipping) */
.order-action-btn.icon-complete {
    background-color: #007bff;  /* Màu primary */
    color: #fff;
}
.order-action-btn.icon-complete:hover {
    background-color: #0069d9;
    transform: scale(1.1);
}

/* Icon hủy đơn hàng (cho các trạng thái không phải completed/cancelled) */
.order-action-btn.icon-cancel {
    background-color: #dc3545;  /* Màu danger */
    color: #fff;
}
.order-action-btn.icon-cancel:hover {
    background-color: #c82333;
    transform: scale(1.1);
}

</style>
@endsection

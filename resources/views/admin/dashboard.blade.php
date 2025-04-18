@extends('layouts.admin')
@section('title', 'Trang Thống Kê')
@section('content')

    <h1 class="text-center mb-3" style="font-size: 2.5rem; font-weight: bold; color: #343a40;">Bảng Thống Kê</h1>
    <style>
        body {
            background-color: #f9fafb;
        }

        .card {
            background-color: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .card-header {
            background-color: #343a40;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            padding: 15px 20px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .card-body {
            padding: 25px;
            font-size: 1.1rem;
        }

        .total-revenue {
            font-size: 1.5rem;
            color: #28a745;
            font-weight: bold;
        }
        .total-revenue-do {
            font-size: 1.5rem;
            color: #f70000;
            font-weight: bold;
        }

        .stats-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px; /* Khoảng cách giữa các cột */
}

.stats-item {
    flex: 0 0 calc(25% - 20px); /* Mỗi cột chiếm khoảng 25% trừ đi gap */
    background-color: #ffffff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    position: relative;
    transition: all 0.3s ease-in-out;
}


        .stats-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .stats-item i {
            position: absolute;
            right: 20px;
            top: 70px;
            font-size: 1.5rem;
        }

        .stats-item .fa-check-circle {
            color: #28a745;
        }

        .stats-item .fa-times-circle {
            color: #dc3545;
        }

        .stats-item .fa-shopping-cart {
            color: #007bff;
        }

        .stats-item .fa-users {
            color: #ffc107;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table th, table td {
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        table th {
            background-color: #f1f3f5;
            color: #343a40;
            font-weight: bold;
        }

        table td {
            color: #555555;
        }

        table tr:hover {
            background-color: #e9ecef;
        }

        .charts-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }

        .card {
            flex: 1;
            min-width: 300px;
        }

        canvas {
            max-width: 100%;
            height: 300px;
        }

        @media (max-width: 768px) {
            .stats-item {
                flex: 0 0 100%;
            }

            .charts-container {
                flex-direction: column;
            }

            h1 {
                font-size: 2rem;
            }
        }
     
    .product-link {
        color: #000; /* Màu chữ mặc định đen */
        transition: color 0.3s ease;
    }
    .product-link:hover {
        color: #28a745; /* Màu xanh lá khi hover */
    }
</style>
   

<div class="container">
    <div class="container-stats mt-3">
        <div class="row">
            @hasanyrole('admin|manager|accountant')
            <!-- Nút xuất Excel -->
            <div class="col-12 text-start mb-3">
                <a href="{{ route('admin.dashboard.exportExcel') }}" class="btn btn-success btn-sm me-2">
                    <i class="fa-solid fa-file-excel"></i> Xuất Excel Đơn Hàng
                </a>
                <a href="{{ route('admin.export-revenue') }}" class="btn btn-success btn-sm">
                    <i class="fa-solid fa-file-excel"></i> Xuất Excel Thống Kê Doanh Thu
                </a>
            </div>
            @endhasanyrole
        </div>
    
        <!-- Thêm gap giữa các hàng -->
        <div class="row g-3">
            @php
            $stats = [
                ['title' => 'Tổng Đơn Hàng', 'value' => $totalOrders, 'icon' => 'fas fa-shopping-cart', 'color' => '#007bff'],
                ['title' => 'Tổng Khách Hàng', 'value' => $totalCustomers, 'icon' => 'fas fa-users', 'color' => '#ffc107'],
                ['title' => 'Doanh Thu (Hoàn Thành)', 'value' => number_format($totalRevenueCompleted, 0, ',', '.') . ' đ', 'icon' => 'fas fa-wallet', 'color' => '#28a745'],
                ['title' => 'Doanh Thu Sau Voucher', 'value' => number_format($totalNetRevenueReceived, 0, ',', '.') . ' đ', 'icon' => 'fas fa-money-check-alt', 'color' => '#28a745'],
                ['title' => 'Đơn Chưa Hoàn Thành', 'value' => $totalPendingOrders, 'icon' => 'fas fa-hourglass-half', 'color' => '#17a2b8'],
                ['title' => 'Đơn Hoàn Thành', 'value' => $totalCompletedOrders, 'icon' => 'fas fa-check-circle', 'color' => '#28a745'],
                ['title' => 'Đơn Đã Hủy', 'value' => $totalCancelledOrders, 'icon' => 'fas fa-ban', 'color' => '#dc3545'],
                ['title' => 'Giá Nhập Hàng', 'value' => number_format($totalImportCost, 0, ',', '.') . ' đ', 'icon' => 'fas fa-boxes-stacked', 'color' => '#143e31'],
                ['title' => 'Lợi Nhuận', 'value' => number_format($totalProfit, 0, ',', '.') . ' đ', 'icon' => 'fas fa-sack-dollar', 'color' => '#ff5733'],
            ];
        @endphp
        
        @foreach($stats as $stat)
            <div class="col-6 col-md-3">
                <div class="card shadow-sm p-3 text-center" style="border-left: 5px solid {{ $stat['color'] }};">
                    <i class="{{ $stat['icon'] }} mb-2" style="font-size: 26px; color: {{ $stat['color'] }};"></i>
                    <h6 class="fw-bold mb-1">{{ $stat['title'] }}</h6>
                    <p class="fs-6 fw-semibold text-dark">{{ $stat['value'] }}</p>
                </div>
            </div>
        @endforeach
        
        </div>
    </div>
    
    
 
    

    <div class="charts-container mt-3">
        <div class="card flex">
            <div class="card-header">Doanh Thu Theo Thời Gian</div>
            <div class="card-body">
                <form id="revenue-form" class="mb-4">
                    <div class="form-group">
                        <label for="timeframe">Chọn Thời Gian:</label>
                        <select id="timeframe" class="form-control" onchange="updateChart()">
                            <option value="day">Theo Ngày</option>
                            <option value="month">Theo Tháng</option>
                            <option value="year">Theo Năm</option>
                        </select>
                    </div>
                </form>
                <canvas id="revenueChart" width="400" height="300"></canvas>
            </div>
        </div>

        <div class="card flex">
            <div class="card-header">Doanh Thu Theo Danh Mục</div>
            <div class="card-body">
                <canvas id="categoryChart" width="300" height="150"></canvas>
            </div>
        </div>

        <div class="card flex">
    <div class="card-header">Theo Phương Thức Thanh Toán</div>
    <div class="card-body">
        <canvas id="paymentChart" width="300" height="150"></canvas>
    </div>
</div>
    </div>

    <div class="container mt-4">
        <div class="row gy-4">
          <!-- Bảng Thống Kê Sản Phẩm Đã Bán -->
<div class="col-md-8">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thống Kê SP Đã Bán</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Sản Phẩm</th>
                            <th>Số Lượng</th>
                            <th>% SL</th>
                            <th>Doanh Thu</th>
                            <th>% DT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($soldProducts as $index => $product)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('products.show', $product->id) }}" class="d-flex align-items-center text-decoration-none product-link">
                                        @if(isset($product->image) && $product->image)
                                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="img-thumbnail me-2" style="width:50px; height:50px;">
                                        @else
                                            <img src="https://via.placeholder.com/50" alt="No image" class="img-thumbnail me-2">
                                        @endif
                                        <span>{{ $product->name }}</span>
                                    </a>
                                </td>
                                
                                <td>{{ $product->sold_quantity }}</td>
                                <td>
                                    @php
                                        $percentageQty = ($totalSoldQuantity > 0) ? ($product->sold_quantity / $totalSoldQuantity) * 100 : 0;
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">{{ number_format($percentageQty, 2) }}%</span>
                                        <div class="progress" style="width: 80px; height: 5px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percentageQty }}%;" aria-valuenow="{{ $percentageQty }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ number_format($product->revenue, 0, ',', '.') }} đ</td>
                                <td>
                                    @php
                                        $percentageRev = ($totalSoldRevenue > 0) ? ($product->revenue / $totalSoldRevenue) * 100 : 0;
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">{{ number_format($percentageRev, 2) }}%</span>
                                        <div class="progress" style="width: 80px; height: 5px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentageRev }}%;" aria-valuenow="{{ $percentageRev }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Custom CSS cho phần Bảng KH Thân Thiết với tên class riêng -->
<style>
    /* Container riêng cho bảng khách hàng thân thiết */
    #loyal-customer-section .loyal-customer-item {
        border: none;
        border-bottom: 1px solid #f1f1f1;
        transition: background 0.3s;
        padding: 1rem 0;
    }
    #loyal-customer-section .loyal-customer-item:hover {
        background: #f9f9f9;
    }
    /* Header card với gradient riêng */
    #loyal-customer-section .loyal-customer-card-header {
        background: linear-gradient(45deg, #28a745, #66bb6a);
    }
    #loyal-customer-section .loyal-customer-card-header h5 {
        color: #fff;
        margin: 0;
    }
    /* Nút mở modal đơn hàng */
    #loyal-customer-section .loyal-customer-btn-view-order {
        transition: background 0.3s;
    }
    #loyal-customer-section .loyal-customer-btn-view-order:hover {
        background: #28a745;
    }
    /* Modal custom cho khách hàng */
    #loyal-customer-section .loyal-customer-modal-header {
        background: #28a745;
        color: #fff;
    }
    #loyal-customer-section .loyal-customer-modal-body {
        padding: 1.5rem;
    }
    /* Bảng sản phẩm trong modal */
    #loyal-customer-section .loyal-customer-table-responsive {
        margin-top: 1rem;
    }
    #loyal-customer-section .loyal-customer-table-bordered th,
    #loyal-customer-section .loyal-customer-table-bordered td {
        border: 1px solid #dee2e6;
    }
    #loyal-customer-section .loyal-customer-table img {
        border-radius: 4px;
        object-fit: cover;
    }
  </style>
  
  <!-- Bảng KH Thân Thiết với tên class riêng -->
  <div id="loyal-customer-section" class="col-md-4">
    <div class="card shadow-sm loyal-customer-card">
      <div class="card-header loyal-customer-card-header">
        <h5 class="mb-0">Bảng KH Thân Thiết</h5>
      </div>
      <div class="card-body">
        <div class="list-group loyal-customer-list-group">
          @foreach($loyalCustomers as $index => $customer)
          @php
          
          if($customer->total_spent >= 5000000) {
          
              $icon = '<i class="fas fa-gem text-info" title="Kim Cương"></i>';
          } elseif($customer->total_spent >= 3000000) {
           
              $icon = '<i class="fas fa-award text-warning" title="Vàng"></i>';
          } elseif($customer->total_spent >= 1000000) {
            
              $icon = '<i class="fas fa-medal text-secondary" title="Bạc"></i>';
          } else {
             
              $icon = '<i class="fas fa-user text-muted" title="Thành viên"></i>';
          }
      @endphp
      
            <div class="list-group-item loyal-customer-item">
              <div class="d-flex align-items-center">
                <img src="{{ isset($customer->avatar) && $customer->avatar ? asset($customer->avatar) : asset('images/avatars/avtdf.jpg') }}" alt="{{ $customer->name }}" class="img-thumbnail me-2" style="width:40px; height:40px;">
                <div>
                  <h6 class="mb-0">{{ $customer->name }}</h6>
                  <small class="text-muted">{{ $customer->email }}</small>
                </div>
                <div class="ms-auto">
                  {!! $icon !!}
                </div>
              </div>
              <div class="mt-2">
                <small><strong>Đơn hàng:</strong> {{ $customer->order_count }}</small><br>
                <small><strong>Chi Tiêu:</strong> {{ number_format($customer->total_spent, 0, ',', '.') }} đ</small>
              </div>
              <!-- Nút mở modal đơn hàng -->
              <div class="mt-2">
                <button type="button" class="btn btn-outline-success btn-sm loyal-customer-btn-view-order" data-bs-toggle="modal" data-bs-target="#modalCustomer{{ $customer->id }}">
                  Xem đơn hàng
                </button>
              </div>
            </div>
            
            <!-- Modal chứa thông tin đơn hàng và chi tiết sản phẩm -->
            <div class="modal fade" id="modalCustomer{{ $customer->id }}" tabindex="-1" aria-labelledby="modalCustomerLabel{{ $customer->id }}" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header loyal-customer-modal-header">
                    <h5 class="modal-title" id="modalCustomerLabel{{ $customer->id }}">Đơn hàng của {{ $customer->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                  </div>
                  <div class="modal-body loyal-customer-modal-body">
                    @if($customer->orders->count())
                      @foreach($customer->orders as $order)
                        <div class="mb-4">
                          <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <strong>Mã đơn hàng:</strong> {{ $order->id }}<br>
                                <strong>Ngày đặt:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}
                              </div>
                              <div>
                                <strong>Tổng tiền:</strong> {{ number_format($order->tong_tien, 0, ',', '.') }} đ
                              </div>
                            </div>
                          </div>
                          @if($order->orderItems->count())
                            <div class="table-responsive loyal-customer-table-responsive">
                              <table class="table table-sm table-bordered loyal-customer-table-bordered">
                                <thead class="table-light">
                                  <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach($order->orderItems as $item)
                                    <tr>
                                      <td>
                                        <div class="d-flex align-items-center">
                                          <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->product_name }}" style="width:40px; height:40px;" class="me-2">
                                          <div>
                                            {{ $item->product->product_name }}
                                            @if(isset($item->size))
                                              <br><small class="text-muted">Size: {{ $item->size }}</small>
                                            @elseif(isset($item->product->size))
                                              <br><small class="text-muted">Size: {{ $item->product->size }}</small>
                                            @endif
                                          </div>
                                        </div>
                                      </td>
                                      <td>{{ $item->so_luong }}</td>
                                      <td>{{ number_format($item->gia, 0, ',', '.') }} đ</td>
                                      <td>{{ number_format($item->thanh_tien, 0, ',', '.') }} đ</td>
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                          @else
                            <small>Không có thông tin sản phẩm.</small>
                          @endif
                        </div>
                      @endforeach
                    @else
                      <p>Không có đơn hàng nào.</p>
                    @endif
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
    
  </div>


        </div>
    </div>
    
    
    
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy tham chiếu tới các canvas
        const revenueCanvas = document.getElementById('revenueChart');
        const categoryCanvas = document.getElementById('categoryChart');
        const paymentCanvas = document.getElementById('paymentChart');
    
        if (!revenueCanvas || !categoryCanvas || !paymentCanvas) {
            console.error('Không tìm thấy một hoặc nhiều canvas cần thiết.');
            return;
        }
    
        const ctx = revenueCanvas.getContext('2d');
        const categoryCtx = categoryCanvas.getContext('2d');
        const paymentCtx = paymentCanvas.getContext('2d');
    
        let revenueChart;
        let categoryChart;
        let paymentChart;
    
        // Hàm cập nhật biểu đồ doanh thu và doanh thu theo danh mục dựa vào thời gian
        function updateChart() {
            const timeframe = document.getElementById('timeframe').value;
    
            // Fetch dữ liệu doanh thu theo thời gian
            fetch(`/admin/revenue-data?timeframe=${timeframe}`)
                .then(response => response.json())
                .then(data => {
                    const completedLabels = data.completed.map(item => item.label);
                    const completedRevenue = data.completed.map(item => item.revenue);
    
                    if (revenueChart) {
                        revenueChart.destroy();
                    }
    
                    revenueChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: completedLabels,
                            datasets: [{
                                label: 'Doanh thu (VNĐ)',
                                data: completedRevenue,
                                backgroundColor: 'rgba(40, 167, 69, 0.5)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Lỗi khi lấy dữ liệu doanh thu:', error));
    
            // Custom colors cho biểu đồ danh mục
            const customColors = [
                '#FF5733', // Cam sáng
                '#33FF57', // Xanh lá sáng
                '#3357FF', // Xanh dương sáng
                '#FF33A1', // Hồng sáng
                '#FFFF33', // Vàng sáng
                '#33FFF7', // Xanh lam
                '#FF8C33'  // Một sắc cam khác
            ];
    
            // Fetch dữ liệu doanh thu theo danh mục
            fetch('/admin/category-revenue-data')
                .then(response => response.json())
                .then(data => {
                    const categoryLabels = data.map(item => item.category_name);
                    const categoryRevenue = data.map(item => item.revenue);
    
                    if (categoryChart) {
                        categoryChart.destroy();
                    }
    
                    categoryChart = new Chart(categoryCtx, {
                        type: 'doughnut',
                        data: {
                            labels: categoryLabels,
                            datasets: [{
                                label: 'Doanh Thu Theo Danh Mục',
                                data: categoryRevenue,
                                backgroundColor: customColors,
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            const formattedValue = new Intl.NumberFormat('vi-VN', {
                                                style: 'currency',
                                                currency: 'VND'
                                            }).format(tooltipItem.raw);
                                            return `${tooltipItem.label}: ${formattedValue}`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Lỗi khi lấy dữ liệu doanh thu theo danh mục:', error));
        }
    
        // Fetch dữ liệu doanh thu theo phương thức thanh toán
        fetch('/admin/payment-revenue-data')
            .then(response => response.json())
            .then(data => {
                // Map trường phuong_thuc_thanh_toan sang nhãn người dùng thân thiện
                const paymentLabels = data.map(item => {
                    const method = item.phuong_thuc_thanh_toan || 'Unknown';
                    switch (method) {
                        case 'COD':
                            return 'Thanh toán khi nhận hàng';
                        case 'momo':
                            return 'MoMo';
                        case 'momo_qr':
                            return 'MoMo(QR)';
                        default:
                            return method.charAt(0).toUpperCase() + method.slice(1);
                    }
                });
    
                const paymentRevenue = data.map(item => item.revenue);
    
                paymentChart = new Chart(paymentCtx, {
                    type: 'pie',
                    data: {
                        labels: paymentLabels,
                        datasets: [{
                            label: 'Doanh Thu Theo Phương Thức Thanh Toán',
                            data: paymentRevenue,
                            backgroundColor: paymentRevenue.map((_, index) => `hsl(${index * 50}, 70%, 50%)`),
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        const formattedValue = new Intl.NumberFormat('vi-VN', {
                                            style: 'currency',
                                            currency: 'VND'
                                        }).format(tooltipItem.raw);
                                        return `${tooltipItem.label}: ${formattedValue}`;
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Lỗi khi lấy dữ liệu doanh thu theo phương thức thanh toán:', error));
    
        // Gắn sự kiện thay đổi cho input timeframe
        const timeframeInput = document.getElementById('timeframe');
        if (timeframeInput) {
            timeframeInput.addEventListener('change', updateChart);
        } else {
            console.error('Không tìm thấy phần tử timeframe.');
        }
    
        // Khởi tạo biểu đồ ngay khi DOM sẵn sàng
        updateChart();
    });
    </script>
    
    </div>
@endsection

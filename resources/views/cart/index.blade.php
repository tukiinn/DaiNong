@extends('layouts.app')

@section('content')
<!-- CSS tùy chỉnh: loại bỏ đường viền dọc của bảng và định dạng lại một số thành phần -->
<style>
/* Loại bỏ đường viền dọc cho bảng giỏ hàng */
.table-bordered td,
.table-bordered th {
    border-left: 0 !important;
    border-right: 0 !important;
}
.ttmua{
    background: none !important;  /* Loại bỏ nền */
    border: none !important;       /* Loại bỏ viền */
    box-shadow: none !important;   /* Loại bỏ đổ bóng nếu có */
    color: inherit;                /* Dùng màu chữ của phần tử cha (hoặc bạn có thể đặt màu cụ thể) */
    font-size: 15px;
    font-weight: bold;
}
</style>

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
    <a href="{{ route('cart.checkout') }}" class="col-md-4 step inactive">
        <div class="step-number">02</div>
        <div class="step-content">
            <h4>Chi tiết thanh toán</h4>
            <p>Thanh toán danh sách sản phẩm</p>
        </div>
    </a>

    <!-- Bước 3: Hoàn thành đơn hàng -->
    <a  class="col-md-4 step inactive">
        <div class="step-number">03</div>
        <div class="step-content">
            <h4>Hoàn thành đơn hàng</h4>
            <p>Xem lại đơn hàng</p>
        </div>
    </a>
</div>
</div>

<div class="container my-5">
    @if($cartItems && count($cartItems) > 0)
        <div class="row">
            <!-- Cột bên trái: Giỏ hàng của bạn -->
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-end">Giá</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Tổng phụ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                            $grandTotal = 0; 
                        @endphp
                        @foreach($cartItems as $cartKey => $item)
                            @php
                                $key = $item['product_id'] . '_' . $item['size'];
                                // Nếu discount_price tồn tại và > 0, sử dụng discount_price; nếu không, dùng price
                                $basePrice = (isset($item['discount_price']) && $item['discount_price'] > 0)
                                             ? $item['discount_price']
                                             : $item['price'];
                                $weightFactor = 1;
                                // Nếu sản phẩm bán theo kg và có thông tin size, tính hệ số cân nặng
                                if (isset($item['size']) && isset($item['unit']) && $item['unit'] === 'kg') {
                                    switch ($item['size']) {
                                        case '500g':
                                            $weightFactor = 0.5;
                                            break;
                                        case '250g':
                                            $weightFactor = 0.25;
                                            break;
                                        case '1kg':
                                        default:
                                            $weightFactor = 1;
                                            break;
                                    }
                                }
                                $effectivePrice = $basePrice * $weightFactor;
                                $lineTotal = $effectivePrice * $item['so_luong'];
                                $grandTotal += $lineTotal;
                            @endphp
                        
                            <tr>
                                <td class="product-cell">
                                    <div class="d-flex align-items-center">
                                        <form action="{{ route('cart.remove', $item['product_id']) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="size" value="{{ $item['size'] ?? '' }}">
                                            <button type="submit" class="btn btn-lg me-2 border-0">×</button>
                                        </form>
                                        <a href="{{ route('products.show', $item['product_id']) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                            <img src="{{ asset($item['image'] ?? 'https://via.placeholder.com/100') }}" alt="{{ $item['name'] }}" class="img-fluid" style="max-width: 80px;">
                                            <div class="ms-3">
                                                <div>{{ $item['name'] }}</div>
                                                @if(isset($item['size']) && $item['size'])
                                                    <small class="text-muted">Size: {{ $item['size'] }}</small>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-end">{{ number_format($effectivePrice, 0, ',', '.') }} VND</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-lg" onclick="changeQuantity('{{ $item['product_id'] }}', '{{ $item['size'] }}', -1)">-</button>
                                    <span id="quantity-{{ $item['product_id'] }}-{{ $item['size'] }}">{{ $item['so_luong'] }}</span>
                                    <button type="button" class="btn btn-lg" onclick="changeQuantity('{{ $item['product_id'] }}', '{{ $item['size'] }}', 1)">+</button>
                                </td>
                                <td class="text-end" id="lineTotal-{{ $item['product_id'] }}-{{ $item['size'] }}">
                                    {{ number_format($lineTotal, 0, ',', '.') }} VND
                                </td>
                            </tr>
                        @endforeach
                        
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('products.index') }}" class="btn ttmua">
                    <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                </a>
            </div>
            <div class="col-md-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center">Cộng giỏ hàng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Tạm tính:</td>
                            <td class="text-end" id="grandTotal">{{ number_format($grandTotal, 0, ',', '.') }} VND</td>
                        </tr>
                        <tr>
                            <td>Giao hàng:</td>
                            <td class="text-end">Giao hàng miễn phí</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <small class="text-muted">
                                    Shipping options will be updated during checkout.
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <td>Tính phí giao hàng:</td>
                            <td class="text-end">0 VND</td>
                        </tr>
                        <tr class="table-secondary">
                            <td class="fw-bold">Tổng:</td>
                            <td class="fw-bold text-end" id="grandTotal1">{{ number_format($grandTotal, 0, ',', '.') }} VND</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-end">
                                @if(auth()->check())
                                    <a href="{{ route('cart.checkout') }}" class="btn btn-success">
                                        Tiến hành thanh toán
                                    </a>
                                @else
                                    <button type="button" class="btn btn-success" onclick="requireLogin()">
                                        Tiến hành thanh toán
                                    </button>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <p class="alert alert-warning mt-3">Giỏ hàng của bạn trống!</p>
    @endif
</div>

<script>
  function changeQuantity(id, size, delta) {
    const quantityEl = document.getElementById('quantity-' + id + '-' + size);
    if (!quantityEl) {
        console.error(`Element with ID quantity-${id}-${size} not found`);
        return;
    }

    let currentQuantity = parseInt(quantityEl.textContent) || 0;
    let newQuantity = Math.max(0, currentQuantity + delta);

    fetch('{{ route("updateQuantity") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id: id, size: size, quantity: newQuantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('quantity-' + id + '-' + size).textContent = data.newQuantity;
            document.getElementById('lineTotal-' + id + '-' + size).textContent = data.lineTotalFormatted;
            if (document.getElementById('grandTotal')) {
                document.getElementById('grandTotal').textContent = data.grandTotalFormatted;
                document.getElementById('grandTotal1').textContent = data.grandTotalFormatted;
            }
        } else {
            console.error('Error:', data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

</script>

    

<script>
    $(document).ready(function () {
        $('#menu').metisMenu();
    });

    function requireLogin() {
        Swal.fire({
            icon: 'warning',
            title: 'Bạn chưa đăng nhập!',
            text: 'Vui lòng đăng nhập để tiếp tục thanh toán.',
            showCancelButton: true,
            confirmButtonText: 'Đăng nhập ngay',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('login') }}";
            }
        });
    }
</script>
@endsection

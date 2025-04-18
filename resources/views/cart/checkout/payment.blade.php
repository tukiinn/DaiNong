@extends('layouts.app')

@section('content')
    <style>
        @media (max-width: 768px) {
            #order-form {
                padding: 15px;
            }

            /* Giảm kích thước select nếu cần */
            .form-select {
                font-size: 14px;
            }
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
            <a href="{{ route('cart.checkout') }}" class="col-md-4 step active">
                <div class="step-number">02</div>
                <div class="step-content">
                    <h4>Chi tiết thanh toán</h4>
                    <p>Thanh toán danh sách sản phẩm</p>
                </div>
            </a>

            <!-- Bước 3: Hoàn thành đơn hàng -->
            <a class="col-md-4 step inactive">
                <div class="step-number">03</div>
                <div class="step-content">
                    <h4>Hoàn thành đơn hàng</h4>
                    <p>Xem lại đơn hàng</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Nội dung thanh toán và chi tiết đơn hàng -->
    <div class="container my-5">
        <div class="row">
            <!-- Cột bên trái: Thông tin thanh toán -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header text-dark">
                        <h4 class="mb-0">Thông tin thanh toán</h4>
                    </div>
                    <div class="card-body">
                        <!-- Chọn địa chỉ đã lưu -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5>Chọn địa chỉ đã lưu</h5>
                            </div>
                            <div class="card-body">
                                @if($savedAddresses->isEmpty())
                                    <p>Hiện chưa có địa chỉ nào được lưu.</p>
                                @else
                                    @foreach($savedAddresses as $address)
                                        <div class="form-check">
                                            <input class="form-check-input address-radio" type="radio" name="selected_address"
                                                id="address_{{ $address->id }}" data-name="{{ $address->name_address }}"
                                                data-phone="{{ $address->phone_address }}" data-province="{{ $address->province }}"
                                                data-district="{{ $address->district }}" data-ward="{{ $address->ward }}"
                                                data-detailed="{{ $address->detailed_address }}" value="{{ $address->id }}">
                                            <label class="form-check-label" for="address_{{ $address->id }}">
                                                @if($address->address_name === 'Nhà riêng')
                                                    <i class="fas fa-home text-primary"></i> <!-- Icon nhà riêng -->
                                                @elseif($address->address_name === 'Văn phòng')
                                                    <i class="fas fa-building text-warning"></i> <!-- Icon văn phòng -->
                                                @endif  
                                                {{ $address->name_address }} - {{ $address->phone_address }} <br>
                                                {{ $address->full_address }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif

                            </div>
                        </div>

                        <!-- Form đặt hàng -->
                        <form id="order-form" method="POST" class="mt-3">
                            @csrf
                            <div class="mb-3">
                                <label for="ten_khach_hang" class="form-label">Tên khách hàng</label>
                                <input type="text" class="form-control" id="ten_khach_hang" name="ten_khach_hang"
                                    placeholder="Nhập tên khách hàng" required>
                            </div>

                            <div class="mb-3">
                                <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai"
                                    placeholder="Nhập số điện thoại" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="province" class="form-label">Tỉnh/Thành Phố</label>
                                    <select id="province" class="form-select" required>
                                        <option value="">Chọn Tỉnh/Thành</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="district" class="form-label">Quận/Huyện</label>
                                    <select id="district" class="form-select" required disabled>
                                        <option value="">Chọn Quận/Huyện</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="ward" class="form-label">Phường/Xã</label>
                                    <select id="ward" class="form-select" required disabled>
                                        <option value="">Chọn Phường/Xã</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="detailed_address" class="form-label">Địa Chỉ Chi Tiết</label>
                                <input type="text" name="detailed_address" id="detailed_address" class="form-control"
                                    placeholder="Số nhà, đường..." required>
                            </div>
                            <!-- Trường ẩn chứa final_total -->
                            <input type="hidden" name="voucher_code" id="voucher_code">
                            <input type="hidden" name="final_total" id="final-total-input" value="{{ $cartTotal }}">
                            <input type="hidden" name="dia_chi" id="dia_chi" class="form-control" required>
                          
                        </form>
                    </div>
                </div>
            </div>

            <!-- Cột bên phải: Chi tiết đơn hàng & lựa chọn phương thức thanh toán -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 class="mb-0">Chi tiết đơn hàng</h4>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-end">Tổng phụ</th>
                                </tr>
                                @php
                                    $total = 0;
                                @endphp
                                @foreach ($cartItems as $item)
                                @php
                                // Lấy giá của sản phẩm: ưu tiên sử dụng discount_price nếu nó > 0, ngược lại dùng price
                                $price = ($item->product->discount_price > 0) ? $item->product->discount_price : $item->product->price;
                            
                                // Xác định hệ số cân nặng theo size (chỉ áp dụng nếu đơn vị của sản phẩm là 'kg')
                                $weightFactor = 1;
                                if (optional($item->product->unit)->unit_name === 'kg' && $item->size) {
                                    if ($item->size === '500g') {
                                        $weightFactor = 0.5;
                                    } elseif ($item->size === '250g') {
                                        $weightFactor = 0.25;
                                    } else {
                                        // Với '1kg' hoặc các trường hợp khác, mặc định là 1
                                        $weightFactor = 1;
                                    }
                                }
                            
                                // Tính giá cho từng dòng: giá x hệ số x số lượng
                                $lineTotal = $price * $weightFactor * $item->quantity;
                                $total += $lineTotal;
                            @endphp
                            
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center position-relative">
                                                                        <img src="{{ asset($item->product->image ?? 'https://placehold.it/50x50') }}"
                                                                            alt="{{ $item->product->product_name }}"
                                                                            style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                                                        <span class="position-absolute top-0 start-0 translate-middle badge bg-danger">
                                                                            {{ $item->quantity }}
                                                                        </span>
                                                                        <div>
                                                                            <span class="d-block fw-bold">{{ $item->product->product_name }}</span>
                                                                            <p>{{ $item->size }}</p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="text-end">
                                                                    {{ number_format($lineTotal, 0, ',', '.') }}₫
                                                                </td>
                                                            </tr>
                                @endforeach

                                <tr>
                                    <td>Giao hàng:</td>
                                    <td class="text-end">Giao hàng miễn phí</td>
                                </tr>
                                <tr>
                                    <td>Tạm tính:</td>
                                    <td class="text-end">{{ number_format($total, 0, ',', '.') }}₫</td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="input-group">
                                            <input type="text" id="voucher-code" class="form-control"
                                                placeholder="Nhập mã giảm giá">
                                            <button id="apply-voucher" class="btn btn-primary">Áp dụng</button>
                                        </div>
                                        <p id="voucher-message" class="mt-2"></p>

                                        @if(Auth::check() && Auth::user()->vouchers->isNotEmpty())
                                            <div id="voucher-suggestions" class="mt-3">
                                                <p>Các voucher đang sở hữu:</p>
                                                <ul class="list-group">
                                                    @foreach(Auth::user()->vouchers as $voucher)
                                                        {{-- Hiển thị voucher nếu số lượt đã dùng (used) nhỏ hơn max_usage --}}
                                                        @if($voucher->used < $voucher->max_usage)
                                                            <li class="list-group-item voucher-suggestion"
                                                                data-code="{{ $voucher->code }}">
                                                                {{ $voucher->code }} -
                                                                @if($voucher->type == 'fixed')
                                                                    {{ number_format($voucher->discount, 0, ',', '.') }}đ
                                                                @elseif($voucher->type == 'percentage')
                                                                    {{ $voucher->discount }}%
                                                                @endif
                                                                <span class="text-muted"> (Đã dùng: {{ $voucher->used }} /
                                                                    {{ $voucher->max_usage }})</span>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Hiển thị thông tin voucher và giảm giá -->
                                <tr id="voucher-info" style="display: none;">
                                    <td>Giảm giá từ voucher:</td>
                                    <td class="text-end text-danger">
                                        <span id="discount-type"></span><br>
                                        <span id="discount-amount"></span>
                                    </td>
                                </tr>

                                <!-- Hiển thị tổng tiền sau khi giảm giá -->
                                <tr class="table-secondary">
                                    <td><strong>Tổng:</strong></td>
                                    <td class="text-end fw-bold" id="final-total">
                                        {{ number_format($cartTotal, 0, ',', '.') }}₫
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                      <!-- Lựa chọn phương thức thanh toán -->
                      <div class="mb-3">
                        <label class="form-label"><strong>Phương thức thanh toán</strong></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="COD"
                                value="COD" form="order-form" required style="vertical-align: middle;">
                            <label class="form-check-label" for="COD" style="display: inline-flex; align-items: center;">
                                <img src="{{ asset('images/icon/COD.png') }}" alt="COD" style="width:25px; margin-right:5px; vertical-align: middle;">
                                Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="VNPay"
                                value="VNPay" form="order-form" required style="vertical-align: middle;">
                            <label class="form-check-label" for="VNPay" style="display: inline-flex; align-items: center;">
                                <img src="{{ asset('images/icon/VNPay.png') }}" alt="VNPay" style="width:25px; margin-right:5px; vertical-align: middle;">
                                Thanh toán VNPay
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="Momo"
                                value="Momo" form="order-form" required style="vertical-align: middle;">
                            <label class="form-check-label" for="Momo" style="display: inline-flex; align-items: center;">
                                <img src="{{ asset('images/icon/Momo.png') }}" alt="Momo" style="width:25px; margin-right:5px; vertical-align: middle;">
                                Thanh toán Momo
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="Paypal"
                                value="Paypal" form="order-form" required style="vertical-align: middle;">
                            <label class="form-check-label" for="Paypal" style="display: inline-flex; align-items: center;">
                                <img src="{{ asset('images/icon/Paypal.png') }}" alt="Paypal" style="width:25px; margin-right:5px; vertical-align: middle;">
                                Thanh toán Paypal
                            </label>
                        </div>
                    </div>
                                     
                        <!-- Nút đặt hàng -->
                        <div class="d-grid">
                            <button type="submit" form="order-form" name="redirect" class="btn btn-success">Đặt hàng</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript kiểm tra dữ liệu và thay đổi action của form theo phương thức thanh toán -->
    <script>
        document.getElementById('apply-voucher').addEventListener('click', function () {
            const code = document.getElementById('voucher-code').value;
            const cartTotal = {{ $cartTotal }}; // Tổng tiền ban đầu từ PHP

            fetch("{{ route('voucher.apply') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ code: code })
            })
                .then(response => response.json())
                .then(data => {
                    const messageEl = document.getElementById('voucher-message');
                    const voucherInfoRow = document.getElementById('voucher-info');
                    const discountTypeEl = document.getElementById('discount-type');
                    const discountAmountEl = document.getElementById('discount-amount');
                    const finalTotalEl = document.getElementById('final-total');
                    const finalTotalInput = document.getElementById('final-total-input');
                    const voucherCode = document.getElementById('voucher_code');

                    if (data.error) {
                        messageEl.textContent = data.error;
                        messageEl.classList.remove('text-success');
                        messageEl.classList.add('text-danger');
                        voucherInfoRow.style.display = 'none';
                    } else {
                        messageEl.textContent = "Voucher hợp lệ!";
                        messageEl.classList.remove('text-danger');
                        messageEl.classList.add('text-success');
                        if (data.type === 'percentage') {
                            discountTypeEl.textContent = "Giảm " + Math.floor(data.voucherValue) + "%";
                        } else {
                            discountTypeEl.textContent = "Giảm " + Math.floor(data.voucherValue).toLocaleString() + "₫";
                        }

                        // Hiển thị số tiền giảm được (computed discount)
                        discountAmountEl.textContent = "- " + Math.floor(data.discount).toLocaleString() + "₫";

                        // Cập nhật finalTotal trên giao diện và trường ẩn trong form
                        finalTotalEl.textContent = Math.floor(data.finalTotal).toLocaleString() + "₫";
                        finalTotalInput.value = Math.floor(data.finalTotal);
                        // Đẩy voucher_code vào input hidden
                        voucherCode.value = data.voucher_code;

                        voucherInfoRow.style.display = 'table-row';
                    }
                })
                .catch(error => {
                    console.error("Có lỗi xảy ra khi áp dụng voucher:", error);
                });
        });
    </script>
    <script>

        document.getElementById('order-form').addEventListener('submit', function (e) {
            // Lấy dữ liệu từ form
            let tenKhachHang = document.getElementById('ten_khach_hang').value.trim();
            let soDienThoai = document.getElementById('so_dien_thoai').value.trim();
            let diaChi = document.getElementById('dia_chi').value.trim();

            // Biến kiểm tra
            let isValid = true;

            // Reset các thông báo lỗi cũ
            document.getElementById('error-ten-khach-hang').classList.add('d-none');
            document.getElementById('error-so-dien-thoai').classList.add('d-none');
            document.getElementById('error-dia-chi').classList.add('d-none');

            // Kiểm tra tên khách hàng (6 - 20 ký tự)
            if (tenKhachHang.length < 6 || tenKhachHang.length > 20) {
                document.getElementById('error-ten-khach-hang').classList.remove('d-none');
                isValid = false;
            }

            // Kiểm tra số điện thoại với các điều kiện chi tiết hơn
            if (soDienThoai.length !== 10) {
                document.getElementById('error-so-dien-thoai').innerText = 'Số điện thoại phải có đúng 10 chữ số.';
                document.getElementById('error-so-dien-thoai').classList.remove('d-none');
                isValid = false;
            } else if (!/^0[35789]\d{8}$/.test(soDienThoai)) {
                document.getElementById('error-so-dien-thoai').innerText = 'Số điện thoại không định dạng';
                document.getElementById('error-so-dien-thoai').classList.remove('d-none');
                isValid = false;
            } else if (/^(\d)\1{9}$/.test(soDienThoai)) {
                document.getElementById('error-so-dien-thoai').innerText = 'Số điện thoại không hợp lệ. Vui lòng kiểm tra lại.';
                document.getElementById('error-so-dien-thoai').classList.remove('d-none');
                isValid = false;
            }
            // Nếu không hợp lệ thì ngăn form submit
            if (!isValid) {
                e.preventDefault();
            }
        });

        document.addEventListener("DOMContentLoaded", function () {
    const orderForm = document.getElementById("order-form");
    const paymentMethods = document.querySelectorAll("input[name='phuong_thuc_thanh_toan']");

    paymentMethods.forEach(method => {
        method.addEventListener("change", function () {
            // Thay đổi action của form dựa trên phương thức thanh toán đã chọn
            if (this.value === "COD") {
                orderForm.action = "{{ route('orders.create') }}";
            } else if (this.value === "VNPay") {
                orderForm.action = "{{ route('vnpay.vn') }}";
            } else if (this.value === "Momo") {
                orderForm.action = "{{ route('momo.vn') }}";
            } else if (this.value === "Paypal") {
                orderForm.action = "{{ route('paypal.createOrder') }}";
            }
        });
    });
});

    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.voucher-suggestion').forEach(item => {
                item.addEventListener('click', function () {
                    const code = this.getAttribute('data-code');
                    document.getElementById('voucher-code').value = code;
                });
            });
        });
    </script>

    <!-- Toàn bộ script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let provinceData = []; // Lưu danh sách tỉnh/thành

            // Gọi API lấy danh sách tỉnh/thành phố
            fetch('https://provinces.open-api.vn/api/?depth=1')
                .then(response => response.json())
                .then(data => {
                    provinceData = data;
                    let provinceSelects = document.querySelectorAll('#province');
                    provinceSelects.forEach(select => {
                        select.innerHTML = '<option value="">Chọn Tỉnh/Thành</option>';
                        data.forEach(province => {
                            let option = document.createElement('option');
                            option.value = province.code;
                            option.text = province.name;
                            select.add(option);
                        });
                    });
                });

            // Hàm tải danh sách quận/huyện
            function loadDistricts(provinceCode, districtSelect, wardSelect, selectedDistrict = null, selectedWard = null) {
                fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                        districtSelect.disabled = false;
                        data.districts.forEach(district => {
                            let option = document.createElement('option');
                            option.value = district.code;
                            option.text = district.name;
                            districtSelect.add(option);
                        });

                        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                        wardSelect.disabled = true;

                        if (selectedDistrict) {
                            setTimeout(() => {
                                districtSelect.value = selectedDistrict;
                                districtSelect.dispatchEvent(new Event('change'));

                                if (selectedWard) {
                                    setTimeout(() => {
                                        wardSelect.value = selectedWard;
                                    }, 500);
                                }
                            }, 500);
                        }
                    });
            }

            // Hàm tải danh sách phường/xã
            function loadWards(districtCode, wardSelect, selectedWard = null) {
                fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                        wardSelect.disabled = false;
                        data.wards.forEach(ward => {
                            let option = document.createElement('option');
                            option.value = ward.code;
                            option.text = ward.name;
                            wardSelect.add(option);
                        });

                        if (selectedWard) {
                            setTimeout(() => {
                                wardSelect.value = selectedWard;
                            }, 500);
                        }
                    });
            }

            // Xử lý chọn tỉnh
            document.querySelector('#province').addEventListener('change', function () {
                let provinceCode = this.value;
                let districtSelect = document.getElementById('district');
                let wardSelect = document.getElementById('ward');

                if (provinceCode) {
                    loadDistricts(provinceCode, districtSelect, wardSelect);
                } else {
                    districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                    districtSelect.disabled = true;
                    wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                    wardSelect.disabled = true;
                }
                updateFullAddress();
            });

            // Xử lý chọn quận/huyện
            document.querySelector('#district').addEventListener('change', function () {
                let districtCode = this.value;
                let wardSelect = document.getElementById('ward');

                if (districtCode) {
                    loadWards(districtCode, wardSelect);
                } else {
                    wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                    wardSelect.disabled = true;
                }
                updateFullAddress();
            });

            // Khi chọn phường/xã
            document.querySelector('#ward').addEventListener('change', function () {
                updateFullAddress();
            });

            // Khi người dùng nhập địa chỉ chi tiết
            document.querySelector('#detailed_address').addEventListener('input', function () {
                updateFullAddress();
            });

            // Xử lý chọn địa chỉ đã lưu
            document.querySelectorAll('.address-radio').forEach(radio => {
                radio.addEventListener('change', function () {
                    if (this.checked) {
                        // Lấy thông tin từ các thuộc tính data-
                        const name = this.getAttribute('data-name');
                        const phone = this.getAttribute('data-phone');
                        const province = this.getAttribute('data-province');
                        const district = this.getAttribute('data-district');
                        const ward = this.getAttribute('data-ward');
                        const detailedAddress = this.getAttribute('data-detailed');

                        // Cập nhật form với dữ liệu địa chỉ đã chọn
                        document.getElementById('ten_khach_hang').value = name;
                        document.getElementById('so_dien_thoai').value = phone;
                        document.getElementById('province').value = province;
                        document.getElementById('detailed_address').value = detailedAddress;

                        // Tải lại quận/huyện và phường/xã với thông tin tỉnh đã chọn,
                        // đồng thời chọn lại quận và phường dựa trên dữ liệu đã lưu
                        loadDistricts(
                            province,
                            document.getElementById('district'),
                            document.getElementById('ward'),
                            district,
                            ward
                        );
                        // Thêm delay update để đảm bảo API load xong dữ liệu
                        setTimeout(() => {
                            updateFullAddress();
                        }, 3000); // Delay 1500ms, bạn có thể điều chỉnh nếu cần
                    }
                });
            });

            // Hàm hợp nhất các trường địa chỉ và cập nhật input ẩn
            function updateFullAddress() {
                const provinceSelect = document.getElementById('province');
                const districtSelect = document.getElementById('district');
                const wardSelect = document.getElementById('ward');
                const detailedAddress = document.getElementById('detailed_address').value.trim();

                const provinceText = provinceSelect.selectedOptions[0] ? provinceSelect.selectedOptions[0].text : '';
                const districtText = districtSelect.selectedOptions[0] ? districtSelect.selectedOptions[0].text : '';
                const wardText = wardSelect.selectedOptions[0] ? wardSelect.selectedOptions[0].text : '';

                let fullAddress = detailedAddress;
                if (wardText && wardText !== 'Chọn Phường/Xã') {
                    fullAddress += ', ' + wardText;
                }
                if (districtText && districtText !== 'Chọn Quận/Huyện') {
                    fullAddress += ', ' + districtText;
                }
                if (provinceText && provinceText !== 'Chọn Tỉnh/Thành') {
                    fullAddress += ', ' + provinceText;
                }

                document.getElementById('dia_chi').value = fullAddress;
            }
        });
    </script>

@endsection
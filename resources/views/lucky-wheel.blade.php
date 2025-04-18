@extends('layouts.app')

@section('content')
<style>
    /* CSS cơ bản cho vòng quay */
    .hoverImage {
        opacity: 0.6;
        transition: opacity 0.3s ease;
    }
    .hoverImage:hover {
        opacity: 1;
    }
    .wheel_style {
        transition: transform 5s ease-out;
    }
    .spin-active {
        opacity: 1 !important;
        pointer-events: none;
    }
    /* CSS cho danh sách quà tặng */
    .prize-list-container {
        max-height: 400px;        /* Giới hạn chiều cao */
        overflow-y: auto;         /* Cho phép cuộn dọc */
        background-color: #f9f9f9;
    }
    .prize-item {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #eee;
        padding: 10px 0;
    }
    .prize-item:last-child {
        border-bottom: none;
    }
    .prize-icon {
        width: 40px;
        height: 40px;
        margin-right: 10px;
        object-fit: contain;
    }
    .prize-details {
        flex: 1;
    }
    /* CSS hiển thị số lượt quay còn lại */
    #remainingSpinsDisplay {
        font-size: 1.2rem;
        font-weight: bold;
        text-align: right;
        margin-bottom: 10px;
    }
</style>

<div class="container">
    <style>
        .marquee-container {
          width: 100%;
          overflow: hidden;
          background: #f1f1f1;
          padding: 10px 0;
          display: flex;
          justify-content: center;
          align-items: center;
        }
        .marquee {
          white-space: nowrap;
          animation: marquee 15s linear infinite;
          font-size: 1.1rem;
          color: #333;
        }
        @keyframes marquee {
          0% { transform: translateX(100%); }
          100% { transform: translateX(-100%); }
        }
      </style>
      
      <div class="marquee-container">
        <div class="marquee">Hoàn thành đơn hàng trị giá >200k sẽ được tặng 1 lượt quay</div>
      </div>
      
    <!-- Hiển thị số lượt quay còn lại nếu người dùng đã đăng nhập -->
    @if(Auth::check())
        <div id="remainingSpinsDisplay" class="text-start mt-2">
            Số lượt quay còn lại: {{ Auth::user()->remaining_spins }}
        </div>
    @endif

    <div class="row">
        <!-- Cột bên trái: Vòng quay -->
        <div class="col-md-8 mt-2">
            <div style="position: relative; width: 500px; height: auto; margin-left: 23%;">
                <img src="{{ asset('images/vqmm3.png') }}" class="wheel_style" alt="vqmm1" style="width: 100%; height: 100%; transform: rotate(23deg);">
                <a style="cursor: pointer;">
                    <!-- Thêm id cho nút quay -->
                    <img onclick="return Spin_wheel()" class="hoverImage" id="spinButton" src="{{ asset('images/quay.png') }}" alt="quay" style="position: absolute; top: 48.6%; left: 50%; transform: translate(-50%, -50%); height: 110px; width: auto;">
                </a>
            </div>
        </div>
        <!-- Cột bên phải: Danh sách quà tặng -->
        <div class="col-md-4 mt-2">
            <h4>Danh sách quà tặng</h4>
            <div id="prizeList" class="prize-list-container">
                @if(Auth::check() && optional(Auth::user()->spins)->count() > 0)
                    @foreach(Auth::user()->spins as $spin)
                        <div class="prize-item">
                            <img src="{{ asset('images/icon/hopqua.png') }}" alt="Icon" class="prize-icon">
                            <div class="prize-details">
                                <div>{{ $spin->prize }}</div>
                                <div><small>{{ $spin->created_at->format('d/m/Y H:i') }}</small></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>Chưa có lịch sử quà tặng nào.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Lấy số lượt quay còn lại từ server (nếu người dùng đã đăng nhập)
    var remainingSpins = {{ Auth::check() ? Auth::user()->remaining_spins : 0 }};

    // Hàm trộn mảng ngẫu nhiên
    function shuffle(array) {
        var currentIndex = array.length, randomIndex;
        while (0 !== currentIndex) {
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex--;
            [array[currentIndex], array[randomIndex]] = [
                array[randomIndex],
                array[currentIndex],
            ];
        }
        return array;
    }

    // Hàm tạo mã voucher ngẫu nhiên (8 ký tự)
    function generateVoucherCode(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    // Hàm cập nhật danh sách quà tặng bên phải (thêm mới vào đầu danh sách)
    function updatePrizeList(newPrize) {
        let prizeList = document.getElementById("prizeList");
        let itemDiv = document.createElement("div");
        itemDiv.className = "prize-item";

        let icon = document.createElement("img");
        icon.src = "{{ asset('images/icon/hopqua.png') }}";
        icon.alt = "Icon";
        icon.className = "prize-icon";

        let detailsDiv = document.createElement("div");
        detailsDiv.className = "prize-details";

        let prizeText = document.createElement("div");
        prizeText.innerText = newPrize;

        let timeText = document.createElement("div");
        let now = new Date();
        timeText.innerHTML = '<small>' + now.toLocaleDateString() + ' ' + now.toLocaleTimeString() + '</small>';

        detailsDiv.appendChild(prizeText);
        detailsDiv.appendChild(timeText);
        itemDiv.appendChild(icon);
        itemDiv.appendChild(detailsDiv);

        // Thêm item mới vào đầu danh sách
        prizeList.insertBefore(itemDiv, prizeList.firstChild);
    }

    // Hàm cập nhật hiển thị số lượt quay còn lại
    function updateRemainingSpinsDisplay() {
        document.getElementById("remainingSpinsDisplay").innerText = "Số lượt quay còn lại: " + remainingSpins;
    }

    function Spin_wheel() {
        // Kiểm tra nếu không còn lượt quay
        if (remainingSpins <= 0) {
            Swal.fire("Thông báo", "Bạn không còn lượt quay!", "warning");
            return;
        }

        // Giảm lượt quay ở phía client và cập nhật giao diện
        remainingSpins--;
        updateRemainingSpinsDisplay();

        const baseRotation = 23; // Offset ban đầu của vòng quay
        const path_wheel = "{{ URL::asset('/wheel/wheel.mp3') }}";
        const path_applause = "{{ URL::asset('/wheel/applause.mp3') }}";
        const wheel_music = new Audio(path_wheel);
        const wheel_applause = new Audio(path_applause);
        wheel_music.play();

        const wheel = document.querySelector('.wheel_style');
        const spinButton = document.getElementById("spinButton");
        let SelectedItem = "";

        // Vô hiệu hóa nút quay
        spinButton.classList.add("spin-active");

        // Các giá trị quay (đơn vị: độ)
        let airpod = shuffle([2115, 2475, 2835]);
        let chucbanmayman2 = shuffle([2070, 2430, 2790]);
        let vou70 = shuffle([2025, 2385, 2745]);  
        let ip11pm = shuffle([1980, 2340, 2700]);
        let vou150 = shuffle([1935, 2295, 2655]);
        let chucbanmayman = shuffle([1890, 2250, 2610]);
        let vou100 = shuffle([1845, 2205, 2565]);
        let vou50 = shuffle([1800, 2160, 2520]);

        let Hasil = shuffle([
            // airpod[0],
            chucbanmayman2[0],
            vou70[0],
            // ip11pm[0],
            vou150[0],
            vou100[0],
            vou50[0],
            chucbanmayman[0],
        ]);
        console.log("Góc quay lựa chọn: " + Hasil[0]);

        if (airpod.includes(Hasil[0])) SelectedItem = "AirPods 2 mô hình";
        if (chucbanmayman2.includes(Hasil[0])) SelectedItem = "Chúc bạn may mắn 2";
        if (vou70.includes(Hasil[0])) SelectedItem = "Voucher 70k";
        if (ip11pm.includes(Hasil[0])) SelectedItem = "iPhone 11 Pro Max mô hình";
        if (vou150.includes(Hasil[0])) SelectedItem = "Voucher 150k";
        if (chucbanmayman.includes(Hasil[0])) SelectedItem = "Chúc bạn may mắn";
        if (vou100.includes(Hasil[0])) SelectedItem = "Voucher 100k";
        if (vou50.includes(Hasil[0])) SelectedItem = "Voucher 50k";

        // Quay vòng quay, cộng thêm offset baseRotation
        wheel.style.setProperty("transition", "all ease 5s");
        wheel.style.transform = `rotate(${baseRotation + Hasil[0]}deg)`;

        setTimeout(function () {
            wheel_applause.play();

            // Nếu kết quả là voucher, tạo mã voucher và gọi endpoint voucher.store
            if (SelectedItem.includes("Voucher")) {
                const voucherCode = generateVoucherCode(8);
                const discountAmount = parseInt(SelectedItem.match(/\d+/)[0]) * 1000;
                fetch("{{ route('voucher.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        code: voucherCode,
                        discount: discountAmount,
                        type: "fixed",
                        max_usage: 1,
                        start_date: null,
                        end_date: null
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            "Chúc mừng bạn",
                            "đã trúng " + SelectedItem + ".\n" +
                            "Mã voucher: " + voucherCode + "\n" +
                            "Số lượt sử dụng: 1" + "\n" +
                            "Có thể vào Profile để xem lại",
                            "success"
                        );
                    } else {
                        Swal.fire("Lỗi", "Lỗi khi lưu voucher", "error");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire("Lỗi", "Lỗi khi lưu voucher", "error");
                });
            } else {
                Swal.fire(
                    "Chúc mừng bạn",
                    "đã trúng " + SelectedItem + ".",
                    "success"
                );
            }

            // Gọi endpoint spin.store để lưu lịch sử lượt quay
            fetch("{{ route('spin.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ result: SelectedItem })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Spin result saved", data);
                // Cập nhật danh sách quà tặng bên phải sau khi quay
                updatePrizeList(SelectedItem);
            })
            .catch(error => {
                console.error("Error saving spin result:", error);
            });
        }, 5500);

        setTimeout(function () {
            wheel_music.pause();
            wheel.style.setProperty("transition", "initial");
            wheel.style.transform = `rotate(${baseRotation}deg)`;
            spinButton.classList.remove("spin-active");
        }, 6000);
    }
</script>
@endsection

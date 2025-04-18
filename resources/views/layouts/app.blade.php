<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đại Nông</title>
     <!-- Meta tags & CSS -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/metismenu/3.0.6/metisMenu.min.css">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
      .group-title {
    font-weight: bold;
    margin-top: 10px;
    margin-bottom: 5px;
    font-size: 14px;
    color: #555;
    border-bottom: 1px solid #eee;
}

  /* Ẩn chat widget ban đầu */
.hidden {
  display: none;
}

/* Chat Widget Container */
#chat-widget {
  width: 320px;
  max-width: 100%;
  border: none;
  border-radius: 8px;
  background: #fff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  position: fixed;
  bottom: 80px;
  right: 20px;
  z-index: 1051;
  font-family: 'Roboto', sans-serif;
  overflow: hidden;
}

/* Header của Chat Widget */
#chat-header {
  background: #28a745; /* Màu xanh lá chủ đạo */
  color: #fff;
  padding: 10px 15px;
  font-size: 16px;
  font-weight: bold;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* Nút đóng trong header */
#chat-header .close-btn {
  background: transparent;
  border: none;
  color: #fff;
  font-size: 18px;
  cursor: pointer;
}

/* Khu vực hiển thị tin nhắn */
#chat-box {
  height: 300px;
  padding: 10px;
  overflow-y: auto;
  background: #f9f9f9;
  display: flex;
  flex-direction: column;
}

/* Chat Bubble chung */
.message {
  margin-bottom: 10px;
  max-width: 80%;
  padding: 8px 12px;
  border-radius: 16px;
  font-size: 14px;
  line-height: 1.4;
  position: relative;
}

/* Tin nhắn của người dùng (của mình) hiển thị bên phải */
.message.user {
  background: #dcf8c6; /* Màu nền xanh lá nhạt */
  align-self: flex-end; /* Căn phải */
  border: 1px solid #b2eabf;
}

/* Tin nhắn của Admin (của họ) hiển thị bên trái */
.message.admin {
  background: #fff;
  border: 1px solid #e1e1e1;
  align-self: flex-start; /* Căn trái */
}

/* Chat Form */
#chat-form {
  display: flex;
  padding: 10px;
  background: #fff;
  border-top: 1px solid #e1e1e1;
}

#message {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 20px;
  outline: none;
  font-size: 14px;
  transition: border-color 0.3s;
}

#message:focus {
  border-color: #28a745;
}

#chat-form button {
  margin-left: 8px;
  padding: 8px 16px;
  border: none;
  background: #28a745;
  color: #fff;
  border-radius: 20px;
  cursor: pointer;
  font-size: 14px;
  transition: background 0.3s;
}

#chat-form button:hover {
  background: #218838;
}

/* Container kết quả live search */
#live-search-results {
    position: absolute;
    top: calc(72%); /* Xuất hiện ngay dưới form, cách một chút */
    left: 46.8%;
    transform: translateX(-50%);
    width: 405px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    max-height: 350px;
    overflow-y: auto;
    padding: 10px;
    opacity: 0; /* Ẩn ban đầu */
    visibility: hidden; /* Ẩn ban đầu */
    transform: translate(-50%, -10px); /* Lùi lên 10px để tạo hiệu ứng trượt xuống */
    transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out, visibility 0.3s;
}

/* Khi có kết quả, hiển thị mượt */
#live-search-results.show {
    opacity: 1;
    visibility: visible;
    transform: translate(-50%, 0);
}

/* Mỗi mục kết quả live search */
.live-search-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
}
.live-search-item:last-child {
    border-bottom: none;
}
.live-search-item {
  transition: all 0.2s ease-in-out;
  border-radius: 6px;
}

.live-search-item:hover {
  background-color: #e9f5ec; /* Màu nền dịu, hơi xanh lá */
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
  transform: translateY(-2px); /* Hiệu ứng nổi nhẹ */
  cursor: pointer;
}

.live-search-item a {
    text-decoration: none;
    color: inherit;
    display: flex;
    width: 100%;
    align-items: center;
}

/* Thumbnail cho sản phẩm */
.live-search-thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
    margin-right: 10px;
    border: 1px solid #ddd;
    transition: transform 0.3s ease;
}
.live-search-item:hover .live-search-thumb {
    transform: scale(1.05);
}

/* Thông tin sản phẩm */
.live-search-info {
    flex: 1;
}
.live-search-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #343a40;
}
.live-search-price {
    margin: 0;
    font-size: 14px;
    color: #28a745;
    font-weight: 500;
}

  
      .order-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    padding: 20px 0;
    border-bottom: 2px solid #eee;
}
.step {
    display: flex;
    align-items: center;
    flex: 1;
    padding: 10px;
    color: inherit; /* Sử dụng màu chữ mặc định */
    text-decoration: none; /* Loại bỏ gạch chân của link */
}
.step-number {
    font-size: 40px;
    font-weight: bold;
    margin-right: 10px;
}
.step-content h4 {
    font-size: 18px;
    font-weight: bold;
    text-transform: uppercase;
    margin: 0;
}
.step-content p {
    font-size: 14px;
    color: #666;
    margin: 0;
}
.active .step-number, .active h4 {
    color: #222;
}
.inactive .step-number, .inactive .step-content h4, .inactive .step-content p {
    color: #ddd;
}
/* Đảm bảo link không thay đổi bố cục */
.step a {
    color: inherit;
    text-decoration: none;
    display: block;
}



.inactive .step-number,
.inactive .step-content h4,
.inactive .step-content p {
    transition: color 0.3s ease;
}

.inactive:hover .step-number,
.inactive:hover .step-content h4,
.inactive:hover .step-content p {
    color: #222;
}

/* Reset underline mặc định và thiết lập màu sắc cho link breadcrumb */
.breadcrumb-item a {
    position: relative;
    text-decoration: none;
  
}

/* Pseudo-element ::after để tạo đường gạch chân ẩn ban đầu */
.breadcrumb-item a::after {
    content: "";
    position: absolute;
    width: 100%;
    height: 1.5px;              /* Độ dày của đường gạch */
    bottom: -2px;             /* Điều chỉnh khoảng cách giữa đường gạch và text */
    left: 0;
    background-color: currentColor; /* Sử dụng cùng màu với text */
    transform: scaleX(0);     /* Ẩn đường gạch theo chiều ngang */
    transform-origin: left;
    transition: transform 0.3s ease-in-out;
}

/* Hiệu ứng khi rê chuột: đường gạch mở rộng (chạy) từ trái sang phải */
.breadcrumb-item a:hover::after {
    transform: scaleX(1);
}


/* Kiểu cho product card xem gần đây */
.product-card-recent {
  padding: 10px;
  border: 1px solid #eee;
  border-radius: 5px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.product-card-recent:hover {
  transform: scale(1.02);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Kiểu cho tiêu đề sản phẩm */
.product-title {
  font-size: 1rem;
  font-weight: bold;
}

/* Kiểu cho khoảng giá sản phẩm */
.product-price {
  color: #d9534f; /* Bạn có thể thay đổi màu theo ý */
  font-size: 1rem;
}


/* Vị trí cố định cho nút floating */
#floating-buttons {
  position: fixed;
  bottom: 20px;
  right: 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  z-index: 1050;
}

/* Kiểu chung cho cả 2 nút tròn */
.round-btn {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: #fff;
  color: #000;
  border: 0; /* Đặt viền là 0px */
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2); /* Đổ bóng cho nút */
  display: flex;
  justify-content: center;
  align-items: center;
  text-decoration: none;
  font-size: 20px;
  transition: opacity 0.5s ease-in-out, transform 0.3s ease;
}

/* Nút Back to top ẩn ban đầu */
#back-to-top {
  opacity: 0;
  visibility: hidden;
}

/* Khi thêm class "show", nút Back to top xuất hiện */
#back-to-top.show {
  opacity: 1;
  visibility: visible;
}

/* Nút luôn hiển thị (cho nút xem lịch sử) */
.always-visible {
  opacity: 1 !important;
  visibility: visible !important;
}

/* Hiệu ứng hover: phóng to nhẹ */
.round-btn:hover {
  transform: scale(1.1);
}


/* Dành cho Chrome, Edge (Chromium), Safari */
::-webkit-scrollbar {
  width: 6px;   /* Đặt độ rộng thanh cuộn dọc */
  height: 6px;  /* Đặt chiều cao thanh cuộn ngang */
}

::-webkit-scrollbar-track {
  background: #e0e0e0;  /* Màu nền của track, có thể thay đổi nếu cần */
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background-color: #00a651;  /* Màu xanh lá cho phần thumb */
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background-color: #008f43;  /* Màu xanh lá đậm hơn khi hover */
}

/* Dành cho Firefox */
* {
  scrollbar-width: thin;  /* Thanh cuộn mỏng */
  scrollbar-color: #00a651 #e0e0e0;  /* Màu thumb và track */
}

/* Cải thiện giao diện toast */
.toast-container {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 1050;
}

.toast {
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    opacity: 0; /* Giữ toast ẩn ban đầu */
    animation: toast-slide-in 0.5s ease-out forwards; /* Hiệu ứng slide-in khi xuất hiện */
}

.toast-body {
    padding: 15px;
    font-size: 14px;
}

.toast-header {
    border-bottom: none;
    padding-bottom: 10px;
}

/* Hiệu ứng slide-in */
@keyframes toast-slide-in {
    from {
        opacity: 0;
        transform: translateY(-20px); /* Di chuyển từ trên xuống */
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Thông báo thành công */
.toast.bg-success {
    background-color: #28a745;
    border-left: 5px solid #218838; /* Thêm viền bên trái màu xanh lá */
}

/* Thông báo lỗi */
.toast.bg-danger {
    background-color: #dc3545;
    border-left: 5px solid #c82333; /* Thêm viền bên trái màu đỏ */
}

/* Thông báo thông tin */
.toast.bg-info {
    background-color: #17a2b8;
    border-left: 5px solid #117a8b;
}

/* Thông báo cảnh báo */
.toast.bg-warning {
    background-color: #ffc107;
    border-left: 5px solid #e0a800;
}

/* Chỉnh sửa nút đóng */
.toast .btn-close {
    color: white;
    font-size: 20px;
    opacity: 0.7;
    padding: 10px;
}

.toast .btn-close:hover {
    opacity: 1;
}

/* Đảm bảo toast sẽ hiển thị đầy đủ */
.toast.show {
    opacity: 1;
}

        /* Styles for the first navbar */
        .navbar-first {
            background: rgba(24, 186, 21, 1.0);
            background: linear-gradient(135deg, rgba(24, 186, 21, 1.0), rgba(65, 206, 127, 1.0));
            padding: 0.5rem 1rem;
        }

        .navbar-first .nav-link {
            color: white !important;
            font-weight: bold;
            font-size: 1.1rem;
            padding: 0.8rem 1.2rem;
        }

        .navbar-first .nav-link:hover {
            color: #FFD700;
            text-decoration: underline;
            transform: scale(1.1);
            transition: all 0.3s ease;
        }

        .btn-outline-primary, .btn-outline-secondary {
            color: white;
            border-color: white;
            transition: background-color 0.3s, border-color 0.3s;

        }

        .btn-outline-primary:hover, .btn-outline-secondary:hover {
            background-color: #FFD700;
            border-color: #FFD700;
            color: #000;
            transform: scale(1.05);
        }

        .navbar-brand {
            font-weight: bold;
            color: white;
            font-size: 1.5rem;
            transition: color 0.3s, transform 0.3s;
        }

        .navbar-brand:hover {
            color: #FFD700;
            transform: scale(1.1);
        }

    .navbar-brand img { 
        margin-left: 50px;
        height: 80px;
        width: auto; /* Giữ tỷ lệ của logo */ 
    }

        .search-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .search-bar input {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .search-bar button {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

  /* Border cho Navbar dòng 2 */
  .navbar-second {
    background-color: white;
    color: black;
    border-bottom: 2px solid #ddd;
    border-top: 2px solid #ddd;
    padding: 10px;
  }

  /* Gạch chân cho mục */
  .navbar-second .nav-link {
    color: black !important;
    position: relative;
    font-weight: bold;
    margin: 0 15px; /* Thêm khoảng cách ngang giữa các mục */
    transition: color 0.3s ease;
  }

  .navbar-second .nav-link.active, 
  .navbar-second .nav-link:hover {
    color: #85B223 !important; /* Đổi màu chữ khi chọn hoặc hover */
  }

  /* Gạch chân chạy */
  .navbar-second .nav-link::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: -5px;
    height: 2px;
    background-color: #85B223;
    width: 0%; /* Gạch chân mặc định là 0 */
    transition: width 0.3s ease; /* Hiệu ứng chạy */
  }

  /* Khi hover hoặc active */
  .navbar-second .nav-link.active::after, 
  .navbar-second .nav-link:hover::after {
    width: 100%; /* Gạch chân chạy toàn phần */
  }   

  /* Căn chỉnh cơ bản cho dropdown */
  .navbar-second .nav-item .metismenu {
    display: none;
    position: absolute;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 10px;
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border: 2px solid #85B223; /* Thêm viền màu xanh */
    border-top: none; /* Bỏ viền trên */
    z-index: 1000;
    min-width: 240px; /* Đảm bảo độ rộng tối thiểu */
    transition: opacity 0.3s ease, transform 0.3s ease;
    opacity: 0; /* Ẩn hoàn toàn lúc đầu */
    transform: translateY(10px); /* Hiệu ứng trượt xuống */
    list-style: none; /* Xóa dấu chấm đầu dòng */
  }

  /* Hiển thị dropdown khi hover */
  .navbar-second .nav-item:hover > .metismenu {
    display: block;
    opacity: 1; /* Hiển thị hoàn toàn */
    transform: translateY(0); /* Đưa về vị trí ban đầu */
  }

  /* Style từng mục con trong dropdown */
  .navbar-second .nav-item .metismenu li {
    list-style: none; /* Xóa dấu chấm đầu dòng */
    margin-bottom: 5px; /* Thêm khoảng cách giữa các mục */
    position: relative; /* Đảm bảo submenu nằm bên cạnh */
  }

  .navbar-second .nav-item .metismenu li a {
    display: block;
    color: #333;
    padding: 5px 10px;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease, color 0.3s ease;
  }

  /* Hiệu ứng hover cho mục con */
  .navbar-second .nav-item .metismenu li a:hover {
    background-color: #85B223;
    color: #fff;
  }

  /* Submenu cấp con */
  .navbar-second .nav-item .metismenu .submenu {
    display: none;
    position: absolute;
    left: 100%;             /* Bắt đầu ngay cạnh bên phải phần cha */
    border: 2px solid #85B223; /* Thêm viền màu xanh */
    border-left: none; 
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 10px;
    border-bottom-left-radius: 0px;
    border-bottom-right-radius: 5px;
    border-top-left-radius: 0px;
    border-top-right-radius: 5px;
    transition: opacity 0.3s ease, transform 0.3s ease;
    opacity: 0;
    transform: translateX(-5%); 
    min-width: 200px;
  }

  /* Hiển thị submenu cấp con khi hover */
  .navbar-second .nav-item .metismenu li:hover > .submenu {
    display: block;
    opacity: 1;
    transform: translateX(0);
  }

  /* Thêm hiệu ứng cho mũi tên */
  .nav-item .nav-link .muiten  {
    margin-left: 5px;
    transition: transform 0.3s ease;
  }

  .nav-item:hover > .nav-link .muiten {
    transform: rotate(180deg); /* Xoay mũi tên khi hover */
  }
  .modal-body {
    max-height: auto; /* Điều chỉnh chiều cao tối đa tùy ý */
    overflow-y: auto;  /* Bật thanh cuộn dọc nếu nội dung vượt quá chiều cao này */
  }
    </style>
</head>
<body>
<!-- Floating Buttons Container -->
<div id="floating-buttons">
  <!-- Nút "Back to top" -->
  <button type="button" class="round-btn" id="back-to-top" title="Back to top">
    <i class="fas fa-arrow-up"></i>
  </button>

  <!-- Nút "Sản phẩm xem gần đây" -->
  <a href="#" class="round-btn always-visible" id="recent-btn" title="Sản phẩm xem gần đây" data-bs-toggle="modal" data-bs-target="#recentlyViewedModal">
    <i class="fas fa-history"></i>
  </a>

  <!-- Nút "Chat với Admin" -->
  @auth
    <button type="button" class="round-btn" id="chat-with-admin-btn" title="Chat với Admin">
      <i class="fas fa-comments"></i>
    </button>
  @else
    <button type="button" class="round-btn" id="chat-with-admin-btn" title="Chat với Admin" onclick="requireLogin1()">
      <i class="fas fa-comments"></i>
    </button>
  @endauth
</div>

@auth
  <!-- Chat Widget -->
  <div id="chat-widget" class="hidden">
    <div id="chat-header">
      Chat với Admin
      <button class="close-btn" onclick="toggleChatWidget()">&times;</button>
    </div>
    <div id="chat-box"></div>
    <form id="chat-form">
      <input type="text" id="message" placeholder="Nhập tin nhắn..." autocomplete="off" required>
      <button type="submit">Gửi</button>
    </form>
  </div>
@endauth

<!-- Hàm requireLogin() để yêu cầu đăng nhập khi người dùng chưa đăng nhập -->
<script>
  function toggleChatWidget() {
  const chatWidget = document.getElementById('chat-widget');
  chatWidget.classList.toggle('hidden');
}

    function requireLogin1() {
        Swal.fire({
            icon: 'warning',
            title: 'Bạn chưa đăng nhập!',
            text: 'Vui lòng đăng nhập để chat với Admin.',
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


  
  <!-- Modal Sản phẩm xem gần đây (hiệu ứng right fade) -->
  <div class="modal right fade" id="recentlyViewedModal" tabindex="-1" aria-labelledby="recentlyViewedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="recentlyViewedModalLabel">Sản phẩm xem gần đây</h5>
          <!-- Nút đóng sử dụng cú pháp Bootstrap 5 -->
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">
          @if(session()->has('recently_viewed') && count(session('recently_viewed')) > 0)
            <div class="row">
              @php
                // Lấy danh sách ID sản phẩm đã xem từ session
                $recentIds = session('recently_viewed');
                // Truy vấn sản phẩm theo danh sách ID và sắp xếp theo thứ tự xem (mới nhất đứng đầu)
                $recentProducts = \App\Models\Product::whereIn('id', $recentIds)
                                    ->orderByRaw("FIELD(id, " . implode(',', $recentIds) . ")")
                                    ->get();
              @endphp
  
  @foreach($recentProducts as $product)
  <div class="mb-3">
    <!-- Bọc toàn bộ thẻ trong <a> để khi bấm vào chuyển sang trang chi tiết sản phẩm -->
    <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none text-reset">
      <div class="product-card-recent shadow-sm position-relative">
        <!-- Badge "Recent" hiển thị ở góc phải trên của card -->
        <span class="badge bg-primary position-absolute" style="top: 10px; right: 10px;">Recent</span>
        <div class="row g-0 align-items-center">
          <!-- Cột chứa ảnh sản phẩm với kích thước 100x100 -->
          <div class="col-auto">
            @if($product->image)
              <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover;">
            @else
              <img src="https://via.placeholder.com/100x100?text=No+Image" alt="No Image" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover;">
            @endif
          </div>
          <!-- Cột chứa thông tin sản phẩm: tên và khoảng giá -->
          <div class="col">
            <div class="py-2">
              <h5 class="product-title mb-1 ms-2">{{ $product->product_name }}</h5>
              @php
              // Nếu có giá giảm và giá giảm khác 0 thì lấy, nếu không thì lấy giá gốc
              $price_min = ($product->discount_price && $product->discount_price > 0) ? $product->discount_price : $product->price;
              
              // Nếu sản phẩm có đơn vị tính là kg, thì tính khoảng giá
              if(optional($product->unit)->unit_name === 'kg'){
                  $price_max = $product->price;
                  $price_min = ($product->discount_price && $product->discount_price > 0) ? $product->discount_price / 4 : $product->price / 4;
              } else {
                  $price_max = $product->price;
              }
          @endphp
          
          <p class="product-price mb-0 ms-2">
              <strong>
                  {{ number_format($price_min, 0, ',', '.') }}đ - {{ number_format($price_max, 0, ',', '.') }}đ
              </strong>
          </p>
          
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>
@endforeach
            </div>
          @else
            <p>Chưa có sản phẩm nào được xem gần đây.</p>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript: Hiển thị nút Back to top khi cuộn và xử lý sự kiện click -->
  <script>
    window.addEventListener('scroll', function() {
      const backToTopButton = document.getElementById('back-to-top');
      // Nếu cuộn xuống hơn 200px, thêm class 'show' vào nút Back to top
      if (window.scrollY > 200) {
        backToTopButton.classList.add('show');
      } else {
        backToTopButton.classList.remove('show');
      }
    });

    document.getElementById('back-to-top').addEventListener('click', function() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  </script>
<!-- Navbar dòng 1 -->
<nav class="navbar navbar-expand-lg navbar-first">
    <div class="container d-flex align-items-center" style="flex-wrap: nowrap;">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="/images/logo/logofn.png" alt="Logo">
        </a>


  <!-- Form tìm kiếm  -->
  <form method="GET" action="{{ route('products.search') }}" 
  class="d-flex justify-content-center position-absolute top-50 start-50 translate-middle w-100"
  style="max-width: 500px;">
<input class="form-control me-2" 
       type="search" 
       name="query" 
       id="live-search-input"
       placeholder="Tìm kiếm sản phẩm" 
       aria-label="Search"
       style="max-width: 400px; min-width: 200px;">
<button class="btn btn-outline-light" type="submit">Tìm kiếm</button>
</form>

<!-- Container hiển thị kết quả live search -->
<div id="live-search-results"></div>


<div class="d-flex align-items-center">
  @if(auth()->check())
  <div class="dropdown">
    <button class="btn btn-outline-light dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      <img src="{{ asset(auth()->user()->avatar ? auth()->user()->avatar : 'images/avatars/avtdf.jpg') }}" alt="Avatar" class="rounded-circle" style="width: 30px; height: 30px;">
      <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
  </button>
  
    <ul class="dropdown-menu dropdown-menu-end shadow">
      <li>
        <a class="dropdown-item" href="{{ route('profile.index') }}">
       Thông tin cá nhân
        </a>
      </li>
      <li>
        <a class="dropdown-item" href="{{ route('orders.index') }}">
        Đơn hàng của tôi
        </a>
      </li>
      <li>
        <hr class="dropdown-divider">
      </li>
      <li>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="dropdown-item">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất
          </button>
        </form>
      </li>
    </ul>
  </div>
  <style>
    .dropdown-item:hover, .dropdown-item:focus, .dropdown-item.active {
        background-color: #28a745 !important; /* Màu xanh lá */
        color: white !important;
    }
</style>

@else
  <div class="d-flex justify-content-end">
    <a href="{{ route('login') }}" class="btn btn-outline-light shadow-sm">
      <i class="fa-solid fa-sign-in-alt me-2"></i> Đăng nhập
    </a>
  </div>
@endif
  @php
  if(auth()->check()) {
      $totalItems = \App\Models\Cart::where('user_id', auth()->id())->count();
  } else {
      $cart = session()->get('cart', []);
      $totalItems = count($cart);
  }
@endphp

<!-- Nút kích hoạt modal giỏ hàng -->
<button type="button" class="btn btn-outline-light ms-3" data-bs-toggle="modal" data-bs-target="#cartModal" title="Giỏ hàng">
  <i class="fas fa-shopping-cart"></i> 
  <span id="cartItemCount" class="badge bg-danger">{{ $totalItems }}</span>
</button>
</div> 
    </div>
</nav>

<!-- Navbar dòng 2 -->
<nav class="navbar navbar-expand-lg navbar-second">
    <div class="container">
        <ul class="navbar-nav " id="menu">
            <!-- Danh mục sản phẩm với dropdown -->
            <li class="nav-item position-relative">
                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
               
<i class="fa-solid fa-list me-2"></i>Danh mục sản phẩm <i class="fas fa-chevron-down muiten ms-2"></i>

                </a>
                <ul class="metismenu">
                    @foreach($categories as $category)
                        <li class="d-flex align-items-center position-relative">
                            <a href="{{ route('categories.show', $category->id) }}" class="flex-grow-1 d-flex justify-content-between align-items-center">
                                {{ $category->category_name }}
                                <!-- Kiểm tra nếu là danh mục cha, hiển thị danh mục con -->
                                @if($category->hasSubCategories())
                                    <i class="fas fa-chevron-right ms-2"></i> <!-- Mũi tên chỉ sang phải nếu có danh mục con -->
                                @endif
                            </a>
                            @if($category->hasSubCategories())
                                <ul class="submenu">
                                    @foreach($category->subCategories as $subCategory)
                                        <li><a href="{{ route('categories.show', $subCategory->id) }}">{{ $subCategory->category_name }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                        @if(!$loop->last) <!-- Thêm gạch ngang giữa các danh mục -->
                            <hr class="my-2 w-100">
                        @endif
                    @endforeach
                </ul>
            </li>
           <!-- Menu "Sản phẩm" -->
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
       Sản phẩm
    </a>
</li>

<!-- Menu "Tin tức" -->
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}" href="{{ route('news.index') }}">
        Tin tức
    </a>
</li>
<!-- Menu "Liên hệ" -->
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('address.*') ? 'active' : '' }}" href="{{ route('address.index') }}">
       Liên hệ 
    </a>
</li>

<li class="nav-item">
  <a class="nav-link {{ request()->routeIs('luckywheel.*') ? 'active' : '' }}" href="{{ route('luckywheel.index') }}">
   Vòng quay
  </a>
</li>
        </ul>
    </div>
</nav>

    @if(session('success'))
    <div class="toast-container">
        <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    @if(session('status'))
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div class="toast align-items-center text-white bg-dark border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('status') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endif

    @if(session('error'))
    <div class="toast-container">
        <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">{{ session('error') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif
    
    <main>
        @yield('content')
    </main>
    @include('cart._cart_modal')

    <!-- Đường gạch ngăn cách giữa nội dung và dòng chữ cuối -->
    <hr class="border-top border-secondary">
  <!-- Footer -->
  <style>
    /* Hiệu ứng hover cho các link trong footer */
    footer a:hover {
      color: #28a745 !important;
    }
  </style>
  
  <footer class="bg-white text-dark pt-5">
    <div class="container">
      <div class="row">
        <!-- Cột Giới thiệu -->
        <div class="col-md-3 mb-4">
          <h5 class="text-uppercase">Giới thiệu</h5>
          <p>Đại Nông là nền tảng thương mại điện tử hàng đầu chuyên cung cấp nông sản sạch, an toàn và đảm bảo chất lượng.</p>
        </div>
        <!-- Cột Thông tin -->
        <div class="col-md-3 mb-4">
          <h5 class="text-uppercase">Thông tin</h5>
          <ul class="list-unstyled">
            <li><a href="#" class="text-dark text-decoration-none">Giới thiệu</a></li>
            <li><a href="{{ route('news.index') }}" class="text-dark text-decoration-none">Tin tức</a></li>
            <li><a href="{{ route('address.index') }}" class="text-dark text-decoration-none">Liên hệ</a></li>
            <li><a href="#" class="text-dark text-decoration-none">Tuyển dụng</a></li>
          </ul>
        </div>
        <!-- Cột Hỗ trợ khách hàng -->
        <div class="col-md-3 mb-4">
          <h5 class="text-uppercase">Hỗ trợ khách hàng</h5>
          <ul class="list-unstyled">
            <li><a href="#" class="text-dark text-decoration-none">Hướng dẫn mua hàng</a></li>
            <li><a href="#" class="text-dark text-decoration-none">Chính sách đổi trả</a></li>
            <li><a href="#" class="text-dark text-decoration-none">Chính sách bảo mật</a></li>
            <li><a href="#" class="text-dark text-decoration-none">Điều khoản sử dụng</a></li>
          </ul>
        </div>
        <!-- Cột Liên hệ -->
        <div class="col-md-3 mb-4">
          <h5 class="text-uppercase">Liên hệ</h5>
          <ul class="list-unstyled">
            <li><i class="fas fa-map-marker-alt me-2"></i>41A Đ. Phú Diễn, Phú Diễn, Bắc Từ Liêm, Hà Nội</li>
            <li><i class="fas fa-phone me-2"></i>0123 456 789</li>
            <li><i class="fas fa-envelope me-2"></i>donhotu03.dev@gmail.com</li>
          </ul>
          <div class="mt-3">
            <a href="https://www.facebook.com/topkiin.Tu" class="text-dark me-2"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-dark me-2"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-dark me-2"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-dark me-2"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
      </div>
      <!-- Đường gạch ngăn cách giữa nội dung và dòng chữ cuối -->
      <hr class="border-top border-secondary">
      <div class="row">
        <div class="col text-center pb-3">
          <p class="mb-0">&copy; 2025 Đại Nông. All rights reserved.</p>
        </div>
      </div>
    </div>
  </footer>
  <!-- End Footer -->
  
  
    
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/metismenu/3.0.6/metisMenu.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset("js/toast.js") }}"></script>
   

    <script>
      var routeUrl = "{{ route('live-search') }}";
    </script>
    <script src="{{ asset('js/live-search.js') }}"></script>
    
  <script>
    document.addEventListener("DOMContentLoaded", function () {
    var cartModal = document.getElementById('cartModal');

    if (cartModal) {
        cartModal.addEventListener('shown.bs.modal', function () {
            document.body.style.paddingRight = '0px';
        });

        cartModal.addEventListener('hidden.bs.modal', function () {
            document.body.style.paddingRight = '0px';
        });
    }
});

  </script>
<script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
      // Lấy thông tin người dùng từ Blade
      const userId = parseInt("{{ Auth::id() }}") || null;
      const userName = "{{ Auth::check() ? Auth::user()->name : 'Guest' }}";
      
      // Admin ID mặc định
      const adminId = 5;
      
      // Tính toán roomId dựa trên ID user và admin
      const roomId = userId ? `chat_${[userId, adminId].sort().join('_')}` : 'default_room';
  
      console.log("User ID:", userId);
      console.log("Room ID:", roomId);
  
      const chatWidget = document.getElementById('chat-widget');
      const chatButton = document.getElementById('chat-with-admin-btn');
      const chatBox = document.getElementById('chat-box');
      const notificationBadge = document.createElement('span');
  
      // Thêm badge thông báo vào nút chat
      notificationBadge.id = "chat-notification";
      notificationBadge.style.cssText = `
          position: absolute; top: 0; right: 0;
          background: red; color: white; 
          width: 15px; height: 15px; 
          border-radius: 50%; font-size: 12px;
          display: none; justify-content: center; align-items: center;
      `;
      chatButton.style.position = "relative";
      chatButton.appendChild(notificationBadge);
  
      let unreadMessages = 0;
  
      // Xử lý mở/đóng chat widget
      chatButton.addEventListener('click', function(event) {
          chatWidget.classList.toggle('hidden');
  
          // Nếu mở chat, reset thông báo tin nhắn
          if (!chatWidget.classList.contains('hidden')) {
              unreadMessages = 0;
              notificationBadge.style.display = 'none';
          }
  
          event.stopPropagation();
      });
  
      // Ẩn chat widget khi click ra ngoài
      document.addEventListener('click', function(event) {
          if (!chatWidget.contains(event.target) && event.target !== chatButton) {
              chatWidget.classList.add('hidden');
          }
      });
  
      // Hàm tải tin nhắn cũ từ API
      function loadOldMessages() {
          fetch(`/chat/messages?room_id=${encodeURIComponent(roomId)}`)
              .then(response => response.json())
              .then(data => {
                  if (data.status === 'success' && Array.isArray(data.messages)) {
                      data.messages.forEach(msg => appendMessage(msg, false));
                  }
              })
              .catch(error => console.error('Lỗi tải tin nhắn:', error));
      }
  
      // Hàm lấy tên người dùng nếu chưa có
      function fetchUserName(userId) {
          return fetch(`/chat/user/${userId}`)
              .then(response => response.json())
              .then(data => data.name || "Người dùng")
              .catch(() => "Người dùng");
      }
  
      // Hàm hiển thị tin nhắn và tự động cuộn xuống
      async function appendMessage(data, isNew = false) {
          const div = document.createElement('div');
          div.classList.add('message');
  
          let sender = data.sender || data.user;
          if (data.sender === "admin") sender = "Admin";
          if (!sender && data.user_id) sender = await fetchUserName(data.user_id);
  
          div.classList.add(sender === "Admin" ? 'admin' : 'user');
          div.innerHTML = `<strong>${sender}:</strong> ${data.message}`;
          chatBox.appendChild(div);
  
          // Nếu chat đang mở, cuộn xuống tin nhắn mới nhất
          if (!chatWidget.classList.contains('hidden')) {
              chatBox.scrollTop = chatBox.scrollHeight;
          } else if (isNew) {
              // Nếu chat đang đóng, hiển thị thông báo tin nhắn mới
              unreadMessages++;
              notificationBadge.innerText = unreadMessages;
              notificationBadge.style.display = 'flex';
          }
      }
  
      // Kết nối đến Socket.IO
      const socket = io('http://localhost:3000');
  
      socket.on('connect', () => {
          socket.emit('joinRoom', roomId);
          console.log('Đã tham gia phòng:', roomId);
      });
  
      // Nhận tin nhắn từ server
      socket.on('message', (payload) => {
          let messageData = payload.data?.data || payload.data || payload;
          if (payload.channel === roomId) {
              appendMessage(messageData, true);
          }
      });
  
      socket.on('direct-message', (message) => {
          appendMessage(message, true);
      });
  
      // Gửi tin nhắn khi form được submit
      document.getElementById('chat-form').addEventListener('submit', function (e) {
          e.preventDefault();
          const message = document.getElementById('message').value.trim();
          if (!message) return;
  
          fetch('/send-message', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              },
              body: JSON.stringify({ message, room_id: roomId })
          })
          .then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  document.getElementById('message').value = '';
              }
          })
          .catch(error => console.error('Lỗi gửi tin nhắn:', error));
      });
  
      // Load tin nhắn khi vào trang
      loadOldMessages();
  });
  </script>
  
</html>

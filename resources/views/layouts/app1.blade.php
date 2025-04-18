<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đại Nông</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- jQuery UI CSS -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <!-- jQuery UI JS -->
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
  <link rel="stylesheet" href="css/app.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    df-messenger {
      /* Màu nền của thanh tiêu đề (titlebar) */
      --df-messenger-button-titlebar-color: #4CAF50; /* Xanh lá cây */
      /* Màu nền của cửa sổ chat */
      --df-messenger-chat-background: #f0f4f1; /* Nền nhẹ với tone xanh lá */
     
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




<!-- Dialogflow Messenger (ẩn mặc định) -->
<df-messenger id="chatbot-widget"
  intent="WELCOME"
  chat-title="DaiNong"
  agent-id="9a66aac8-4b63-4db7-bd6e-71df9cfb9e91"
  language-code="vi"
  style="position: fixed; bottom: 100px; right: 20px; z-index: 1051;">
</df-messenger>


<!-- Script Dialogflow Messenger -->
<script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>



  <!-- Modal Sản phẩm xem gần đây (hiệu ứng right fade) -->
  <div class="modal right fade" id="recentlyViewedModal" tabindex="-1" aria-labelledby="recentlyViewedModalLabel"
    aria-hidden="true">
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
          <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="img-fluid"
          style="width: 100px; height: 100px; object-fit: cover;">
        @else
        <img src="https://via.placeholder.com/100x100?text=No+Image" alt="No Image" class="img-fluid"
        style="width: 100px; height: 100px; object-fit: cover;">
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
    window.addEventListener('scroll', function () {
      const backToTopButton = document.getElementById('back-to-top');
      // Nếu cuộn xuống hơn 200px, thêm class 'show' vào nút Back to top
      if (window.scrollY > 200) {
        backToTopButton.classList.add('show');
      } else {
        backToTopButton.classList.remove('show');
      }
    });

    document.getElementById('back-to-top').addEventListener('click', function () {
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



      <form method="GET" action="{{ route('products.search') }}"
        class="d-flex justify-content-center position-absolute top-50 start-50 translate-middle w-100"
        style="max-width: 500px;">
        <input class="form-control me-2" type="search" name="query" id="live-search-input"
          placeholder="Tìm kiếm sản phẩm" aria-label="Search" style="max-width: 400px; min-width: 200px;">
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
      @else
        <div class="d-flex justify-content-end">
          <a href="{{ route('login') }}" class="btn btn-outline-light shadow-sm">
            <i class="fa-solid fa-sign-in-alt me-2"></i> Đăng nhập
          </a>
        </div>
      @endif
        @php
      if (auth()->check()) {
        $totalItems = \App\Models\Cart::where('user_id', auth()->id())->count();
      } else {
        $cart = session()->get('cart', []);
        $totalItems = count($cart);
      }
    @endphp

        <!-- Nút kích hoạt modal giỏ hàng -->
        <button type="button" class="btn btn-outline-light ms-3" data-bs-toggle="modal" data-bs-target="#cartModal"
          title="Giỏ hàng">
          <i class="fas fa-shopping-cart"></i>
          <span id="cartItemCount" class="badge bg-danger">{{ $totalItems }}</span>
        </button>
      </div>
    </div>
  </nav>

  <!-- Navbar dòng 2 -->
  <nav class="navbar navbar-expand-lg navbar-second">
    <div class="container">
      <ul class="navbar-nav" id="menu">
        <!-- Danh mục sản phẩm với dropdown -->
        <li class="nav-item position-relative">
          <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
            href="{{ route('categories.index') }}">
            <i class="fa-solid fa-list me-2"></i>Danh mục sản phẩm
            <i class="fas fa-chevron-down muiten ms-2"></i>
          </a>
          <ul class="metismenu">
            @foreach($categories as $category)
        <li class="d-flex align-items-center position-relative">
          <a href="{{ route('categories.show', $category->id) }}"
          class="flex-grow-1 d-flex justify-content-between align-items-center">
          {{ $category->category_name }}
          @if($category->hasSubCategories())
        <i class="fas fa-chevron-right ms-2"></i>
      @endif
          </a>
          @if($category->hasSubCategories())
        <ul class="submenu">
        @foreach($category->subCategories as $subCategory)
      <li>
      <a href="{{ route('categories.show', $subCategory->id) }}">

      {{ $subCategory->category_name }}
      </a>
      </li>
    @endforeach
        </ul>
      @endif
        </li>
        @if(!$loop->last)
      <hr class="my-2 w-100">
    @endif
      @endforeach
          </ul>
        </li>
        <!-- Menu "Sản phẩm" -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
            href="{{ route('products.index') }}">
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
    <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="d-flex">
      <div class="toast-body">{{ session('success') }}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
        aria-label="Close"></button>
      </div>
    </div>
    </div>
  @endif

  @if(session('error'))
    <div class="toast-container">
    <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="d-flex">
      <div class="toast-body">{{ session('error') }}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
        aria-label="Close"></button>
      </div>
    </div>
    </div>
  @endif

  <main>
    @yield('content')
  </main>
  @include('cart._cart_modal')
  <!-- Footer -->
  <!-- Đường gạch ngăn cách giữa nội dung và dòng chữ cuối -->
  <hr class="border-top border-secondary">
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

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/js/toast.js"></script>

<script>
  var routeUrl = "{{ route('live-search') }}"; // Khai báo biến routeUrl
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
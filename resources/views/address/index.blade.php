@extends('layouts.app')

@section('content')
   <!-- Banner & Breadcrumb -->
   <div class="banner-container position-relative mb-4">
    <img src="{{ asset('images/banner/organic-breadcrumb-1.jpg') }}" alt="Banner quảng cáo" class="banner-image w-100" style="height: 130px; object-fit: cover;">
    <div class="banner-overlay position-absolute top-50 start-50 translate-middle text-center">
        <h2 class="text-dark">Liên hệ với chúng tôi</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-dark">Trang chủ</a></li>
                
            </ol>
        </nav>
    </div>
</div>
<div class="container my-5">
  <div class="row">
    <!-- Cột bên trái: Map -->
    <div class="col-md-6 mb-4">
      <!-- Nhúng map từ Google Maps (sử dụng link embed) -->
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.340370965913!2d105.7598622!3d21.0470486!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313454c3ce577141%3A0xb1a1ac92701777bc!2sTr%C6%B0%E1%BB%9Dng%20%C4%90%E1%BA%A1i%20h%E1%BB%8Dc%20T%C3%A0i%20Nguy%C3%AAn%20v%C3%A0%20M%C3%B4i%20tr%C6%B0%E1%BB%9Dng%20H%C3%A0%20N%E1%BB%99i!5e0!3m2!1svi!2s!4v1670000000000!5m2!1svi!2s" 
        width="100%" 
        height="100%" 
        style="border:0;" 
        allowfullscreen="" 
        loading="lazy">
        
      </iframe>
    </div>
    <!-- Cột bên phải: Thông tin liên hệ và form -->
    <div class="col-md-6">
      
      <div class="mb-4">
        <h5>Địa chỉ</h5>
        <p>41A Đ. Phú Diễn, Phú Diễn, Bắc Từ Liêm, Hà Nội</p>
      </div>

      <div class="mb-4">
        <h5>Điện thoại</h5>
        <p>0123 456 789</p>
      </div>

      <div class="mb-4">
        <h5>Email</h5>
        <p>donhotu03.dev@gmail.com</p>
      </div>

      <div class="mb-4 ">
        <h5>Mạng xã hội</h5>
        <p>
          Nếu bạn có thắc mắc hoặc câu hỏi, hãy liên hệ với chúng tôi để biết thêm chi tiết. Chúng tôi hỗ trợ 24/7.
        </p>
        <p class="mt-4">
          <a href="#" class="me-2"><i class="fa-brands fa-facebook fa-beat fa-xl" style="color: #45c534;"></i></a>
          <a href="#" class="me-2"><i class="fab fa-twitter fa-beat fa-xl" style="color: #45c534;"></i></a>
          <a href="#"><i class="fab fa-instagram fa-beat fa-xl" style="color: #45c534;"></i></a>
        </p>
      </div>

      <h5 class="mb-3">Gửi liên hệ</h5>
      <form action="{{ route('contact.submit') }}" method="post">
        @csrf
        <div class="mb-3">
          <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
          <input type="text" name="name" id="name" class="form-control" placeholder="Nhập họ tên của bạn" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
          <input type="email" name="email" id="email" class="form-control" placeholder="Nhập email của bạn" required>
        </div>
        <div class="mb-3">
          <label for="message" class="form-label">Nội dung</label>
          <textarea name="message" id="message" rows="5" class="form-control" placeholder="Nhập nội dung liên hệ"></textarea>
        </div>
        <button type="submit" class="btn btn-success ">Gửi liên hệ</button>
      </form>
    </div>
  </div>
</div>

@endsection

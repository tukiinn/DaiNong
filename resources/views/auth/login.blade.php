@extends('layouts.app')

@section('title', 'Đăng Nhập/Đăng Ký')

@section('content')
@if ($errors->any())
    @foreach ($errors->all() as $error)
        <script>
            toastr.error("{{ $error }}");
        </script>
    @endforeach
@endif

<div class="auth-wrapper">  
  <div class="auth-container" id="authContainer">
    <!-- Form đăng ký -->
    <div class="auth-form-container auth-sign-up-container">
      <form action="{{ route('register') }}" method="POST">
        @csrf
        <h1>Tạo Tài Khoản</h1>
        <div class="auth-social-container">
          <a href="{{ route('auth.facebook') }}" class="auth-social"><i class="fab fa-facebook-f"></i></a>
          <a href="{{ route('auth.google') }}" class="auth-social"><i class="fab fa-google-plus-g"></i></a>
          <a href="#" class="auth-social"><i class="fab fa-linkedin-in"></i></a>
        </div>
        <span>hoặc sử dụng email của bạn để đăng ký</span>
        
        <!-- Các input đăng ký -->
        <div class="form-group">
          <input type="text" placeholder="Họ tên" name="name" required />
        </div>
        <div class="form-group">
          <input type="email" placeholder="Email" name="email" required />
        </div>
        <div class="form-group">
          <input type="password" placeholder="Mật khẩu" name="password" required />
        </div>
        <div class="form-group">
          <input type="password" placeholder="Xác nhận mật khẩu" name="password_confirmation" required />
        </div>
        <!-- Thêm reCAPTCHA -->
        <div class="form-group">
          {!! NoCaptcha::display() !!}
          @if ($errors->has('g-recaptcha-response'))
            <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
          @endif
        </div>
        <button type="submit">Đăng ký</button>
      </form>
    </div>

    <!-- Form đăng nhập -->
    <div class="auth-form-container auth-sign-in-container">
      <form action="{{ route('login') }}" method="POST">
        @csrf
        <h1>Đăng nhập</h1>
        <div class="auth-social-container">
          <a href="{{ route('auth.facebook') }}" class="auth-social"><i class="fab fa-facebook-f"></i></a>
          <a href="{{ route('auth.google') }}" class="auth-social"><i class="fab fa-google-plus-g"></i></a>
          <a href="#" class="auth-social"><i class="fab fa-linkedin-in"></i></a>
        </div>
        <span>hoặc sử dụng tài khoản của bạn</span>
        
        <!-- Các input đăng nhập -->
        <div class="form-group">
          <input type="email" placeholder="Email" name="email" required />
        </div>
        <div class="form-group">
          <input type="password" placeholder="Mật khẩu" name="password" required />
        </div>
        <!-- Thêm reCAPTCHA -->
        <div class="form-group">
          {!! NoCaptcha::display() !!}
          @if ($errors->has('g-recaptcha-response'))
            <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
          @endif
        </div>
        <a href="{{ url('/password/reset') }}">Quên mật khẩu?</a>
        <button type="submit">Đăng nhập</button>
      </form>
    </div>

    <!-- Overlay chuyển đổi giữa đăng nhập và đăng ký -->
    <div class="auth-overlay-container">
      <div class="auth-overlay">
        <div class="auth-overlay-panel auth-overlay-left">
          <h1>Chào mừng trở lại!</h1>
          <p>Để tiếp tục kết nối với chúng tôi, vui lòng đăng nhập bằng thông tin cá nhân của bạn</p>
          <button class="ghost" id="signIn">Đăng nhập</button>
        </div>
        <div class="auth-overlay-panel auth-overlay-right">
          <h1>Chào bạn!</h1>
          <p>Nhập thông tin cá nhân của bạn và bắt đầu hành trình cùng chúng tôi</p>
          <button class="ghost" id="signUp">Đăng ký</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bao gồm JS của reCAPTCHA -->
{!! NoCaptcha::renderJs() !!}


<style>
@import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

.error-message {
      color: red;
      font-size: 0.9em;
      margin-top: 5px;
      display: block;
    }
 

.auth-wrapper * {
    box-sizing: border-box;
}

.auth-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-family: 'Montserrat', sans-serif;
  
    padding: 20px 0 50px;

}

.auth-wrapper h1 {
    font-size: 30px;
    font-weight: bold;
    margin: 0;
}

.auth-wrapper h2 {
    text-align: center;
    font-weight: bold;  
}

.auth-wrapper p {
    font-size: 15px;
    font-weight: 100;
    line-height: 20px;
    letter-spacing: 0.5px;
    margin: 20px 0 30px;
}

.auth-wrapper span {
    font-size: 12px;
}

.auth-wrapper a {
    color: #333;
    font-size: 14px;
    text-decoration: none;
    margin: 15px 0;
}


.auth-wrapper button {
    border-radius: 20px;
    border: 1px solid #22e35f;
    background-color: #22e35f;
    color: #2D2D2D;
    font-size: 12px;
    font-weight: bold;
    padding: 12px 45px;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: transform 80ms ease-in;
}

.auth-wrapper button:active {
    transform: scale(0.95);
}

.auth-wrapper button:focus {
    outline: none;
}


.auth-wrapper button.ghost {
    background-color: transparent;
    border-color: #2D2D2D;
}

.auth-wrapper form {
    background-color: #FFFFFF;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0 50px;
    height: 100%;
    text-align: center;
}

.auth-wrapper input {
    background-color: #eee;
    border: none;
    padding: 12px 15px;
    margin: 8px 0;
    width: 100%;
}

.auth-container {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25),
                0 10px 10px rgba(0, 0, 0, 0.22);
    position: relative;
    overflow: hidden;
    width: 768px;
    max-width: 100%;
    min-height: 560px;
}

.auth-form-container {
    position: absolute;
    top: 0;
    height: 100%;
    transition: all 0.6s ease-in-out;
}

/* Đăng nhập */
.auth-sign-in-container {
    left: 0;
    width: 50%;
    z-index: 2;
}

.auth-container.right-panel-active .auth-sign-in-container {
    transform: translateX(100%);
}

/* Đăng ký */
.auth-sign-up-container {
    left: 0;
    width: 50%;
    opacity: 0;
    z-index: 1;
}

.auth-container.right-panel-active .auth-sign-up-container {
    transform: translateX(100%);
    opacity: 1;
    z-index: 5;
    animation: show 0.6s;
}

@keyframes show {
    0%, 49.99% {
        opacity: 0;
        z-index: 1;
    }
    50%, 100% {
        opacity: 1;
        z-index: 5;
    }
}

/* Overlay */
.auth-overlay-container {
    position: absolute;
    top: 0;
    left: 50%;
    width: 50%;
    height: 100%;
    overflow: hidden;
    transition: transform 0.6s ease-in-out;
    z-index: 100;
}

.auth-container.right-panel-active .auth-overlay-container {
    transform: translateX(-100%);
}

/* Sửa gradient: sử dụng hai sắc xanh lá làm màu chủ đạo và thay đổi màu chữ trong overlay */
.auth-overlay {
    background: #22e35f; /* Màu dự phòng */
    background: -webkit-linear-gradient(to right, #22e35f, #2ecc71);
    background: linear-gradient(to right, #22e35f, #2ecc71);
    background-repeat: no-repeat;
    background-size: cover;
    background-position: 0 0;
    /* Thay màu chữ từ trắng (#FFFFFF) thành #401D83 */
    color: #2D2D2D;
    position: relative;
    left: -100%;
    height: 100%;
    width: 200%;
    transform: translateX(0);
    transition: transform 0.6s ease-in-out;
}

.auth-container.right-panel-active .auth-overlay {
    transform: translateX(50%);
}

.auth-overlay-panel {
    position: absolute;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0 40px;
    text-align: center;
    top: 0;
    height: 100%;
    width: 50%;
    transform: translateX(0);
    transition: transform 0.6s ease-in-out;
}

.auth-overlay-left {
    transform: translateX(-20%);
}

.auth-container.right-panel-active .auth-overlay-left {
    transform: translateX(0);
}

.auth-overlay-right {
    right: 0;
    transform: translateX(0);
}

.auth-container.right-panel-active .auth-overlay-right {
    transform: translateX(20%);
}

.auth-social-container {
    margin: 20px 0;
}

.auth-social-container a {
    border: 1px solid #DDDDDD;
    border-radius: 50%;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    margin: 0 5px;
    height: 40px;
    width: 40px;
}

</style>
<script>
    // Hàm hiển thị thông báo lỗi dưới input
    function showError(input, message) {
      let errorElement = input.parentNode.querySelector('.error-message');
      if (!errorElement) {
        errorElement = document.createElement('span');
        errorElement.className = 'error-message';
        input.parentNode.appendChild(errorElement);
      }
      errorElement.textContent = message;
    }

    // Hàm xóa thông báo lỗi của input
    function clearError(input) {
      let errorElement = input.parentNode.querySelector('.error-message');
      if (errorElement) {
        errorElement.textContent = '';
      }
    }

    // Validate form đăng ký
    const registerForm = document.querySelector('.auth-sign-up-container form');
    if (registerForm) {
      registerForm.addEventListener('submit', function(e) {
        let valid = true;
        // Lấy các input
        const nameInput = registerForm.querySelector("input[name='name']");
        const emailInput = registerForm.querySelector("input[name='email']");
        const passwordInput = registerForm.querySelector("input[name='password']");
        const passwordConfirmInput = registerForm.querySelector("input[name='password_confirmation']");

        // Xóa thông báo lỗi cũ
        [nameInput, emailInput, passwordInput, passwordConfirmInput].forEach(input => clearError(input));

        // Validate tên
        if (nameInput.value.trim() === '') {
          showError(nameInput, "Vui lòng nhập tên của bạn.");
          valid = false;
        }
        
        // Validate email
        if (emailInput.value.trim() === '') {
          showError(emailInput, "Vui lòng nhập địa chỉ email.");
          valid = false;
        } else {
          const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailPattern.test(emailInput.value.trim())) {
            showError(emailInput, "Email không hợp lệ.");
            valid = false;
          }
        }
        
        // Validate mật khẩu
        if (passwordInput.value === '') {
          showError(passwordInput, "Vui lòng nhập mật khẩu.");
          valid = false;
        } else if (passwordInput.value.length < 8) {
          showError(passwordInput, "Mật khẩu phải có ít nhất 8 ký tự.");
          valid = false;
        }
        
        // Validate xác nhận mật khẩu
        if (passwordConfirmInput.value === '') {
          showError(passwordConfirmInput, "Vui lòng nhập mật khẩu xác nhận.");
          valid = false;
        } else if (passwordInput.value !== passwordConfirmInput.value) {
          showError(passwordConfirmInput, "Mật khẩu xác nhận không khớp.");
          valid = false;
        }
        
        if (!valid) {
          e.preventDefault();
        }
      });
    }

    // Validate form đăng nhập
    const loginForm = document.querySelector('.auth-sign-in-container form');
    if (loginForm) {
      loginForm.addEventListener('submit', function(e) {
        let valid = true;
        const emailInput = loginForm.querySelector("input[name='email']");
        const passwordInput = loginForm.querySelector("input[name='password']");

        // Xóa thông báo lỗi cũ
        [emailInput, passwordInput].forEach(input => clearError(input));

        // Validate email
        if (emailInput.value.trim() === '') {
          showError(emailInput, "Vui lòng nhập địa chỉ email.");
          valid = false;
        } else {
          const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailPattern.test(emailInput.value.trim())) {
            showError(emailInput, "Email không hợp lệ.");
            valid = false;
          }
        }
        
        // Validate mật khẩu
        if (passwordInput.value === '') {
          showError(passwordInput, "Vui lòng nhập mật khẩu.");
          valid = false;
        }
        
        if (!valid) {
          e.preventDefault();
        }
      });
    }
  </script>

<script>
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('authContainer');

    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });
</script>
@endsection

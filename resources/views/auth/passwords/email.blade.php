@extends('layouts.app')

@section('title', 'Quên Mật Khẩu')

@section('content')
<div class="auth-wrapper">
    <div class="auth-container my-3" id="authContainer">
        <!-- Panel form: sử dụng phần bên trái (sign-in container) -->
        <div class="auth-form-container auth-sign-in-container">
            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <h1>Quên Mật Khẩu</h1>
                <input type="email" name="email" placeholder="Nhập email của bạn" required />
                
                <!-- Thêm widget reCAPTCHA -->
                <div class="form-group">
                    {!! NoCaptcha::display() !!}
                    @if ($errors->has('g-recaptcha-response'))
                        <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                    @endif
                </div>
                
                <button type="submit">Gửi liên kết</button>
            </form>
        </div>
        <!-- Overlay hiển thị thông tin bổ trợ -->
        <div class="auth-overlay-container">
            <div class="auth-overlay">
                <div class="auth-overlay-panel auth-overlay-right">
                    <h1>Quên mật khẩu?</h1>
                    <p>Nhập email của bạn và chúng tôi sẽ gửi liên kết đặt lại mật khẩu.</p>
                    <a href="{{ route('login') }}" class="ghost">Quay lại đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bao gồm JS của reCAPTCHA (có thể đặt ở cuối trang hoặc trong layout chính) -->
{!! NoCaptcha::renderJs() !!}


<!-- CSS tùy chỉnh -->
<style>
    @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

    /* Global wrapper */
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
        font-size: 28px;
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
    /* Style cho button */
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
        cursor: pointer;
        margin-top: 10px;
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
    /* Ghost style cho anchor */
    .auth-wrapper a.ghost {
        display: inline-block;
        border-radius: 20px;
        border: 1px solid #2D2D2D;
        padding: 12px 45px;
        text-transform: uppercase;
        font-weight: bold;
        text-decoration: none;
        color: #2D2D2D;
        transition: transform 80ms ease-in;
        cursor: pointer;
        background-color: transparent;
    }
    .auth-wrapper a.ghost:active {
        transform: scale(0.95);
    }
    /* Style cho form và input */
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
        border-radius: 20px;
        font-size: 14px;
    }
    /* Container chính */
    .auth-container {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25),
                    0 10px 10px rgba(0, 0, 0, 0.22);
        position: relative;
        overflow: hidden;
        width: 768px;
        max-width: 100%;
        min-height: 480px;
        margin: auto;
    }
    .auth-form-container {
        position: absolute;
        top: 0;
        height: 100%;
        transition: all 0.6s ease-in-out;
        padding: 50px;
    }
    /* Form sign-in cho Quên mật khẩu */
    .auth-sign-in-container {
        left: 0;
        width: 50%;
        z-index: 2;
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
    .auth-overlay {
        background: #22e35f;
        background: -webkit-linear-gradient(to right, #22e35f, #2ecc71);
        background: linear-gradient(to right, #22e35f, #2ecc71);
        background-repeat: no-repeat;
        background-size: cover;
        background-position: 0 0;
        color: #2D2D2D;
        position: relative;
        left: -100%;
        height: 100%;
        width: 200%;
        transform: translateX(0);
        transition: transform 0.6s ease-in-out;
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
    .auth-overlay-right {
        right: 0;
        transform: translateX(0);
    }
    /* Responsive */
    @media (max-width: 768px) {
        .auth-container {
            width: 100%;
            min-height: 600px;
        }
        .auth-form-container,
        .auth-overlay-container {
            width: 100%;
            left: 0;
        }
        .auth-overlay {
            display: none;
        }
    }
</style>
@endsection

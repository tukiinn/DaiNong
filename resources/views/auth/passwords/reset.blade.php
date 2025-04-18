@extends('layouts.app')

@section('title', 'Đặt Lại Mật Khẩu')

@section('content')
<div class="auth-reset-wrapper">
    <div class="auth-reset-container">
        <!-- Form container (bên trái) -->
        <div class="auth-reset-form-container">
            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <h1>Đặt Lại Mật Khẩu</h1>
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="email" name="email" placeholder="Nhập email của bạn" value="{{ $email }}" required />
                <input type="password" name="password" placeholder="Nhập mật khẩu mới" required />
                <input type="password" name="password_confirmation" placeholder="Xác nhận mật khẩu" required />
                <button type="submit">Đặt lại mật khẩu</button>
            </form>
        </div>
        <!-- Overlay container (bên phải) -->
        <div class="auth-reset-overlay-container">
            <div class="auth-reset-overlay">
                <h1>Đã nhớ mật khẩu?</h1>
                <br>
                <p>Nếu bạn đã nhớ mật khẩu, hãy quay lại đăng nhập.</p>
                <a href="{{ route('login') }}" class="auth-reset-ghost">Đăng nhập</a>
            </div>
        </div>
    </div>
</div>

<!-- Full CSS với các class có tiền tố riêng -->
<style>
    @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

    /* Global reset & wrapper */
    * {
        box-sizing: border-box;
    }
   
    .auth-reset-wrapper {
        width: 768px;
        max-width: 100%;
        margin: auto;
        box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
        border-radius: 10px;
        overflow: hidden;
        display: flex;
    }
    .auth-reset-container {
        display: flex;
        width: 100%;
        height: 480px;
    }
    .auth-reset-form-container {
        width: 50%;
        padding: 50px;
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .auth-reset-form-container h1 {
        margin-bottom: 25px;
        font-size: 30px;
        font-weight: bold;
        margin: 0;
    }
    .auth-reset-form-container form {
        width: 100%;
        text-align: center;
    }
    .auth-reset-form-container input {
        background-color: #eee;
        border: none;
        padding: 12px 15px;
        margin: 8px 0;
        width: 100%;
        border-radius: 20px;
        font-size: 14px;
    }
    .auth-reset-form-container button {
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
        margin-top: 20px;
    }
    .auth-reset-form-container button:active {
        transform: scale(0.95);
    }
    /* Overlay container */
    .auth-reset-overlay-container {
        width: 50%;
        background: linear-gradient(to right, #22e35f, #2ecc71);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 50px;
    }
    .auth-reset-overlay {
        text-align: center;
        color: #2D2D2D;
    }
    .auth-reset-overlay h1 {
        margin-bottom: 20px;
        font-size: 30px;
        font-weight: bold;
        margin: 0;
    }
    .auth-reset-overlay p {
        margin-bottom: 20px;
        font-size: 15px;
    }
    /* Ghost button cho anchor */
    .auth-reset-ghost {
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
    .auth-reset-ghost:active {
        transform: scale(0.95);
    }
    /* Responsive */
    @media (max-width: 768px) {
        .auth-reset-wrapper {
            flex-direction: column;
            width: 100%;
        }
        .auth-reset-container {
            flex-direction: column;
            height: auto;
        }
        .auth-reset-form-container,
        .auth-reset-overlay-container {
            width: 100%;
        }
    }
</style>
@endsection

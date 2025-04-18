<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;

class AuthController extends Controller
{
    
    // Xử lý đăng ký người dùng (tích hợp reCAPTCHA)
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users',
            'password'              => 'required|string|min:8|confirmed',
            'g-recaptcha-response'  => 'required|captcha',
        ], [
            'name.required'                   => 'Vui lòng nhập tên của bạn.',
            'email.required'                  => 'Vui lòng nhập địa chỉ email.',
            'email.email'                     => 'Email không hợp lệ.',
            'email.unique'                    => 'Email này đã được đăng ký.',
            'password.required'               => 'Vui lòng nhập mật khẩu.',
            'password.min'                    => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed'              => 'Mật khẩu xác nhận không khớp.',
            'g-recaptcha-response.required'   => 'Vui lòng xác nhận reCAPTCHA.',
            'g-recaptcha-response.captcha'    => 'Xác nhận reCAPTCHA không hợp lệ, vui lòng thử lại.',
        ]);

        // Tạo user mới và tặng 1 lượt quay cho người dùng mới
        $user = User::create([
            'name'            => $validatedData['name'],
            'email'           => $validatedData['email'],
            'password'        => Hash::make($validatedData['password']),
            'role'            => 'user',
            'remaining_spins' => 1,
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Đăng ký thành công! Chào mừng bạn! Bạn được tặng 1 lượt quay.');
    }

    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    // Xử lý đăng nhập người dùng (tích hợp reCAPTCHA)
    public function login(Request $request)
    {
       // Validate bao gồm cả reCAPTCHA
$request->validate([
    'email'                => 'required|string|email',
    'password'             => 'required|string',
    'g-recaptcha-response' => 'required|captcha',
], [
    'email.required'                => 'Vui lòng nhập địa chỉ email.',
    'email.email'                   => 'Email không hợp lệ.',
    'password.required'             => 'Vui lòng nhập mật khẩu.',
    'g-recaptcha-response.required' => 'Vui lòng xác nhận reCAPTCHA.',
    'g-recaptcha-response.captcha'  => 'Xác nhận reCAPTCHA không hợp lệ, vui lòng thử lại.',
]);

// Lấy thông tin đăng nhập chỉ gồm email và password
$credentials = $request->only('email', 'password');

if (Auth::attempt($credentials)) {
    $request->session()->regenerate();

    return Auth::user()->role === 'admin'
        ? redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công!')
        : redirect()->route('home')->with('success', 'Đăng nhập thành công!');
}

// Nếu đăng nhập không thành công, trả về thông báo lỗi
return back()->with('error', 'Email hoặc mật khẩu không đúng.')->withInput($request->only('email'));

    }

    // Xử lý đăng xuất người dùng
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Đăng xuất thành công!');
    }

    // Hiển thị form yêu cầu liên kết đặt lại mật khẩu
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        // Xác thực email
        $request->validate(['email' => 'required|email']);

        // Gửi email nếu người dùng tồn tại
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $token = Password::createToken($user);
            $user->notify(new ResetPasswordNotification($token));
        }

        return back()->with('status', 'Đã gửi liên kết đặt lại mật khẩu đến email của bạn.');
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.passwords.reset')->with(['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        // Xác thực và đặt lại mật khẩu
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token'    => 'required',
        ]);

        $response = Password::reset($request->only('email', 'password', 'token'), function (User $user, $password) {
            $user->password = Hash::make($password);
            $user->save();
            Auth::login($user);
        });

        return $response == Password::PASSWORD_RESET
            ? redirect()->route('home')->with('success', 'Mật khẩu đã được đặt lại thành công!')
            : back()->with('error', 'Không thể đặt lại mật khẩu. Vui lòng thử lại.');
    }
}

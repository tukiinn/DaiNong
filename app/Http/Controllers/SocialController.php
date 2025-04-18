<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SocialController extends Controller
{
        public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->id)->first();

            if (!$user) {
                $user = User::where('email', $googleUser->email)->first();

                if ($user) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            }

            if (!$user) {
                $user = User::create([
                    'name'      => $googleUser->name,
                    'email'     => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'role'      => 'user',
                    'password'  => Hash::make('toideptraiqua'),
                    'remaining_spins' => 1,
                ]);
            }

            Auth::login($user);

            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công!')
                : redirect()->route('home')->with('success', 'Đăng nhập thành công!');
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Lỗi đăng nhập Google: ' . $e->getMessage());
        }
    }

    // === FACEBOOK ===
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $fbUser = Socialite::driver('facebook')->stateless()->user();

            $user = User::where('facebook_id', $fbUser->id)->first();

            if (!$user) {
                $user = User::where('email', $fbUser->email)->first();

                if ($user) {
                    $user->update(['facebook_id' => $fbUser->id]);
                }
            }

            if (!$user) {
                $user = User::create([
                    'name'        => $fbUser->name,
                    'email'       => $fbUser->email,
                    'facebook_id' => $fbUser->id,
                    'role'        => 'user',
                    'password'    => Hash::make('toideptraiqua'),
                    'remaining_spins' => 1,
                ]);
            }

            Auth::login($user);

            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công!')
                : redirect()->route('home')->with('success', 'Đăng nhập thành công!');
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Lỗi đăng nhập Facebook: ' . $e->getMessage());
        }
    }
}

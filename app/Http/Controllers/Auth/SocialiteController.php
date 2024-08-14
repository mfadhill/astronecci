<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $socialUser = Socialite::driver('google')->stateless()->user();

        // Cari pengguna berdasarkan Google ID
        $registeredUser = User::where('google_id', $socialUser->id)->first();

        if ($registeredUser) {
            // Jika pengguna sudah ada, login langsung
            $user = $registeredUser;
        } else {
            // Jika pengguna belum ada, buat pengguna baru
            $user = User::create([
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'password' => Hash::make('password'), // Password dummy, tidak digunakan
                'google_id' => $socialUser->id,
                'google_token' => $socialUser->token,
                'google_refresh_token' => $socialUser->refreshToken,
            ]);
        }

        // Login pengguna
        Auth::login($user);

        return redirect('/dashboard');
    }
}

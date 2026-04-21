<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DefaultCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('Auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $data = [
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ];

        $user = User::create($data);

        // Copy kategori default untuk user baru (template dari admin)
        $defaultCategories = DefaultCategory::query()->get(['nama_kategori', 'ikon', 'warna']);
        foreach ($defaultCategories as $dc) {
            Category::query()->create([
                'user_id' => $user->id,
                'nama_kategori' => $dc->nama_kategori,
                'ikon' => $dc->ikon,
                'warna' => $dc->warna,
                'saldo' => 0,
            ]);
        }

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function showLogin()
    {
        return view('Auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard')->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }
}

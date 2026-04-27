<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    // 🔹 FORM LOGIN
    public function showLogin()
    {
        return view('auth.login');
    }

    // 🔹 PROSES LOGIN
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {

            // 🔥 CEK VERIFIKASI EMAIL
            if (!Auth::user()->hasVerifiedEmail()) {

                Auth::logout();

                return redirect()->route('verification.notice')
                    ->with('error', 'Silakan verifikasi email terlebih dahulu');
            }

            $request->session()->regenerate();

            return redirect()->route('piutang.index');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->withInput();
    }

    // 🔹 FORM REGISTER
    public function showRegister()
    {
        return view('auth.register');
    }

    // 🔹 PROSES REGISTER (FIXED)
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'jabatan' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        // ✅ SIMPAN USER (PASSWORD DI-HASH)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'jabatan' => $request->jabatan,
            'divisi' => 'Keuangan',
            'password' => Hash::make($request->password),
        ]);

        // ✅ KIRIM EMAIL VERIFIKASI
        event(new Registered($user));

        // ❌ JANGAN LOGIN DULU (hindari 419 & bug session)

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Silakan cek email untuk verifikasi.');
    }

    // 🔹 LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

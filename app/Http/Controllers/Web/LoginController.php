<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Buat nanti di resources/views/auth/login.blade.php
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            if (Auth::user()->role !== 'admin') {

                Auth::logout();
                return back()->withErrors(['email' => 'Akses hanya untuk admin']);
            }

            return redirect()->intended('/admin/destinasi');
        }

        return back()->withErrors(['email' => 'Login gagal']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}

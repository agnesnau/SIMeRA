<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller {
    public function showLoginForm() { return view('auth.login'); }
    public function login(Request $request) {
        if (Auth::attempt($request->only('username', 'password'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }
        return back()->withErrors(['username' => 'Login Gagal.']);
    }
    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
    }
}